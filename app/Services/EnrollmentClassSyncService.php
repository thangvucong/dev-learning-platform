<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EnrollmentClassSyncService
{
    /**
     * Assign a user to a class only when an active enrollment exists.
     *
     * @param  \App\Models\CourseClass  $courseClass
     * @param  int  $userId
     * @param  string  $status
     * @param  \Illuminate\Support\Carbon|string|null  $assignedAt
     * @return void
     */
    public function assignUserToClass(CourseClass $courseClass, int $userId, string $status = 'active', $assignedAt = null): void
    {
        $this->assertActiveEnrollmentForClass($courseClass, $userId);

        $courseClass->students()->syncWithoutDetaching([
            $userId => [
                'status' => $status,
                'assigned_at' => $assignedAt ?: now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Keep class assignment statuses in sync with enrollment status.
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @return void
     */
    public function syncClassAssignmentsForEnrollment(Enrollment $enrollment): void
    {
        $status = $enrollment->status === 'active' ? 'active' : 'inactive';

        DB::table('course_class_user')
            ->join('course_classes', 'course_classes.id', '=', 'course_class_user.course_class_id')
            ->where('course_classes.course_id', $enrollment->course_id)
            ->where('course_class_user.user_id', $enrollment->user_id)
            ->update([
                'course_class_user.status' => $status,
                'course_class_user.updated_at' => now(),
            ]);
    }

    /**
     * Auto assign to the nearest available class for an active enrollment.
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @return \App\Models\CourseClass|null
     */
    public function autoAssignUpcomingClassForEnrollment(Enrollment $enrollment): ?CourseClass
    {
        if ($enrollment->status !== 'active') {
            return null;
        }

        $alreadyAssignedClass = CourseClass::query()
            ->where('course_id', $enrollment->course_id)
            ->whereHas('students', function ($query) use ($enrollment) {
                $query->where('users.id', $enrollment->user_id)
                    ->where('course_class_user.status', 'active');
            })
            ->orderBy('start_at')
            ->first();

        if ($alreadyAssignedClass) {
            return $alreadyAssignedClass;
        }

        $candidateClass = CourseClass::query()
            ->where('course_id', $enrollment->course_id)
            ->whereIn('status', ['upcoming', 'active'])
            ->withCount('students')
            ->havingRaw('students_count < capacity')
            ->orderByRaw('CASE WHEN start_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('start_at')
            ->orderBy('id')
            ->first();

        if (!$candidateClass) {
            return null;
        }

        $this->assignUserToClass(
            $candidateClass,
            (int) $enrollment->user_id,
            'active',
            $enrollment->enrolled_at ?: now()
        );

        return $candidateClass;
    }

    /**
     * Validate that user has active enrollment for the class's course.
     *
     * @param  \App\Models\CourseClass  $courseClass
     * @param  int  $userId
     * @return void
     */
    public function assertActiveEnrollmentForClass(CourseClass $courseClass, int $userId): void
    {
        $hasActiveEnrollment = Enrollment::query()
            ->where('course_id', $courseClass->course_id)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();

        if (!$hasActiveEnrollment) {
            throw new RuntimeException('Cannot assign class without active enrollment.');
        }
    }
}

