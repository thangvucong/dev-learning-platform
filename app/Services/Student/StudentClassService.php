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
            ->with([
                'course',
                'instructor',
                'sessions.attendances' => function ($query) use ($user) {
                    $query->where('student_id', $user->id);
                },
            ])
            ->orderBy('start_at')
            ->get();

        $keyword = trim((string) ($filters['q'] ?? ''));
        $statusFilter = trim((string) ($filters['status'] ?? ''));

        $mapped = $classes->map(function (CourseClass $classItem) {
            $computedStatus = $this->computedClassStatus($classItem);
            $attendanceSummary = $this->buildAttendanceSummary($classItem);
            $progress = $this->calculateProgress($classItem);
            $nextSessionAt = $this->resolveNextSessionStartAt($classItem);

            return [
                'id' => $classItem->id,
                'name' => $classItem->name,
                'code' => $classItem->code,
                'status' => $computedStatus,
                'thumbnail' => optional($classItem->course)->thumbnail_url,
                'teacher' => optional($classItem->instructor)->name ?: 'Giảng viên',
                'course_title' => optional($classItem->course)->title ?: 'Khóa học',
                'next_session' => optional($nextSessionAt)->format('d/m/Y H:i'),
                'progress' => $progress,
                'attendance_rate' => $attendanceSummary['rate'],
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
            ->with([
                'course',
                'instructor',
                'users',
                'sessions.attendances' => function ($query) use ($user) {
                    $query->where('student_id', $user->id);
                },
            ])
            ->firstOrFail();

        $status = $this->computedClassStatus($courseClass);
        $progress = $this->calculateProgress($courseClass);
        $attendanceSummary = $this->buildAttendanceSummary($courseClass);
        $sessions = $this->buildSessions($courseClass);
        $nextSessionAt = $this->resolveNextSessionStartAt($courseClass);
        $sessionCounts = $this->countSessions($courseClass);

        return [
            'class' => [
                'id' => $courseClass->id,
                'name' => $courseClass->name,
                'code' => $courseClass->code,
                'status' => $status,
                'description' => optional($courseClass->course)->description ?: 'Lớp học thực hành theo lộ trình học tập.',
                'thumbnail' => optional($courseClass->course)->thumbnail_url,
                'teacher' => optional($courseClass->instructor)->name ?: 'Giảng viên',
                'teacher_email' => optional($courseClass->instructor)->email,
                'course_title' => optional($courseClass->course)->title ?: 'Khóa học',
                'progress' => $progress,
                'attendance_rate' => $attendanceSummary['rate'],
                'next_session' => optional($nextSessionAt)->format('d/m/Y H:i'),
                'location' => $courseClass->location ?: 'Online',
            ],
            'overview' => [
                'total_members' => $courseClass->users->count(),
                'completed_sessions' => $sessionCounts['completed'],
                'remaining_sessions' => $sessionCounts['remaining'],
                'study_streak_days' => $this->calculateStudyStreak($courseClass),
            ],
            'schedules' => $sessions,
            'materials' => $this->buildMaterials($courseClass),
            'members' => [
                'teacher' => [
                    'name' => optional($courseClass->instructor)->name ?: 'Giảng viên',
                    'email' => optional($courseClass->instructor)->email,
                    'avatar' => optional($courseClass->instructor)->avatar_url,
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
                'rate' => $attendanceSummary['rate'],
                'present' => $attendanceSummary['present'],
                'absent' => $attendanceSummary['absent'],
                'late' => $attendanceSummary['late'],
                'timeline' => $this->buildAttendanceTimeline($courseClass),
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
     * Calculate progress from real class sessions.
     */
    protected function calculateProgress(CourseClass $classItem): int
    {
        $sessions = $classItem->sessions instanceof Collection ? $classItem->sessions : collect();

        if ($sessions->isEmpty()) {
            return $this->computedClassStatus($classItem) === 'completed' ? 100 : 0;
        }

        $completedSessions = $sessions->filter(function ($session) {
            return $this->isCompletedSession($session);
        })->count();

        return (int) round(($completedSessions / max(1, $sessions->count())) * 100);
    }

    protected function countSessions(CourseClass $classItem): array
    {
        $sessions = $classItem->sessions instanceof Collection ? $classItem->sessions : collect();
        $completed = $sessions->filter(function ($session) {
            return $this->isCompletedSession($session);
        })->count();

        return [
            'completed' => $completed,
            'remaining' => max(0, $sessions->count() - $completed),
        ];
    }

    protected function isCompletedSession($session): bool
    {
        if ((string) ($session->status ?? '') === 'completed') {
            return true;
        }

        return $session->end_at !== null && $session->end_at->isPast();
    }

    protected function buildAttendanceSummary(CourseClass $classItem): array
    {
        $records = $this->attendanceRecords($classItem);
        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $late = $records->where('status', 'late')->count();
        $absent = $records->where('status', 'absent')->count();
        $attended = $present + $late;

        return [
            'rate' => $total > 0 ? (int) round(($attended / $total) * 100) : 0,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
        ];
    }

    protected function attendanceRecords(CourseClass $classItem): Collection
    {
        $sessions = $classItem->sessions instanceof Collection ? $classItem->sessions : collect();

        return $sessions
            ->flatMap(function ($session) {
                return $session->attendances instanceof Collection ? $session->attendances : collect();
            })
            ->values();
    }

    protected function calculateStudyStreak(CourseClass $classItem): int
    {
        $records = $this->attendanceRecords($classItem)
            ->sortByDesc(function ($attendance) {
                return optional(optional($attendance->session)->start_at)->timestamp ?: optional($attendance->created_at)->timestamp;
            })
            ->values();

        $streak = 0;
        foreach ($records as $attendance) {
            if (!in_array((string) $attendance->status, ['present', 'late'], true)) {
                break;
            }

            $streak++;
        }

        return $streak;
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
    protected function buildAttendanceTimeline(CourseClass $classItem): Collection
    {
        return $this->attendanceRecords($classItem)
            ->sortBy(function ($attendance) {
                return optional(optional($attendance->session)->start_at)->timestamp ?: optional($attendance->created_at)->timestamp;
            })
            ->values()
            ->map(function ($attendance) {
                return [
                    'date' => optional(optional($attendance->session)->start_at)->format('d/m/Y') ?: optional($attendance->created_at)->format('d/m/Y'),
                    'status' => (string) $attendance->status,
                ];
            });
    }
}
