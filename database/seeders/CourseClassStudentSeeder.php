<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Services\EnrollmentClassSyncService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CourseClassStudentSeeder extends Seeder
{
    /**
     * @var \App\Services\EnrollmentClassSyncService
     */
    protected EnrollmentClassSyncService $enrollmentClassSyncService;

    /**
     * Create a new seeder instance.
     *
     * @param  \App\Services\EnrollmentClassSyncService  $enrollmentClassSyncService
     */
    public function __construct(EnrollmentClassSyncService $enrollmentClassSyncService)
    {
        $this->enrollmentClassSyncService = $enrollmentClassSyncService;
    }

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

            $this->enrollmentClassSyncService->assignUserToClass(
                $targetClass,
                (int) $enrollment->user_id,
                'active',
                $enrollment->enrolled_at ?: now()
            );
        }
    }
}
