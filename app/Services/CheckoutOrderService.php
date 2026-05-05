<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Services\SePay\SePayQrService;

class CheckoutOrderService
{
    /**
     * @var \App\Repositories\OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * @var \App\Services\SePay\SePayQrService
     */
    protected SePayQrService $sePayQrService;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\OrderRepository  $orderRepository
     * @param  \App\Services\SePay\SePayQrService  $sePayQrService
     */
    public function __construct(OrderRepository $orderRepository, SePayQrService $sePayQrService)
    {
        $this->orderRepository = $orderRepository;
        $this->sePayQrService = $sePayQrService;
    }

    /**
     * Build SePay QR session for an authenticated checkout (pending order + QR URL).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @param  array<string, mixed>  $checkout  Output of CheckoutService presentation
     * @return array<string, mixed>|null
     */
    public function buildSepayCheckoutSession(User $user, Course $course, array $checkout): ?array
    {
        if (!empty($checkout['is_free'])) {
            return null;
        }

        if ((int) $checkout['amount_vnd'] <= 0) {
            return null;
        }

        if (!$this->isSepayQrConfigured()) {
            return null;
        }

        $amountVnd = (int) $checkout['amount_vnd'];
        $saleAmount = (float) $checkout['sale_amount'];

        $pending = $this->orderRepository->findPendingSepayOrderForCourse($user->id, $course->id);
        $order = null;

        if ($pending && (int) round((float) $pending->total_amount) === $amountVnd) {
            $order = $pending;
        }

        if ($order === null) {
            $order = $this->orderRepository->createPendingSepayOrderWithCourse(
                $user->id,
                $saleAmount,
                $course->id,
                $saleAmount
            );
        }

        $transferContent = (string) $order->payment_reference;
        $accountNumber = (string) config('sepay.qr.account_number');
        $bankCode = (string) config('sepay.qr.bank_code');

        $qrImageUrl = $this->sePayQrService->buildQrImageUrl(
            $accountNumber,
            $bankCode,
            $amountVnd,
            $transferContent
        );

        return [
            'order_id' => $order->id,
            'qr_image_url' => $qrImageUrl,
            'transfer_content' => $transferContent,
            'amount_vnd' => $amountVnd,
            'amount_formatted' => format_price((float) $amountVnd, $checkout['currency_symbol']),
            'bank_name' => (string) config('sepay.qr.bank_display_name'),
            'account_number' => $accountNumber,
            'account_name' => (string) config('sepay.qr.account_name'),
            'poll_status_url' => route('orders.status', ['order' => $order->id], false),
            'success_url' => route('checkout.success', ['order' => $order->id], false),
        ];
    }

    /**
     * Whether env/config has minimum fields to render a VietQR image URL.
     *
     * @return bool
     */
    protected function isSepayQrConfigured(): bool
    {
        $bank = config('sepay.qr.bank_code');
        $acc = config('sepay.qr.account_number');

        return is_string($bank) && $bank !== '' && is_string($acc) && $acc !== '';
    }
}
