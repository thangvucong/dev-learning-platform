<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CoursePrice;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoursePriceFactory extends Factory
{
    protected $model = CoursePrice::class;

    public function definition()
    {
        $price = $this->faker->numberBetween(149000, 2999000);

        return [
            'course_id' => Course::factory(),
            'currency_id' => Currency::factory(),
            'price' => $price,
            'compare_price' => $price + $this->faker->numberBetween(50000, 300000),
            'starts_at' => now()->subDays($this->faker->numberBetween(1, 40)),
            'ends_at' => null,
            'is_active' => true,
        ];
    }

    public function active()
    {
        return $this->state(function () {
            return [
                'is_active' => true,
                'starts_at' => now()->subDays($this->faker->numberBetween(1, 30)),
                'ends_at' => null,
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function () {
            return [
                'is_active' => false,
                'starts_at' => now()->subDays(90),
                'ends_at' => now()->subDays($this->faker->numberBetween(1, 30)),
            ];
        });
    }
}
