<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Seed courses data.
     *
     * @return void
     */
    public function run()
    {
        $courses = [
            [
                'title' => 'JavaScript Fundamentals for Beginners',
                'original_price' => 499000,
                'rating_avg' => 4.7,
                'rating_count' => 128,
            ],
            [
                'title' => 'Modern PHP from Zero to Hero',
                'original_price' => 599000,
                'rating_avg' => 4.6,
                'rating_count' => 96,
            ],
            [
                'title' => 'Laravel 8 REST API in Practice',
                'original_price' => 799000,
                'rating_avg' => 4.8,
                'rating_count' => 214,
            ],
            [
                'title' => 'Mastering Eloquent ORM',
                'original_price' => 649000,
                'rating_avg' => 4.5,
                'rating_count' => 73,
            ],
            [
                'title' => 'Frontend with Blade and Alpine.js',
                'original_price' => 549000,
                'rating_avg' => 4.4,
                'rating_count' => 65,
            ],
            [
                'title' => 'Build Real-world Apps with Laravel',
                'original_price' => 899000,
                'rating_avg' => 4.9,
                'rating_count' => 256,
            ],
            [
                'title' => 'Clean Code in PHP Projects',
                'original_price' => 459000,
                'rating_avg' => 4.3,
                'rating_count' => 54,
            ],
            [
                'title' => 'Database Design for Web Developers',
                'original_price' => 699000,
                'rating_avg' => 4.6,
                'rating_count' => 119,
            ],
            [
                'title' => 'Testing Laravel Applications with PHPUnit',
                'original_price' => 749000,
                'rating_avg' => 4.7,
                'rating_count' => 88,
            ],
            [
                'title' => 'Secure Laravel Authentication and Authorization',
                'original_price' => 849000,
                'rating_avg' => 4.8,
                'rating_count' => 102,
            ],
        ];

        Course::query()->forceDelete();

        foreach ($courses as $course) {
            Course::factory()
                ->published()
                ->state(function () use ($course) {
                    return [
                        'title' => $course['title'],
                        'slug' => Str::slug($course['title']),
                        'original_price' => $course['original_price'],
                        'rating_avg' => $course['rating_avg'],
                        'rating_count' => $course['rating_count'],
                    ];
                })
                ->create();
        }
    }
}
