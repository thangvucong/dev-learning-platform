<?php

namespace App\Services\Teacher;

use App\Models\ClassSession;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Support\Collection;

class TeacherDashboardService
{
    /**
     * Build dashboard payload for an instructor.
     *
     * @param  \App\Models\User  $teacher
     * @return array<string, mixed>
     */
    public function build(User $teacher): array
    {
        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $todaySessions = $this->sessionsForTeacher($teacher)
            ->whereBetween('start_at', [$todayStart, $todayEnd])
            ->with([
                'courseClass' => function ($query) {
                    $query->with('course')->withCount('classEnrollments');
                },
                'attendances',
            ])
            ->orderBy('start_at')
            ->get();

        $monthSessions = $this->sessionsForTeacher($teacher)
            ->whereBetween('start_at', [$monthStart, $monthEnd])
            ->with(['attendances'])
            ->get();

        $nextSession = $this->sessionsForTeacher($teacher)
            ->where('start_at', '>=', $now)
            ->with([
                'courseClass' => function ($query) {
                    $query->with('course')->withCount('classEnrollments');
                },
                'attendances',
            ])
            ->orderBy('start_at')
            ->first();

        $classes = CourseClass::query()
            ->where('instructor_id', $teacher->id)
            ->with(['course', 'sessions'])
            ->withCount(['classEnrollments as students_count', 'sessions as sessions_count'])
            ->orderBy('start_at')
            ->get();

        $activeClassCount = $classes->whereIn('status', [
            CourseClass::STATUS_ONGOING,
            CourseClass::STATUS_UPCOMING,
        ])->count();
        $studentCount = $classes->sum('students_count');
        $pendingAttendanceCount = $this->pendingAttendanceSessions($teacher)->count();
        $monthHours = $this->calculateTeachingHours($monthSessions);

        return [
            'welcome' => [
                'name' => $teacher->name,
                'avatar' => $teacher->avatar_url,
                'greeting' => 'Chào giảng viên',
                'today_sessions' => $todaySessions->count(),
                'month_hours' => $monthHours,
            ],
            'stats' => [
                [
                    'title' => 'Buổi hôm nay',
                    'value' => $todaySessions->count(),
                    'suffix' => 'buổi',
                    'icon' => 'fa-solid fa-calendar-day',
                    'tone' => 'emerald',
                ],
                [
                    'title' => 'Lớp đang dạy',
                    'value' => $activeClassCount,
                    'suffix' => 'lớp',
                    'icon' => 'fa-solid fa-chalkboard-user',
                    'tone' => 'blue',
                ],
                [
                    'title' => 'Học viên',
                    'value' => $studentCount,
                    'suffix' => 'bạn',
                    'icon' => 'fa-solid fa-users',
                    'tone' => 'violet',
                ],
                [
                    'title' => 'Chưa điểm danh',
                    'value' => $pendingAttendanceCount,
                    'suffix' => 'buổi',
                    'icon' => 'fa-solid fa-clipboard-check',
                    'tone' => 'amber',
                ],
            ],
            'next_session' => $nextSession ? $this->mapSession($nextSession, true) : null,
            'today_sessions' => $todaySessions->map(function (ClassSession $session) {
                return $this->mapSession($session);
            })->values(),
            'classes' => $classes->take(5)->map(function (CourseClass $courseClass) {
                return $this->mapClass($courseClass);
            })->values(),
            'action_items' => $this->buildActionItems($pendingAttendanceCount, $todaySessions, $classes),
        ];
    }

    protected function sessionsForTeacher(User $teacher)
    {
        return ClassSession::query()
            ->whereHas('courseClass', function ($query) use ($teacher) {
                $query->where('instructor_id', $teacher->id);
            });
    }

    protected function pendingAttendanceSessions(User $teacher): Collection
    {
        return $this->sessionsForTeacher($teacher)
            ->where('end_at', '<=', now())
            ->where('status', '!=', ClassSession::STATUS_CANCELLED)
            ->doesntHave('attendances')
            ->get();
    }

    protected function calculateTeachingHours(Collection $sessions): float
    {
        $minutes = $sessions->sum(function (ClassSession $session) {
            if (!$session->start_at || !$session->end_at) {
                return 0;
            }

            return max(0, $session->start_at->diffInMinutes($session->end_at));
        });

        return round($minutes / 60, 1);
    }

    protected function mapSession(ClassSession $session, bool $includeCourse = false): array
    {
        $courseClass = $session->courseClass;
        $attendanceCount = $session->attendances instanceof Collection ? $session->attendances->count() : 0;
        $studentCount = $courseClass ? (int) ($courseClass->class_enrollments_count ?? 0) : 0;

        return [
            'id' => $session->id,
            'title' => $session->title ?: 'Buổi ' . $session->session_no,
            'class_name' => optional($courseClass)->name ?: 'Lớp học',
            'course_name' => $includeCourse ? optional(optional($courseClass)->course)->title : null,
            'start_at' => $session->start_at,
            'end_at' => $session->end_at,
            'time' => $this->formatSessionTime($session),
            'status' => $this->resolveSessionStatus($session),
            'meeting_type' => $session->meeting_type ?: optional($courseClass)->mode ?: 'offline',
            'meeting_info' => $session->meeting_info ?: optional($courseClass)->location ?: 'Online',
            'join_url' => (string) ($session->join_url ?: ''),
            'attendance_count' => $attendanceCount,
            'student_count' => $studentCount,
            'attendance_label' => $studentCount > 0 ? $attendanceCount . '/' . $studentCount : '0',
        ];
    }

    protected function mapClass(CourseClass $courseClass): array
    {
        $sessions = $courseClass->sessions instanceof Collection ? $courseClass->sessions : collect();
        $nextSession = $sessions
            ->filter(function ($session) {
                return $session->start_at !== null && $session->start_at->isFuture();
            })
            ->sortBy('start_at')
            ->first();
        $completedSessions = $sessions->filter(function ($session) {
            return $this->isCompletedSession($session);
        })->count();
        $progress = $sessions->isNotEmpty()
            ? (int) round(($completedSessions / max(1, $sessions->count())) * 100)
            : ($courseClass->status === CourseClass::STATUS_COMPLETED ? 100 : 0);

        return [
            'id' => $courseClass->id,
            'name' => $courseClass->name,
            'course_name' => optional($courseClass->course)->title ?: 'Khóa học',
            'status' => $courseClass->status,
            'students_count' => (int) $courseClass->students_count,
            'sessions_count' => (int) $courseClass->sessions_count,
            'progress' => $progress,
            'next_session' => optional(optional($nextSession)->start_at)->format('d/m H:i') ?: 'Chưa có lịch',
        ];
    }

    protected function buildActionItems(int $pendingAttendanceCount, Collection $todaySessions, Collection $classes): Collection
    {
        $items = collect();

        if ($pendingAttendanceCount > 0) {
            $items->push([
                'title' => 'Hoàn tất điểm danh',
                'description' => $pendingAttendanceCount . ' buổi đã kết thúc chưa có bản ghi điểm danh.',
                'icon' => 'fa-solid fa-clipboard-check',
                'tone' => 'amber',
            ]);
        }

        $missingLinks = $todaySessions->filter(function (ClassSession $session) {
            return $session->meeting_type === ClassSession::MEETING_ZOOM && empty($session->join_url);
        })->count();
        if ($missingLinks > 0) {
            $items->push([
                'title' => 'Bổ sung link lớp',
                'description' => $missingLinks . ' buổi online hôm nay chưa có link vào lớp.',
                'icon' => 'fa-solid fa-link-slash',
                'tone' => 'red',
            ]);
        }

        $emptyClasses = $classes->filter(function (CourseClass $courseClass) {
            return (int) $courseClass->students_count === 0;
        })->count();
        if ($emptyClasses > 0) {
            $items->push([
                'title' => 'Kiểm tra danh sách lớp',
                'description' => $emptyClasses . ' lớp hiện chưa có học viên.',
                'icon' => 'fa-solid fa-users-slash',
                'tone' => 'blue',
            ]);
        }

        if ($items->isEmpty()) {
            $items->push([
                'title' => 'Không có việc gấp',
                'description' => 'Lịch dạy và dữ liệu lớp học hiện không có cảnh báo cần xử lý ngay.',
                'icon' => 'fa-solid fa-circle-check',
                'tone' => 'emerald',
            ]);
        }

        return $items->take(3)->values();
    }

    protected function formatSessionTime(ClassSession $session): string
    {
        if (!$session->start_at) {
            return 'Đang cập nhật';
        }

        $end = $session->end_at ? ' - ' . $session->end_at->format('H:i') : '';

        return $session->start_at->format('H:i') . $end;
    }

    protected function resolveSessionStatus(ClassSession $session): string
    {
        if ($session->status === ClassSession::STATUS_CANCELLED) {
            return 'cancelled';
        }

        if ($session->start_at && $session->start_at->isFuture()) {
            return 'upcoming';
        }

        if ($session->start_at && $session->end_at && $session->start_at->lessThanOrEqualTo(now()) && $session->end_at->greaterThanOrEqualTo(now())) {
            return 'live';
        }

        return 'completed';
    }

    protected function isCompletedSession(ClassSession $session): bool
    {
        if ($session->status === ClassSession::STATUS_COMPLETED) {
            return true;
        }

        return $session->end_at !== null && $session->end_at->isPast();
    }
}
