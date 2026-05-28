<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseDiscount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseDiscountFactory extends Factory
{
    protected $model = CourseDiscount::class;

    public function definition()
    {
        $startsAt = $this->faker->dateTimeBetween('-2 weeks', '+1 week');
        $endsAt = (clone $startsAt)->modify('+' . $this->faker->numberBetween(7, 30) . ' days');

        return [
            'course_id' => Course::factory(),
            'type' => CourseDiscount::TYPE_PERCENT,
            'amount' => $this->faker->numberBetween(5, 40),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'repeat_type' => CourseDiscount::REPEAT_NONE,
            'day_of_week' => null,
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }

    public function percent()
    {
        return $this->state(function () {
            return [
                'type' => CourseDiscount::TYPE_PERCENT,
                'amount' => $this->faker->numberBetween(5, 50),
            ];
        });
    }

    public function fixed()
    {
        return $this->state(function () {
            return [
                'type' => CourseDiscount::TYPE_FIXED,
                'amount' => $this->faker->numberBetween(50, 300) * 1000,
            ];
        });
    }

    public function finalPrice()
    {
        return $this->state(function () {
            return [
                'type' => CourseDiscount::TYPE_FINAL_PRICE,
                'amount' => $this->faker->numberBetween(99, 699) * 1000,
            ];
        });
    }

    public function weekly()
    {
        return $this->state(function () {
            return [
                'repeat_type' => CourseDiscount::REPEAT_WEEKLY,
                'day_of_week' => $this->faker->numberBetween(0, 6),
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function () {
            return [
                'is_active' => false,
            ];
        });
    }
}
