<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Database\Seeder;

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

        $courses = Course::query()
            ->select(['id', 'title'])
            ->orderBy('id')
            ->get();

        if ($courses->isEmpty()) {
            throw new \RuntimeException(
                'CourseClassSeeder: không có khóa học nào. Chạy CourseSeeder trước khi seed classes.'
            );
        }

        $instructorIds = User::query()
            ->role(User::ROLE_INSTRUCTOR)
            ->pluck('id')
            ->all();

        $courses->each(function (Course $course, int $index) use ($instructorIds) {
            $classCount = random_int(1, 2);

            /** @var int $iteration */
            for ($iteration = 1; $iteration <= $classCount; $iteration++) {
                $startAt = now()->addDays(($index + 1) * 7 + ($iteration * 3))->startOfDay()->addHours(19);

                CourseClass::factory()
                    ->upcoming()
                    ->state([
                        'course_id' => $course->id,
                        'instructor_id' => $instructorIds === []
                            ? null
                            : $instructorIds[($index + $iteration - 1) % count($instructorIds)],
                        'name' => sprintf('%s - Cohort %02d', $course->title, $iteration),
                        'code' => sprintf('CLS-%03d-%02d', $course->id, $iteration),
                        'mode' => $iteration % 2 === 0 ? CourseClass::MODE_OFFLINE : CourseClass::MODE_ZOOM,
                        'capacity' => 30,
                        'start_at' => $startAt,
                        'end_at' => (clone $startAt)->addWeeks(10),
                        'location' => $iteration % 2 === 0 ? 'Ho Chi Minh City Campus' : null,
                    ])
                    ->create();
            }
        });
    }
}
