<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Track;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    public function run()
    {
        Track::query()->whereNotNull('parent_id')->delete();
        Track::query()->delete();

        $courses = Course::query()
            ->select(['id'])
            ->orderBy('id')
            ->get();

        if ($courses->isEmpty()) {
            throw new \RuntimeException(
                'TrackSeeder: không có khóa học nào. Chạy CourseSeeder trước khi seed tracks.'
            );
        }

        $courses->each(function (Course $course) {
            $parents = Track::factory()
                ->count(3)
                ->parent()
                ->state(function () use ($course) {
                    return [
                        'course_id' => $course->id,
                    ];
                })
                ->sequence(
                    [
                        'title' => 'Getting Started',
                        'description' => 'Chuẩn bị môi trường, công cụ và cách tiếp cận khóa học.',
                        'position' => 1,
                    ],
                    [
                        'title' => 'Core Concepts',
                        'description' => 'Nắm các khái niệm chính và thực hành qua ví dụ nhỏ.',
                        'position' => 2,
                    ],
                    [
                        'title' => 'Project & Practice',
                        'description' => 'Áp dụng kiến thức vào bài tập tổng hợp và dự án thực tế.',
                        'position' => 3,
                    ]
                )
                ->create();

            $parents->each(function (Track $parent) use ($course) {
                Track::factory()
                    ->count(2)
                    ->childOf($parent)
                    ->state(function () use ($course, $parent) {
                        return [
                            'course_id' => $course->id,
                        ];
                    })
                    ->sequence(
                        [
                            'title' => $parent->title . ' - Part A',
                            'description' => 'Bài học nền tảng trong phần ' . $parent->title . '.',
                            'position' => ((int) $parent->position * 10) + 1,
                        ],
                        [
                            'title' => $parent->title . ' - Part B',
                            'description' => 'Bài thực hành mở rộng trong phần ' . $parent->title . '.',
                            'position' => ((int) $parent->position * 10) + 2,
                        ]
                    )
                    ->create();
            });
        });
    }
}
