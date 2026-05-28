<?php

namespace App\Services\OnePay;

use App\Events\EnrollmentActivated;
use App\Models\Enrollment;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\EnrollmentClassSyncService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnePayPaymentService
{
    /**
     * @var \App\Services\OnePay\OnePayGatewayService
     */
    protected OnePayGatewayService $gatewayService;

    /**
     * @var \App\Repositories\OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * @var \App\Services\EnrollmentClassSyncService
     */
    protected EnrollmentClassSyncService $enrollmentClassSyncService;

    /**
     * @param  \App\Services\OnePay\OnePayGatewayService  $gatewayService
     * @param  \App\Repositories\OrderRepository  $orderRepository
     * @param  \App\Services\EnrollmentClassSyncService  $enrollmentClassSyncService
     */
    public function __construct(
        OnePayGatewayService $gatewayService,
        OrderRepository $orderRepository,
        EnrollmentClassSyncService $enrollmentClassSyncService
    ) {
        $this->gatewayService = $gatewayService;
        $this->orderRepository = $orderRepository;
        $this->enrollmentClassSyncService = $enrollmentClassSyncService;
    }

    /**
     * Handle browser return payload from OnePay.
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, order: \App\Models\Order|null, message: string, code: string}
     */
    public function handleReturnPayload(array $payload): array
    {
        return $this->processPayload($payload, 'return');
    }

    /**
     * Handle IPN payload from OnePay.
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, order: \App\Models\Order|null, message: string, code: string}
     */
    public function handleIpnPayload(array $payload): array
    {
        return $this->processPayload($payload, 'ipn');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  string  $source
     * @return array{success: bool, order: \App\Models\Order|null, message: string, code: string}
     */
    protected function processPayload(array $payload, string $source): array
    {
        $merchantTxnRef = (string) ($payload['vpc_MerchTxnRef'] ?? '');
        if ($merchantTxnRef === '') {
            return [
                'success' => false,
                'order' => null,
                'message' => 'Missing merchant transaction reference.',
                'code' => 'INVALID_REFERENCE',
            ];
        }

        $order = $this->orderRepository->findByPaymentReference($merchantTxnRef);
        if (!$order) {
            return [
                'success' => false,
                'order' => null,
                'message' => 'Order not found.',
                'code' => 'ORDER_NOT_FOUND',
            ];
        }

        $result = $source === 'ipn'
            ? $this->gatewayService->notification((string) $order->payment_method, $payload)
            : $this->gatewayService->completePurchase((string) $order->payment_method, $payload);

        Log::info('OnePay callback received', [
            'source' => $source,
            'order_id' => $order->id,
            'merchant_txn_ref' => $merchantTxnRef,
            'code' => $result['code'],
            'is_successful' => $result['is_successful'],
        ]);

        if (!$result['is_successful']) {
            $this->markFailedIfPending($order);

            return [
                'success' => false,
                'order' => $order->fresh(),
                'message' => (string) ($result['message'] ?: 'OnePay payment failed.'),
                'code' => (string) $result['code'],
            ];
        }

        DB::transaction(function () use ($order) {
            $order->refresh();
            $this->orderRepository->markPaidIfPending($order);
            $order->refresh();
            $this->activateEnrollments($order);
        });

        return [
            'success' => true,
            'order' => $order->fresh(['items']),
            'message' => 'OnePay payment successful.',
            'code' => (string) $result['code'],
        ];
    }

    /**
     * @param  \App\Models\Order  $order
     * @return void
     */
    protected function markFailedIfPending(Order $order): void
    {
        if ($order->status !== 'pending') {
            return;
        }

        $order->forceFill([
            'status' => 'failed',
        ])->save();
    }

    /**
     * Activate enrollments for each paid order item.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    protected function activateEnrollments(Order $order): void
    {
        $order->loadMissing('items');

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
    }
}
