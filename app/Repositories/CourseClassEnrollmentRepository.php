<?php

namespace App\Repositories;

use App\Models\Enrollment;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class CourseClassEnrollmentRepository
{
    public function getActiveStudentCount(int $classId): int
    {
        return (int) DB::table('class_enrollments')
            ->where('class_id', $classId)
            ->where('status', 'active')
            ->count();
    }

    /**
     * @return array<int, string> Map: user_id => pivot status
     */
    public function getExistingPivotStatuses(int $classId, array $userIds): array
    {
        if ($userIds === []) {
            return [];
        }

        $rows = DB::table('class_enrollments')
            ->select(['user_id', 'status'])
            ->where('class_id', $classId)
            ->whereIn('user_id', $userIds)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row->user_id] = (string) $row->status;
        }

        return $result;
    }

    /**
     * @return array<int> Eligible user ids with active enrollment for given course.
     */
    public function getEligibleActiveEnrollmentUserIds(int $courseId, array $userIds): array
    {
        if ($userIds === []) {
            return [];
        }

        return Enrollment::query()
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->whereIn('user_id', $userIds)
            ->pluck('user_id')
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public function activateExistingPivot(int $classId, array $userIds, CarbonInterface $assignedAt): int
    {
        if ($userIds === []) {
            return 0;
        }

        return (int) DB::table('class_enrollments')
            ->where('class_id', $classId)
            ->whereIn('user_id', $userIds)
            ->update([
                'status' => 'active',
                'assigned_at' => $assignedAt,
                'updated_at' => $assignedAt,
            ]);
    }

    public function attachNewPivot(int $classId, array $userIds, CarbonInterface $assignedAt): int
    {
        if ($userIds === []) {
            return 0;
        }

        $now = $assignedAt;
        $rows = array_map(static function (int $userId) use ($classId, $now) {
            return [
                'class_id' => $classId,
                'user_id' => $userId,
                'status' => 'active',
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $userIds);

        DB::table('class_enrollments')->insert($rows);

        return count($rows);
    }
}
