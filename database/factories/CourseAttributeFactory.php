<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseAttributeFactory extends Factory
{
    protected $model = CourseAttribute::class;

    public function definition()
    {
        return [
            'course_id' => Course::factory(),
            'type' => $this->faker->randomElement([
                CourseAttribute::TYPE_REQUIREMENT,
                CourseAttribute::TYPE_BENEFIT,
                CourseAttribute::TYPE_TARGET,
            ]),
            'content' => $this->faker->sentence(10),
        ];
    }

    public function requirement()
    {
        $contents = [
            'Basic knowledge of HTML, CSS, and programming mindset.',
            'A computer with PHP 7.3+ and Composer installed.',
            'Willingness to practice by building mini projects.',
        ];

        return $this->state(function () use ($contents) {
            return [
                'type' => CourseAttribute::TYPE_REQUIREMENT,
                'content' => $this->faker->randomElement($contents),
            ];
        });
    }

    public function benefit()
    {
        $contents = [
            'Build production-ready Laravel applications from scratch.',
            'Apply clean architecture and reusable service patterns.',
            'Deploy and maintain scalable web applications confidently.',
        ];

        return $this->state(function () use ($contents) {
            return [
                'type' => CourseAttribute::TYPE_BENEFIT,
                'content' => $this->faker->randomElement($contents),
            ];
        });
    }

    public function target()
    {
        $contents = [
            'Beginner web developers transitioning to backend development.',
            'Junior PHP developers who want to master Laravel.',
            'Freelancers building practical online course products.',
        ];

        return $this->state(function () use ($contents) {
            return [
                'type' => CourseAttribute::TYPE_TARGET,
                'content' => $this->faker->randomElement($contents),
            ];
        });
    }
}
