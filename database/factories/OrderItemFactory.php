<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        $originalPrice = $this->faker->numberBetween(199, 1999) * 1000;
        $discountAmount = $this->faker->numberBetween(0, min(500000, $originalPrice));

        return [
            'order_id' => Order::factory(),
            'course_id' => Course::factory(),
            'original_price' => $originalPrice,
            'discount_amount' => $discountAmount,
            'final_price' => max(0, $originalPrice - $discountAmount),
        ];
    }

    public function forCourse(Course $course)
    {
        return $this->state(function () use ($course) {
            $originalPrice = (int) $course->original_price;
            $discountAmount = $originalPrice > 0
                ? min($originalPrice, $this->faker->numberBetween(0, min(300000, $originalPrice)))
                : 0;

            return [
                'course_id' => $course->id,
                'original_price' => $originalPrice,
                'discount_amount' => $discountAmount,
                'final_price' => max(0, $originalPrice - $discountAmount),
            ];
        });
    }

    public function free()
    {
        return $this->state(function () {
            return [
                'original_price' => 0,
                'discount_amount' => 0,
                'final_price' => 0,
            ];
        });
    }
}
