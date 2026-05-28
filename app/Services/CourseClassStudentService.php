<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Repositories\CourseClassEnrollmentRepository;
use Carbon\CarbonImmutable;

class CourseClassStudentService
{
    protected CourseClassEnrollmentRepository $enrollmentRepository;

    public function __construct(CourseClassEnrollmentRepository $enrollmentRepository)
    {
        $this->enrollmentRepository = $enrollmentRepository;
    }

    /**
     * Add students into a class with:
     * - eligibility: user must have active enrollment for the class's course
     * - capacity validation: do not exceed classes.capacity for active students
     *
     * @param  array<int, int|string>  $userIds
     * @return array<string, mixed>
     */

    public function addStudents(CourseClass $class, array $userIds): array
    {
        // 1. Chuẩn hóa dữ liệu User ID đầu vào
        $userIds = array_values(array_unique(array_map(static function ($id) {
            return is_numeric($id) ? (int) $id : null;
        }, $userIds)));
        $userIds = array_values(array_filter($userIds));

        if ($userIds === []) {
            return [
                'success' => true,
                'class_id' => $class->id,
                'added' => 0,
                'skipped' => [],
            ];
        }

      
        $validStudentIds = \App\Models\User::query()
            ->whereIn('id', $userIds)
            ->role(\App\Models\User::ROLE_STUDENT)
            ->pluck('id')
            ->toArray();

        $eligibleSet = array_fill_keys($validStudentIds, true);
        // --- KẾT THÚC LOGIC LỌC ROLE STUDENT ---

        $capacity = $class->capacity !== null ? (int) $class->capacity : 30;
        $now = CarbonImmutable::now();

        $currentActive = $this->enrollmentRepository->getActiveStudentCount((int) $class->id);
        
        $existingStatuses = $this->enrollmentRepository
            ->getExistingPivotStatuses((int) $class->id, $userIds);

        $addedUserIds = [];
        $activatedUserIds = [];
        $alreadyActiveUserIds = [];
        $skippedCapacity = [];
        $skippedNotStudent = [];

        $activeCount = $currentActive;

        foreach ($userIds as $userId) {
       
            if (!isset($eligibleSet[$userId])) {
                $skippedNotStudent[] = $userId;
                continue;
            }
            
            $pivotStatus = $existingStatuses[$userId] ?? null;
            
            if ($pivotStatus === 'active') {
                $alreadyActiveUserIds[] = $userId;
                continue;
            }

            if ($capacity > 0 && $activeCount >= $capacity) {
                $skippedCapacity[] = $userId;
                continue;
            }

            if ($pivotStatus !== null) {
                $activatedUserIds[] = $userId;
            } else {
                $addedUserIds[] = $userId;
            }

            $activeCount++;
        }

        $activatedCount = $this->enrollmentRepository
            ->activateExistingPivot((int) $class->id, $activatedUserIds, $now);

        $addedCount = $this->enrollmentRepository
            ->attachNewPivot((int) $class->id, $addedUserIds, $now);

        return [
            'success' => true,
            'class_id' => $class->id,
            'capacity' => $capacity,
            'current_active_before' => $currentActive,
            'current_active_after' => $activeCount,
            'added' => $addedCount,
            'activated' => $activatedCount,
            'already_active' => count($alreadyActiveUserIds),
            'skipped' => [
                'capacity_full' => $skippedCapacity,
                'not_a_student' => $skippedNotStudent, 
            ],
        ];
    }
}
 
