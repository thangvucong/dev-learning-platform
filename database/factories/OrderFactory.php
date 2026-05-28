<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $order->load('items');
            $subtotal = (int) $order->items->sum('original_price');
            $discount = (int) $order->items->sum('discount_amount');
            $total = (int) $order->items->sum('final_price');
            $updates = [];

            if ($subtotal > 0 || $discount > 0 || $total > 0) {
                $updates['subtotal_amount'] = $subtotal;
                $updates['discount_amount'] = $discount;
                $updates['total_amount'] = $total;
            }

            if ($updates !== []) {
                $order->update($updates);
            }
        });
    }

    public function definition()
    {
        $status = $this->faker->randomElement([
            Order::STATUS_PENDING,
            Order::STATUS_PAID,
            Order::STATUS_CANCELLED,
            Order::STATUS_FAILED,
        ]);

        return [
            'user_id' => User::factory(),
            'subtotal_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 0,
            'status' => $status,
            'payment_method' => $this->faker->randomElement([
                Order::PAYMENT_ONEPAY_DOMESTIC,
                Order::PAYMENT_ONEPAY_INTERNATIONAL,
                Order::PAYMENT_SEPAY_QR,
            ]),
            'payment_reference' => 'ORDER_' . $this->faker->unique()->numberBetween(100000, 999999),
            'note' => $this->faker->optional()->sentence(),
            'paid_at' => $status === Order::STATUS_PAID
                ? $this->faker->dateTimeBetween('-2 months', 'now')
                : null,
            'cancelled_at' => $status === Order::STATUS_CANCELLED
                ? $this->faker->dateTimeBetween('-2 months', 'now')
                : null,
        ];
    }

    public function paid()
    {
        return $this->state(function () {
            return [
                'status' => Order::STATUS_PAID,
                'paid_at' => now()->subDays($this->faker->numberBetween(1, 30)),
                'cancelled_at' => null,
            ];
        });
    }

    public function pending()
    {
        return $this->state(function () {
            return [
                'status' => Order::STATUS_PENDING,
                'paid_at' => null,
                'cancelled_at' => null,
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function () {
            return [
                'status' => Order::STATUS_CANCELLED,
                'paid_at' => null,
                'cancelled_at' => now()->subDays($this->faker->numberBetween(1, 30)),
            ];
        });
    }

    public function withItems($count = 2)
    {
        return $this->has(
            OrderItem::factory()
                ->count($count),
            'items'
        );
    }
}
