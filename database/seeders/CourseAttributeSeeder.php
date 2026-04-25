<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseAttribute;
use Illuminate\Database\Seeder;

class CourseAttributeSeeder extends Seeder
{
    public function run()
    {
        CourseAttribute::query()->delete();

        Course::query()->each(function (Course $course) {
            CourseAttribute::factory()
                ->count(2)
                ->requirement()
                ->state(function () use ($course) {
                    return ['course_id' => $course->id];
                })
                ->sequence(
                    ['position' => 1],
                    ['position' => 2]
                )
                ->create();

            CourseAttribute::factory()
                ->count(2)
                ->benefit()
                ->state(function () use ($course) {
                    return ['course_id' => $course->id];
                })
                ->sequence(
                    ['position' => 3],
                    ['position' => 4]
                )
                ->create();

            CourseAttribute::factory()
                ->count(2)
                ->target()
                ->state(function () use ($course) {
                    return ['course_id' => $course->id];
                })
                ->sequence(
                    ['position' => 5],
                    ['position' => 6]
                )
                ->create();
        });
    }
}
