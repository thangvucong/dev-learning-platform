<?php

namespace Database\Seeders;

use App\Models\Course;
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

        $teacherIds = User::query()->where('role', 'teacher')->pluck('id')->all();
        if ($teacherIds === []) {
            throw new \RuntimeException(
                'CourseSeeder: không có user nào với role=teacher trong bảng users. Chạy UserSeeder trước và đảm bảo có ít nhất một teacher.'
            );
        }

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

        foreach ($courses as $title) {
            Course::factory()
                ->published()
                ->state(function () use ($title, $teacherIds) {
                    return [
                        'title' => $title,
                        'instructor_id' => $teacherIds[array_rand($teacherIds)],
                        'status' => 1,
                    ];
                })
                ->create();
        }
    }
}
