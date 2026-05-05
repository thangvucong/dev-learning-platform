<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
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
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->where('payment_method', 'sepay_qr')
            ->where(function ($query) use ($courseId) {
                $query->where('course_id', $courseId)
                    ->orWhereHas('items', function ($sub) use ($courseId) {
                        $sub->where('course_id', $courseId);
                    });
            })
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Create pending order with one course line and set payment_reference to ORDER_{id}.
     *
     * @param  int  $userId
     * @param  float  $totalAmount
     * @param  int  $courseId
     * @param  float  $linePrice
     * @return \App\Models\Order
     */
    public function createPendingSepayOrderWithCourse(
        int $userId,
        float $totalAmount,
        int $courseId,
        float $linePrice
    ): Order {
        return DB::transaction(function () use ($userId, $totalAmount, $courseId, $linePrice) {
            $order = Order::query()->create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => 'sepay_qr',
                'note' => null,
                'payment_reference' => null,
            ]);

            OrderItem::query()->create([
                'order_id' => $order->id,
                'course_id' => $courseId,
                'price' => $linePrice,
            ]);

            $reference = 'ORDER_' . $order->id;
            $order->forceFill(['payment_reference' => $reference])->save();

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
