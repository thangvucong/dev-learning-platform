<?php

namespace App\Services\Student;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StudentDashboardService
{
    /**
     * Build dashboard payload for authenticated student.
     *
     * @param  \App\Models\User  $user
     * @return array<string, mixed>
     */
    public function build(User $user): array
    {
        $user->loadMissing([
            'assignedClasses' => function ($query) use ($user) {
                $query->with([
                    'course',
                    'instructor',
                    'sessions.attendances' => function ($attendanceQuery) use ($user) {
                        $attendanceQuery->where('student_id', $user->id);
                    },
                ])->orderBy('start_at');
            },
            'enrolledCourses',
        ]);

        $assignedClasses = $user->assignedClasses instanceof Collection ? $user->assignedClasses : collect();
        $enrolledCourses = $user->enrolledCourses instanceof Collection ? $user->enrolledCourses : collect();

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $todaySchedule = $assignedClasses
            ->flatMap(function ($classItem) use ($todayStart, $todayEnd) {
                if (isset($classItem->sessions) && $classItem->sessions instanceof Collection && $classItem->sessions->isNotEmpty()) {
                    return $classItem->sessions
                        ->filter(function ($session) use ($todayStart, $todayEnd) {
                            return $session->start_at !== null && $session->start_at->between($todayStart, $todayEnd);
                        })
                        ->map(function ($session) use ($classItem) {
                            return [
                                'id' => $session->id,
                                'class_name' => $classItem->name,
                                'course_name' => optional($classItem->course)->title ?: 'Lớp học',
                                'teacher_name' => optional($classItem->instructor)->name ?: 'Giảng viên',
                                'start_time' => optional($session->start_at)->format('H:i'),
                                'end_time' => optional($session->end_at)->format('H:i'),
                                'location' => $classItem->location ?: 'Online',
                                'status' => $this->resolveSessionStatus($session->start_at, $session->end_at),
                                'join_url' => (string) ($session->join_url ?? ''),
                            ];
                        });
                }

                if ($classItem->start_at !== null && $classItem->start_at->between($todayStart, $todayEnd)) {
                    return collect([[
                        'id' => $classItem->id,
                        'class_name' => $classItem->name,
                        'course_name' => optional($classItem->course)->title ?: 'Lớp học',
                        'teacher_name' => optional($classItem->instructor)->name ?: 'Giảng viên',
                        'start_time' => optional($classItem->start_at)->format('H:i'),
                        'end_time' => optional($classItem->end_at)->format('H:i'),
                        'location' => $classItem->location ?: 'Online',
                        'status' => $this->resolveClassStatus($classItem->status, $classItem->start_at, $classItem->end_at),
                        'join_url' => '',
                    ]]);
                }

                return collect();
            })
            ->sortBy(function (array $item) {
                return (string) ($item['start_time'] ?? '');
            })
            ->values();

        $upcomingClasses = $assignedClasses
            ->map(function ($classItem) {
                $nextSession = $this->resolveNextSession($classItem);
                $classItem->dashboard_next_session = $nextSession;
                $classItem->dashboard_next_session_at = $nextSession ? $nextSession->start_at : null;

                return $classItem;
            })
            ->filter(function ($classItem) {
                return $classItem->dashboard_next_session_at !== null
                    && $classItem->dashboard_next_session_at->greaterThan(now());
            })
            ->sortBy(function ($classItem) {
                return $classItem->dashboard_next_session_at;
            })
            ->take(6)
            ->values()
            ->map(function ($classItem) {
                $nextSession = $classItem->dashboard_next_session;

                return [
                    'id' => $classItem->id,
                    'name' => $classItem->name,
                    'course' => optional($classItem->course)->title ?: 'Khóa học',
                    'teacher' => optional($classItem->instructor)->name ?: 'Giảng viên',
                    'next_session' => optional($classItem->dashboard_next_session_at)->format('d/m/Y H:i'),
                    'thumbnail' => optional($classItem->course)->thumbnail_url,
                    'progress' => $this->calculateClassProgress($classItem),
                    'status' => $this->resolveClassStatus($classItem->status, $classItem->dashboard_next_session_at, null),
                    'join_url' => (string) data_get($nextSession, 'join_url', ''),
                ];
            });

        $learningCourses = $enrolledCourses
            ->take(6)
            ->values()
            ->map(function ($courseItem) use ($assignedClasses) {
                $courseClasses = $assignedClasses
                    ->where('course_id', $courseItem->id)
                    ->values();
                $firstClass = $courseClasses->first();
                $progress = $this->calculateCourseProgress($courseItem->pivot->status ?? null, $courseClasses);

                return [
                    'id' => $courseItem->id,
                    'title' => $courseItem->title,
                    'teacher' => optional(optional($firstClass)->instructor)->name ?: 'Giảng viên',
                    'thumbnail' => $courseItem->thumbnail_url,
                    'status' => (string) ($courseItem->pivot->status ?? 'learning'),
                    'progress' => $progress,
                ];
            });

        $activeClassesCount = $assignedClasses
            ->filter(function ($classItem) {
                return in_array($classItem->status, ['ongoing', 'upcoming'], true);
            })
            ->count();

        $progressAverage = (int) round($learningCourses->avg('progress') ?: 0);
        $attendanceRate = $this->calculateAttendanceRate($assignedClasses);

        return [
            'welcome' => [
                'name' => $user->name,
                'avatar' => $user->avatar_url,
                'greeting' => $this->resolveGreeting(),
                'today_classes' => $todaySchedule->count(),
            ],
            'stats' => [
                [
                    'title' => 'Lớp đang học',
                    'value' => $activeClassesCount,
                    'suffix' => 'lớp',
                    'icon' => 'fa-solid fa-chalkboard-user',
                    'tone' => 'emerald',
                ],
                [
                    'title' => 'Buổi học hôm nay',
                    'value' => $todaySchedule->count(),
                    'suffix' => 'buổi',
                    'icon' => 'fa-solid fa-calendar-day',
                    'tone' => 'blue',
                ],
                [
                    'title' => 'Tiến độ học tập',
                    'value' => $progressAverage,
                    'suffix' => '%',
                    'icon' => 'fa-solid fa-chart-line',
                    'tone' => 'violet',
                ],
                [
                    'title' => 'Điểm danh',
                    'value' => $attendanceRate,
                    'suffix' => '%',
                    'icon' => 'fa-solid fa-user-check',
                    'tone' => 'amber',
                ],
            ],
            'today_schedule' => $todaySchedule,
            'upcoming_classes' => $upcomingClasses,
            'learning_courses' => $learningCourses,
        ];
    }

    /**
     * Resolve greeting message by current time.
     *
     * @return string
     */
    protected function resolveGreeting(): string
    {
        $hour = (int) now()->format('H');
        if ($hour < 12) {
            return 'Chào buổi sáng';
        }

        if ($hour < 18) {
            return 'Chào buổi chiều';
        }

        return 'Chào buổi tối';
    }

    /**
     * Resolve class status label.
     *
     * @param  string|null  $status
     * @param  \Illuminate\Support\Carbon|null  $startAt
     * @param  \Illuminate\Support\Carbon|null  $endAt
     * @return string
     */
    protected function resolveClassStatus(?string $status, ?Carbon $startAt, ?Carbon $endAt): string
    {
        if ($status !== null && $status !== '') {
            return $status;
        }

        if ($startAt && $startAt->isFuture()) {
            return 'upcoming';
        }

        if ($endAt && $endAt->isPast()) {
            return 'completed';
        }

        return 'ongoing';
    }

    /**
     * Resolve status of one session for dashboard card.
     *
     * @param  \Illuminate\Support\Carbon|null  $startAt
     * @param  \Illuminate\Support\Carbon|null  $endAt
     * @return string
     */
    protected function resolveSessionStatus(?Carbon $startAt, ?Carbon $endAt): string
    {
        if ($startAt && $startAt->isFuture()) {
            return 'upcoming';
        }

        if ($endAt && $endAt->isPast()) {
            return 'completed';
        }

        return 'live';
    }

    /**
     * Calculate progress for one class card from real sessions.
     *
     * @param  mixed  $classItem
     * @return int
     */
    protected function calculateClassProgress($classItem): int
    {
        $sessions = $classItem->sessions instanceof Collection ? $classItem->sessions : collect();

        if ($sessions->isEmpty()) {
            if ($classItem->status === 'completed') {
                return 100;
            }

            return 0;
        }

        $completedSessions = $sessions->filter(function ($session) {
            return $this->isCompletedSession($session);
        })->count();

        return (int) round(($completedSessions / max(1, $sessions->count())) * 100);
    }

    /**
     * Determine if one session should count as completed for progress.
     *
     * @param  mixed  $session
     * @return bool
     */
    protected function isCompletedSession($session): bool
    {
        if ((string) ($session->status ?? '') === 'completed') {
            return true;
        }

        return $session->end_at instanceof Carbon && $session->end_at->isPast();
    }

    /**
     * Calculate course progress from assigned class sessions.
     *
     * @param  string|null  $enrollmentStatus
     * @param  \Illuminate\Support\Collection  $courseClasses
     * @return int
     */
    protected function calculateCourseProgress(?string $enrollmentStatus, Collection $courseClasses): int
    {
        $sessions = $courseClasses
            ->flatMap(function ($classItem) {
                return $classItem->sessions instanceof Collection ? $classItem->sessions : collect();
            })
            ->values();

        if ($sessions->isEmpty()) {
            return $enrollmentStatus === 'completed' ? 100 : 0;
        }

        $completedSessions = $sessions->filter(function ($session) {
            return $this->isCompletedSession($session);
        })->count();

        return (int) round(($completedSessions / max(1, $sessions->count())) * 100);
    }

    /**
     * Calculate attendance percentage from saved attendance records.
     *
     * @param  \Illuminate\Support\Collection  $assignedClasses
     * @return int
     */
    protected function calculateAttendanceRate(Collection $assignedClasses): int
    {
        $attendanceRecords = $assignedClasses
            ->flatMap(function ($classItem) {
                $sessions = $classItem->sessions instanceof Collection ? $classItem->sessions : collect();

                return $sessions->flatMap(function ($session) {
                    return $session->attendances instanceof Collection ? $session->attendances : collect();
                });
            })
            ->values();

        if ($attendanceRecords->isEmpty()) {
            return 0;
        }

        $attendedStatuses = ['present', 'late'];
        $attendedCount = $attendanceRecords->filter(function ($attendance) use ($attendedStatuses) {
            return in_array((string) $attendance->status, $attendedStatuses, true);
        })->count();

        return (int) round(($attendedCount / max(1, $attendanceRecords->count())) * 100);
    }

    /**
     * Resolve next session entity for one class.
     *
     * @param  mixed  $classItem
     * @return mixed|null
     */
    protected function resolveNextSession($classItem)
    {
        $now = now();

        if (isset($classItem->sessions) && $classItem->sessions instanceof Collection) {
            $nextSession = $classItem->sessions
                ->filter(function ($session) use ($now) {
                    return $session->start_at !== null && $session->start_at->greaterThan($now);
                })
                ->sortBy('start_at')
                ->first();

            if ($nextSession && $nextSession->start_at instanceof Carbon) {
                return $nextSession;
            }
        }

        return null;
    }
}
