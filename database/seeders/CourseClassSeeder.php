<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseClassSeeder extends Seeder
{
    /**
     * Seed classes/cohorts for each course.
     *
     * @return void
     */
    public function run()
    {
        CourseClass::query()->delete();

        Course::query()->each(function (Course $course, int $index) {
            $classCount = random_int(1, 2);

            for ($iteration = 1; $iteration <= $classCount; $iteration++) {
                $startAt = now()->addDays(($index + 1) * 7 + ($iteration * 3))->startOfDay()->addHours(19);
                CourseClass::query()->create([
                    'course_id' => $course->id,
                    'name' => sprintf('%s - Cohort %02d', $course->title, $iteration),
                    'code' => strtoupper(Str::random(3)) . '-' . $course->id . $iteration,
                    'status' => 'upcoming',
                    'start_at' => $startAt,
                    'end_at' => (clone $startAt)->addWeeks(10),
                    'location' => $iteration % 2 === 0 ? 'Ho Chi Minh City Campus' : 'Zoom Room A',
                ]);
            }
        });
    }
}
