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
                ->state([
                    'course_id' => $course->id
                ])
                ->create();

            CourseAttribute::factory()
                ->count(2)
                ->benefit()
                ->state([
                    'course_id' => $course->id
                ])
                ->create();

            CourseAttribute::factory()
                ->count(2)
                ->target()
                ->state([
                    'course_id' => $course->id
                ])
                ->create();
        });
    }
}