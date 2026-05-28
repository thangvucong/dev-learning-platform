<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Repositories\OrderRepository;

class CheckoutOrderService
{
    /**
     * @var \App\Repositories\OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\OrderRepository  $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Build OnePay card checkout metadata for frontend (order is created on start endpoint).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @param  array<string, mixed>  $checkout
     * @return array<string, mixed>|null
     */
    public function buildOnepayCheckoutSession(User $user, Course $course, array $checkout): ?array
    {
        if (!config('onepay.merchant') || !config('onepay.access_code') || !config('onepay.hash_key')) {
            return null;
        }

        if (!empty($checkout['is_free']) || (int) $checkout['amount_vnd'] <= 0) {
            return null;
        }

        $mode = (string) config('onepay.card_mode', 'both');
        $availableMethods = [];

        if (in_array($mode, ['both', 'international'], true)) {
            $availableMethods[] = [
                'method' => 'onepay_int_card',
                'label' => 'Thẻ quốc tế (Visa/Master/JCB)',
            ];
        }

        if (in_array($mode, ['both', 'domestic'], true)) {
            $availableMethods[] = [
                'method' => 'onepay_dom_card',
                'label' => 'Thẻ ATM nội địa',
            ];
        }

        if ($availableMethods === []) {
            return null;
        }

        return [
            'start_url' => route('payment.onepay.start', [], false),
            'course_id' => $course->id,
            'amount_vnd' => (int) $checkout['amount_vnd'],
            'available_methods' => $availableMethods,
        ];
    }

}
