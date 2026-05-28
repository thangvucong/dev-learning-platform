<?php

namespace Database\Seeders;

use App\Models\ClassEnrollment;
use App\Models\CourseClass;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class ClassEnrollmentSeeder extends Seeder
{
    /**
     * Seed class enrollment data from active course enrollments.
     *
     * @return void
     */
    public function run()
    {
        ClassEnrollment::query()->delete();

        $enrollments = Enrollment::query()
            ->where('status', Enrollment::STATUS_ACTIVE)
            ->orderBy('course_id')
            ->orderBy('user_id')
            ->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        $classesByCourse = CourseClass::query()
            ->orderBy('start_at')
            ->orderBy('id')
            ->get()
            ->groupBy('course_id');

        $classIndexes = [];

        foreach ($enrollments as $enrollment) {
            $courseClasses = $classesByCourse->get($enrollment->course_id);

            if (!$courseClasses || $courseClasses->isEmpty()) {
                continue;
            }

            $courseId = (int) $enrollment->course_id;
            $currentIndex = $classIndexes[$courseId] ?? 0;
            $targetClass = $courseClasses->values()[$currentIndex % $courseClasses->count()];
            $classIndexes[$courseId] = $currentIndex + 1;

            ClassEnrollment::factory()
                ->active()
                ->state([
                    'class_id' => $targetClass->id,
                    'user_id' => $enrollment->user_id,
                    'assigned_at' => $enrollment->enrolled_at ?: now(),
                ])
                ->create();
        }
    }
}
