<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Level;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed courses data.
     *
     * @return void
     */
    public function run()
    {
        Course::query()->delete();

        $levelIds = Level::query()->pluck('id')->all();
        $teacherIds = User::role('teacher')->pluck('id')->all();

        $courses = [
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
        ];

        foreach ($courses as $index => $title) {
            Course::factory()
                ->published()
                ->state(function () use ($title, $levelIds, $teacherIds, $index) {
                    return [
                        'title' => $title,
                        'level_id' => $levelIds[array_rand($levelIds)],
                        'instructor_id' => $teacherIds[array_rand($teacherIds)],
                        'status' => 1,
                        'is_free' => $index < 2,
                    ];
                })
                ->create();
        }
    }
}
