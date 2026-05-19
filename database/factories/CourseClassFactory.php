<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseClassFactory extends Factory
{
    protected $model = CourseClass::class;

    public function definition()
    {
        $startAt = now()->addDays($this->faker->numberBetween(7, 45))->setTime(19, 0);

        return [
            'course_id' => Course::factory(),
            'instructor_id' => User::factory(),
            'name' => 'Cohort ' . $this->faker->unique()->numberBetween(100, 999),
            'code' => strtoupper(Str::random(6)),
            'mode' => $this->faker->randomElement([
                CourseClass::MODE_ZOOM,
                CourseClass::MODE_OFFLINE,
                CourseClass::MODE_HYBRID,
            ]),
            'status' => CourseClass::STATUS_UPCOMING,
            'capacity' => $this->faker->numberBetween(20, 40),
            'start_at' => $startAt,
            'end_at' => (clone $startAt)->addWeeks(10),
            'location' => $this->faker->optional()->randomElement([
                'Zoom Room A',
                'Ho Chi Minh City Campus',
                'Hanoi Learning Hub',
            ]),
        ];
    }

    public function upcoming()
    {
        return $this->state(function () {
            return [
                'status' => CourseClass::STATUS_UPCOMING,
            ];
        });
    }

    public function ongoing()
    {
        $startAt = now()->subWeeks(2)->setTime(19, 0);

        return $this->state(function () use ($startAt) {
            return [
                'status' => CourseClass::STATUS_ONGOING,
                'start_at' => $startAt,
                'end_at' => (clone $startAt)->addWeeks(10),
            ];
        });
    }

    public function completed()
    {
        $startAt = now()->subWeeks(14)->setTime(19, 0);

        return $this->state(function () use ($startAt) {
            return [
                'status' => CourseClass::STATUS_COMPLETED,
                'start_at' => $startAt,
                'end_at' => (clone $startAt)->addWeeks(10),
            ];
        });
    }
}
