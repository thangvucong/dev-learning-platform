<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        $title = $this->faker->randomElement([
            'JavaScript Fundamentals for Beginners',
            'Modern PHP from Zero to Hero',
            'Laravel 8 REST API in Practice',
            'Mastering Eloquent ORM',
            'Frontend with Blade and Alpine.js',
            'Build Real-world Apps with Laravel',
            'Clean Code in PHP Projects',
            'Database Design for Web Developers',
            'Testing Laravel Applications with PHPUnit',
            'Secure Laravel Authentication and Authorization',
        ]);

        $status = $this->faker->randomElement([Course::STATUS_DRAFT, Course::STATUS_PUBLISHED]);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::lower(Str::random(6)),
            'description' => $this->faker->paragraph(4),
            'status' => $status,
            'original_price' => $this->faker->numberBetween(149, 2999) * 1000,
            'rating_avg' => $this->faker->randomFloat(1, 3.5, 5),
            'rating_count' => $this->faker->numberBetween(0, 500),
            'published_at' => $status === Course::STATUS_PUBLISHED
                ? $this->faker->dateTimeBetween('-6 months', 'now')
                : null,
        ];
    }

    public function published()
    {
        return $this->state(function () {
            return [
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => now()->subDays($this->faker->numberBetween(3, 120)),
            ];
        });
    }

    public function draft()
    {
        return $this->state(function () {
            return [
                'status' => Course::STATUS_DRAFT,
                'published_at' => null,
            ];
        });
    }

    public function free()
    {
        return $this->state(function () {
            return [
                'original_price' => 0,
            ];
        });
    }
}
