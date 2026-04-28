<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CourseClassStudentSeeder extends Seeder
{
    /**
     * Seed students into classes from existing enrollments.
     *
     * @return void
     */
    public function run()
    {
        DB::table('course_class_user')->delete();

        $enrollments = Enrollment::query()
            ->with('course.classes')
            ->orderBy('course_id')
            ->orderBy('user_id')
            ->get();

        $classIndexes = [];

        foreach ($enrollments as $enrollment) {
            $courseClasses = $enrollment->course ? $enrollment->course->classes : collect();

            if ($courseClasses->isEmpty()) {
                continue;
            }

            $sortedClasses = $courseClasses->sortBy('start_at')->values();
            $courseId = $enrollment->course_id;
            $currentIndex = $classIndexes[$courseId] ?? 0;
            $targetClass = $sortedClasses[$currentIndex % $sortedClasses->count()];

            $classIndexes[$courseId] = $currentIndex + 1;

            $targetClass->students()->syncWithoutDetaching([
                $enrollment->user_id => [
                    'status' => 'active',
                    'assigned_at' => $enrollment->enrolled_at ?: now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
