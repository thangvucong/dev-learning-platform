<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    /**
     * Check whether user already has a paid order for course.
     *
     * @param  int  $userId
     * @param  int  $courseId
     * @return bool
     */
    public function hasPaidOrderForCourse(int $userId, int $courseId): bool
    {
        return Order::query()
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->whereHas('items', function ($sub) use ($courseId) {
                $sub->where('course_id', $courseId);
            })
            ->exists();
    }

    /**
     * Find a pending SePay QR order for user + course (reuse on refresh).
     *
     * @param  int  $userId
     * @param  int  $courseId
     * @return \App\Models\Order|null
     */
    public function findPendingSepayOrderForCourse(int $userId, int $courseId): ?Order
    {
        return Order::query()
            ->whereHas('items', function ($sub) use ($courseId) {
                $sub->where('course_id', $courseId);
            })
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->where('payment_method', 'sepay_qr')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Find a pending OnePay card order for user + course.
     *
     * @param  int  $userId
     * @param  int  $courseId
     * @param  string  $paymentMethod
     * @return \App\Models\Order|null
     */
    public function findPendingOnepayOrderForCourse(int $userId, int $courseId, string $paymentMethod): ?Order
    {
        return Order::query()
            ->whereHas('items', function ($sub) use ($courseId) {
                $sub->where('course_id', $courseId);
            })
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->where('payment_method', $paymentMethod)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Create pending order with one course line and set payment_reference to ORDER_{id}.
     *
     * @param  int  $userId
     * @param  int  $originalAmount
     * @param  int  $discountAmount
     * @param  int  $finalAmount
     * @param  int  $courseId
     * @return \App\Models\Order
     */
    public function createPendingSepayOrderWithCourse(
        int $userId,
        int $originalAmount,
        int $discountAmount,
        int $finalAmount,
        int $courseId
    ): Order {
        return DB::transaction(function () use ($userId, $originalAmount, $discountAmount, $finalAmount, $courseId) {
            $order = Order::query()->create([
                'user_id' => $userId,
                'subtotal_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $finalAmount,
                'status' => 'pending',
                'payment_method' => 'sepay_qr',
                'note' => null,
                'payment_reference' => null,
            ]);

            OrderItem::query()->create([
                'order_id' => $order->id,
                'course_id' => $courseId,
                'original_price' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_price' => $finalAmount,
            ]);

            $reference = 'ORDER_' . $order->id;
            $order->forceFill(['payment_reference' => $reference])->save();

            return $order->fresh(['items']);
        });
    }

    /**
     * Create pending OnePay card order with one course line.
     *
     * @param  int  $userId
     * @param  int  $originalAmount
     * @param  int  $discountAmount
     * @param  int  $finalAmount
     * @param  int  $courseId
     * @param  string  $paymentMethod
     * @return \App\Models\Order
     */
    public function createPendingOnepayOrderWithCourse(
        int $userId,
        int $originalAmount,
        int $discountAmount,
        int $finalAmount,
        int $courseId,
        string $paymentMethod
    ): Order {
        return DB::transaction(function () use ($userId, $originalAmount, $discountAmount, $finalAmount, $courseId, $paymentMethod) {
            $order = Order::query()->create([
                'user_id' => $userId,
                'subtotal_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $finalAmount,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'note' => null,
                'payment_reference' => null,
            ]);

            OrderItem::query()->create([
                'order_id' => $order->id,
                'course_id' => $courseId,
                'original_price' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_price' => $finalAmount,
            ]);

            return $order->fresh(['items']);
        });
    }

    /**
     * Find order by exact payment reference.
     *
     * @param  string  $paymentReference
     * @return \App\Models\Order|null
     */
    public function findByPaymentReference(string $paymentReference): ?Order
    {
        return Order::query()
            ->where('payment_reference', $paymentReference)
            ->first();
    }

    /**
     * Mark order paid (idempotent if already paid).
     *
     * @param  \App\Models\Order  $order
     * @return bool  True if state changed to paid
     */
    public function markPaidIfPending(Order $order): bool
    {
        if ($order->status !== 'pending') {
            return false;
        }

        $order->forceFill([
            'status' => 'paid',
            'paid_at' => now(),
        ])->save();

        return true;
    }
}
