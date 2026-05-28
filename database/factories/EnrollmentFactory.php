<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition()
    {
        return [
            'course_id' => Course::factory(),
            'user_id' => User::factory(),
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'completed_at' => null,
        ];
    }

    public function active()
    {
        return $this->state(function () {
            return [
                'status' => Enrollment::STATUS_ACTIVE,
                'completed_at' => null,
            ];
        });
    }

    public function completed()
    {
        $enrolledAt = now()->subMonths(4);

        return $this->state(function () use ($enrolledAt) {
            return [
                'status' => Enrollment::STATUS_COMPLETED,
                'enrolled_at' => $enrolledAt,
                'completed_at' => (clone $enrolledAt)->addMonths(3),
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function () {
            return [
                'status' => Enrollment::STATUS_CANCELLED,
                'completed_at' => null,
            ];
        });
    }
}
