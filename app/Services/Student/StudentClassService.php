<?php

namespace App\Services\Student;

use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Support\Collection;

class StudentClassService
{
    /**
     * Build list payload for "Lớp học của tôi".
     *
     * @param  \App\Models\User  $user
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function buildList(User $user, array $filters): array
    {
        $classes = $user->assignedClasses()
            ->with(['course.instructor', 'sessions'])
            ->orderBy('start_at')
            ->get();

        $keyword = trim((string) ($filters['q'] ?? ''));
        $statusFilter = trim((string) ($filters['status'] ?? ''));

        $mapped = $classes->map(function (CourseClass $classItem) {
            $computedStatus = $this->computedClassStatus($classItem);
            $attendanceRate = $this->estimateAttendanceRateFromPivot($classItem);
            $progress = $this->estimateProgress($classItem);
            $nextSessionAt = $this->resolveNextSessionStartAt($classItem);

            return [
                'id' => $classItem->id,
                'name' => $classItem->name,
                'code' => $classItem->code,
                'status' => $computedStatus,
                'thumbnail' => optional($classItem->course)->thumbnail_url,
                'teacher' => optional(optional($classItem->course)->instructor)->name ?: 'Giảng viên',
                'course_title' => optional($classItem->course)->title ?: 'Khóa học',
                'next_session' => optional($nextSessionAt)->format('d/m/Y H:i'),
                'progress' => $progress,
                'attendance_rate' => $attendanceRate,
                'location' => $classItem->location ?: 'Online',
            ];
        });

        if ($keyword !== '') {
            $mapped = $mapped->filter(function (array $item) use ($keyword) {
                $haystack = mb_strtolower(implode(' ', [
                    (string) ($item['name'] ?? ''),
                    (string) ($item['course_title'] ?? ''),
                    (string) ($item['teacher'] ?? ''),
                    (string) ($item['code'] ?? ''),
                ]));

                return mb_stripos($haystack, mb_strtolower($keyword)) !== false;
            })->values();
        }

        if ($statusFilter !== '' && in_array($statusFilter, ['ongoing', 'upcoming', 'completed'], true)) {
            $mapped = $mapped->where('status', $statusFilter)->values();
        }

        return [
            'filters' => [
                'q' => $keyword,
                'status' => $statusFilter,
            ],
            'classes' => $mapped,
        ];
    }

    /**
     * Build detail payload for one student class.
     *
     * @param  \App\Models\User  $user
     * @param  int  $classId
     * @return array<string, mixed>
     */
    public function buildDetail(User $user, int $classId): array
    {
        $courseClass = $user->assignedClasses()
            ->where('classes.id', $classId)
            ->with(['course.instructor', 'users', 'sessions'])
            ->firstOrFail();

        $status = $this->computedClassStatus($courseClass);
        $progress = $this->estimateProgress($courseClass);
        $attendanceRate = $this->estimateAttendanceRateFromPivot($courseClass);
        $sessions = $this->buildSessions($courseClass);
        $nextSessionAt = $this->resolveNextSessionStartAt($courseClass);

        return [
            'class' => [
                'id' => $courseClass->id,
                'name' => $courseClass->name,
                'code' => $courseClass->code,
                'status' => $status,
                'description' => optional($courseClass->course)->description ?: 'Lớp học thực hành theo lộ trình học tập.',
                'thumbnail' => optional($courseClass->course)->thumbnail_url,
                'teacher' => optional(optional($courseClass->course)->instructor)->name ?: 'Giảng viên',
                'teacher_email' => optional(optional($courseClass->course)->instructor)->email,
                'course_title' => optional($courseClass->course)->title ?: 'Khóa học',
                'progress' => $progress,
                'attendance_rate' => $attendanceRate,
                'next_session' => optional($nextSessionAt)->format('d/m/Y H:i'),
                'location' => $courseClass->location ?: 'Online',
            ],
            'overview' => [
                'total_members' => $courseClass->users->count(),
                'completed_sessions' => $status === 'completed' ? 12 : ($status === 'ongoing' ? 7 : 0),
                'remaining_sessions' => $status === 'completed' ? 0 : ($status === 'ongoing' ? 5 : 12),
                'study_streak_days' => $status === 'ongoing' ? 9 : 0,
            ],
            'schedules' => $sessions,
            'materials' => $this->buildMaterials($courseClass),
            'members' => [
                'teacher' => [
                    'name' => optional(optional($courseClass->course)->instructor)->name ?: 'Giảng viên',
                    'email' => optional(optional($courseClass->course)->instructor)->email,
                    'avatar' => optional(optional($courseClass->course)->instructor)->avatar_url,
                ],
                'students' => $courseClass->users->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'avatar' => $student->avatar_url,
                    ];
                })->values(),
            ],
            'attendance' => [
                'rate' => $attendanceRate,
                'present' => (int) round(($attendanceRate / 100) * 20),
                'absent' => max(0, 20 - (int) round(($attendanceRate / 100) * 20) - 1),
                'late' => 1,
                'timeline' => $this->buildAttendanceTimeline($attendanceRate),
            ],
        ];
    }

    /**
     * Resolve class status.
     */
    protected function computedClassStatus(CourseClass $classItem): string
    {
        if ($classItem->status && in_array($classItem->status, ['ongoing', 'upcoming', 'completed'], true)) {
            return $classItem->status;
        }

        if ($classItem->start_at && $classItem->start_at->isFuture()) {
            return 'upcoming';
        }

        if ($classItem->end_at && $classItem->end_at->isPast()) {
            return 'completed';
        }

        return 'ongoing';
    }

    /**
     * Resolve next upcoming session time from class sessions.
     *
     * @param  \App\Models\CourseClass  $classItem
     * @return mixed|null
     */
    protected function resolveNextSessionStartAt(CourseClass $classItem)
    {
        $sessions = $classItem->sessions instanceof Collection ? $classItem->sessions : collect();
        $now = now();

        $nextSession = $sessions
            ->filter(function ($session) use ($now) {
                return $session->start_at !== null && $session->start_at->greaterThan($now);
            })
            ->sortBy('start_at')
            ->first();

        return $nextSession ? $nextSession->start_at : null;
    }

    /**
     * Estimate progress from class status.
     */
    protected function estimateProgress(CourseClass $classItem): int
    {
        $status = $this->computedClassStatus($classItem);
        if ($status === 'completed') {
            return 100;
        }
        if ($status === 'upcoming') {
            return 5;
        }

        return 62;
    }

    /**
     * Estimate attendance from pivot.
     */
    protected function estimateAttendanceRateFromPivot(CourseClass $classItem): int
    {
        $pivotStatus = (string) optional($classItem->pivot)->status;
        if (in_array($pivotStatus, ['completed', 'present', 'active'], true)) {
            return 92;
        }

        if ($pivotStatus === 'pending') {
            return 0;
        }

        return 78;
    }

    /**
     * Build schedule list from class sessions.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function buildSessions(CourseClass $courseClass): Collection
    {
        $sessions = $courseClass->sessions instanceof Collection ? $courseClass->sessions : collect();

        return $sessions
            ->filter(function ($session) {
                return $session->start_at !== null;
            })
            ->sortBy('start_at')
            ->values()
            ->map(function ($session, int $index) use ($courseClass) {
                $startAt = $session->start_at;
                $endAt = $session->end_at ?: (clone $startAt)->addHours(2);

                return [
                    'title' => 'Buổi ' . ($index + 1),
                    'start_at' => optional($startAt)->format('d/m/Y H:i'),
                    'end_at' => optional($endAt)->format('H:i'),
                    'status' => $this->resolveSessionStatus($startAt, $endAt),
                    'location' => $courseClass->location ?: 'Online',
                    'join_url' => (string) ($session->join_url ?: '#'),
                ];
            });
    }

    /**
     * Resolve one class session status by time.
     *
     * @param  mixed  $startAt
     * @param  mixed  $endAt
     * @return string
     */
    protected function resolveSessionStatus($startAt, $endAt): string
    {
        $now = now();

        if ($endAt && $endAt->isPast()) {
            return 'completed';
        }

        if ($startAt && $startAt->isFuture()) {
            return 'upcoming';
        }

        if ($startAt && $endAt && $startAt->lessThanOrEqualTo($now) && $endAt->greaterThanOrEqualTo($now)) {
            return 'ongoing';
        }

        return 'ongoing';
    }

    /**
     * Build learning materials list.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function buildMaterials(CourseClass $courseClass): Collection
    {
        return collect([
            ['type' => 'Slide', 'name' => 'Introduction & Setup', 'url' => '#'],
            ['type' => 'PDF', 'name' => 'Class handbook', 'url' => '#'],
            ['type' => 'Source code', 'name' => 'Sample project repository', 'url' => '#'],
            ['type' => 'Recording', 'name' => 'Session recordings (coming soon)', 'url' => '#'],
        ]);
    }

    /**
     * Build attendance timeline.
     *
     * @return \Illuminate\Support\Collection<int, array<string, string>>
     */
    protected function buildAttendanceTimeline(int $rate): Collection
    {
        $statuses = ['present', 'present', 'late', 'present', 'absent', 'present', 'present', 'present'];

        return collect($statuses)->map(function (string $status, int $index) {
            return [
                'date' => now()->subDays((7 - $index) * 2)->format('d/m/Y'),
                'status' => $status,
            ];
        });
    }
}

