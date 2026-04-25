<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Track;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    public function run()
    {
        Track::query()->delete();

        Course::query()->each(function (Course $course) {
            $parents = Track::factory()
                ->count(3)
                ->state(function () use ($course) {
                    return [
                        'course_id' => $course->id,
                        'parent_id' => null,
                    ];
                })
                ->sequence(
                    ['title' => 'Getting Started', 'position' => 1],
                    ['title' => 'Core Concepts', 'position' => 2],
                    ['title' => 'Project & Practice', 'position' => 3]
                )
                ->create();

            $parents->each(function (Track $parent) use ($course) {
                Track::factory()
                    ->count(2)
                    ->childOf($parent)
                    ->state(function () use ($course, $parent) {
                        return [
                            'course_id' => $course->id,
                            'position' => ($parent->position * 10) + random_int(1, 9),
                        ];
                    })
                    ->sequence(
                        ['title' => $parent->title . ' - Part A'],
                        ['title' => $parent->title . ' - Part B']
                    )
                    ->create();
            });
        });
    }
}
