<?php

namespace App\Services\SePay;

use App\Events\EnrollmentActivated;
use App\Models\Enrollment;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\EnrollmentClassSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SepayWebhookPaymentService
{
    /**
     * @var \App\Repositories\OrderRepository
     */
    protected OrderRepository $orderRepository;
    /**
     * @var \App\Services\EnrollmentClassSyncService
     */
    protected EnrollmentClassSyncService $enrollmentClassSyncService;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\OrderRepository  $orderRepository
     * @param  \App\Services\EnrollmentClassSyncService  $enrollmentClassSyncService
     */
    public function __construct(
        OrderRepository $orderRepository,
        EnrollmentClassSyncService $enrollmentClassSyncService
    )
    {
        $this->orderRepository = $orderRepository;
        $this->enrollmentClassSyncService = $enrollmentClassSyncService;
    }

    /**
     * Validate SePay API key header when configured.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function assertValidApiKey(Request $request): void
    {
        $expected = config('sepay.webhook.api_key');
        if ($expected === null || $expected === '') {
            return;
        }

        $authorization = (string) $request->header('Authorization', '');
        $needle = 'Apikey ' . $expected;

        if (!hash_equals($needle, $authorization)) {
            abort(401, 'Invalid webhook authorization.');
        }
    }

    /**
     * Process SePay JSON webhook body (idempotent by SePay transaction id).
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool}
     */
    public function processPayload(array $payload): array
    {
        $normalized = $this->normalizePayload($payload);
        $transactionId = (int) ($normalized['id'] ?? 0);
        if ($transactionId <= 0) {
            Log::warning('SePay webhook missing transaction id', [
                'payload_keys' => array_keys($payload),
            ]);
            return ['success' => true];
        }

        $transferType = strtolower((string) ($normalized['transferType'] ?? ''));
        if ($transferType !== '' && $transferType !== 'in') {
            Log::info('SePay webhook ignored (non incoming transfer)', [
                'sepay_transaction_id' => $transactionId,
                'transfer_type' => $transferType,
            ]);
            return ['success' => true];
        }

        $reference = $this->resolvePaymentReference($normalized);
        if ($reference === null) {
            Log::warning('SePay webhook missing ORDER reference', [
                'sepay_transaction_id' => $transactionId,
            ]);
            return ['success' => true];
        }

        try {
            DB::transaction(function () use ($normalized, $reference, $transactionId) {
                $inserted = DB::table('sepay_webhook_events')->insertOrIgnore([
                    'sepay_transaction_id' => $transactionId,
                    'order_id' => null,
                    'payload' => json_encode($normalized),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($inserted === 0) {
                    return;
                }

                $order = Order::query()
                    ->with('items')
                    ->where('payment_reference', $reference)
                    ->lockForUpdate()
                    ->first();

                DB::table('sepay_webhook_events')
                    ->where('sepay_transaction_id', $transactionId)
                    ->update(['order_id' => $order ? $order->id : null]);

                if (!$order) {
                    return;
                }

                $transferAmount = $this->resolveTransferAmount($normalized);
                $expectedMinor = (int) floor((float) $order->total_amount);

                if ($transferAmount < $expectedMinor) {
                    Log::warning('SePay webhook amount too low', [
                        'order_id' => $order->id,
                        'expected' => $expectedMinor,
                        'received' => $transferAmount,
                    ]);

                    return;
                }

                if ($order->status === 'paid') {
                    return;
                }

                $this->orderRepository->markPaidIfPending($order);
                $order->refresh();

                foreach ($order->items as $item) {
                    $enrollment = Enrollment::query()->firstOrNew([
                        'course_id' => $item->course_id,
                        'user_id' => $order->user_id,
                    ]);

                    $enrollment->status = 'active';
                    if (!$enrollment->enrolled_at) {
                        $enrollment->enrolled_at = now();
                    }
                    $enrollment->save();

                    $this->enrollmentClassSyncService->syncClassAssignmentsForEnrollment($enrollment);
                    event(new EnrollmentActivated((int) $enrollment->id));
                }
            });
        } catch (Throwable $e) {
            Log::error('SePay webhook processing failed', [
                'sepay_transaction_id' => $transactionId,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }

        return ['success' => true];
    }

    /**
     * Resolve ORDER_{id} reference from SePay code or transfer content.
     *
     * @param  array<string, mixed>  $payload
     * @return string|null
     */
    protected function resolvePaymentReference(array $payload): ?string
    {
        $code = $payload['code'] ?? null;
        if (is_string($code) && preg_match('/ORDER[_-]?(\d+)/i', $code, $matches)) {
            return 'ORDER_' . $matches[1];
        }

        $fields = [
            $payload['content'] ?? null,
            $payload['description'] ?? null,
            $payload['transferContent'] ?? null,
            $payload['transfer_content'] ?? null,
        ];

        foreach ($fields as $value) {
            if (!is_string($value)) {
                continue;
            }

            if (preg_match('/ORDER[_-]?(\d+)/i', $value, $matches)) {
                return 'ORDER_' . $matches[1];
            }
        }

        return null;
    }

    /**
     * Normalize SePay payload shape from JSON/form-data wrappers.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function normalizePayload(array $payload): array
    {
        if (isset($payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        if (isset($payload['data']) && is_string($payload['data'])) {
            $decoded = json_decode($payload['data'], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $payload;
    }

    /**
     * Resolve transfer amount from multiple payload field names.
     *
     * @param  array<string, mixed>  $payload
     * @return int
     */
    protected function resolveTransferAmount(array $payload): int
    {
        $candidates = [
            $payload['transferAmount'] ?? null,
            $payload['transfer_amount'] ?? null,
            $payload['amount'] ?? null,
        ];

        foreach ($candidates as $value) {
            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return 0;
    }
}
