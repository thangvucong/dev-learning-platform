<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CoursePrice;
use App\Models\Currency;
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
            $total = $order->items()->sum('price');

            if ($total > 0) {
                $order->update(['total_amount' => $total]);
            }
        });
    }

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'total_amount' => 0,
            'currency_id' => Currency::factory(),
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'momo']),
            'note' => $this->faker->optional()->sentence(),
            'paid_at' => $this->faker->optional(0.7)->dateTimeBetween('-2 months', 'now'),
        ];
    }

    public function paid()
    {
        return $this->state(function () {
            return [
                'status' => 'paid',
                'paid_at' => now()->subDays($this->faker->numberBetween(1, 30)),
            ];
        });
    }

    public function withItems($count = 2)
    {
        return $this->has(
            OrderItem::factory()
                ->count($count)
                ->state(function (array $attributes, Order $order) {
                    $course = Course::query()->inRandomOrder()->first();

                    $coursePrice = CoursePrice::query()
                        ->where('course_id', $course->id)
                        ->where('currency_id', $order->currency_id)
                        ->where('is_active', true)
                        ->first();

                    if (!$coursePrice) {
                        $coursePrice = CoursePrice::query()
                            ->where('course_id', $course->id)
                            ->where('is_active', true)
                            ->first();
                    }

                    return [
                        'course_id' => $course->id,
                        'price' => $coursePrice ? $coursePrice->price : random_int(149000, 999000),
                    ];
                }),
            'items'
        );
    }
}
