<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        $title = $this->faker->unique()->randomElement([
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

        return [
            'instructor_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::lower(Str::random(6)),
            'description' => $this->faker->paragraph(4),
            'thumbnail_url' => $this->faker->imageUrl(640, 360, 'education', true),
            'intro_video_url' => 'https://cdn.example.com/videos/' . Str::slug($title) . '.mp4',
            'status' => $this->faker->randomElement([0, 1]),
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function published()
    {
        return $this->state(function () {
            return [
                'status' => 1,
                'published_at' => now()->subDays($this->faker->numberBetween(3, 120)),
            ];
        });
    }

    public function free()
    {
        return $this->state(function () {
            return [
                'price' => 0,
            ];
        });
    }
}
