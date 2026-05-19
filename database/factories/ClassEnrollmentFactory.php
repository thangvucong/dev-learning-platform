<?php

namespace Database\Factories;

use App\Models\ClassEnrollment;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassEnrollmentFactory extends Factory
{
    protected $model = ClassEnrollment::class;

    public function definition()
    {
        return [
            'class_id' => CourseClass::factory(),
            'user_id' => User::factory(),
            'status' => ClassEnrollment::STATUS_ACTIVE,
            'assigned_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }

    public function active()
    {
        return $this->state(function () {
            return [
                'status' => ClassEnrollment::STATUS_ACTIVE,
                'assigned_at' => now()->subDays($this->faker->numberBetween(1, 30)),
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function () {
            return [
                'status' => ClassEnrollment::STATUS_INACTIVE,
            ];
        });
    }

    public function completed()
    {
        return $this->state(function () {
            return [
                'status' => ClassEnrollment::STATUS_COMPLETED,
            ];
        });
    }
}
