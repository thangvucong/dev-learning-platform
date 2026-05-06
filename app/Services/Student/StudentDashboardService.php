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
            'assignedClasses' => function ($query) {
                $query->with(['course.instructor'])->orderBy('start_at');
            },
            'enrolledCourses' => function ($query) {
                $query->with(['instructor'])->orderByDesc('published_at');
            },
        ]);

        $assignedClasses = $user->assignedClasses instanceof Collection ? $user->assignedClasses : collect();
        $enrolledCourses = $user->enrolledCourses instanceof Collection ? $user->enrolledCourses : collect();

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $todaySchedule = $assignedClasses
            ->filter(function ($classItem) use ($todayStart, $todayEnd) {
                return $classItem->start_at !== null && $classItem->start_at->between($todayStart, $todayEnd);
            })
            ->values()
            ->map(function ($classItem) {
                return [
                    'id' => $classItem->id,
                    'class_name' => $classItem->name,
                    'course_name' => optional($classItem->course)->title ?: 'Lớp học',
                    'teacher_name' => optional(optional($classItem->course)->instructor)->name ?: 'Giảng viên',
                    'start_time' => optional($classItem->start_at)->format('H:i'),
                    'end_time' => optional($classItem->end_at)->format('H:i'),
                    'location' => $classItem->location ?: 'Online',
                    'status' => $this->resolveClassStatus($classItem->status, $classItem->start_at, $classItem->end_at),
                ];
            });

        $upcomingClasses = $assignedClasses
            ->filter(function ($classItem) {
                return $classItem->start_at !== null && $classItem->start_at->greaterThan(now());
            })
            ->sortBy('start_at')
            ->take(6)
            ->values()
            ->map(function ($classItem) {
                return [
                    'id' => $classItem->id,
                    'name' => $classItem->name,
                    'course' => optional($classItem->course)->title ?: 'Khóa học',
                    'teacher' => optional(optional($classItem->course)->instructor)->name ?: 'Giảng viên',
                    'next_session' => optional($classItem->start_at)->format('d/m/Y H:i'),
                    'thumbnail' => optional($classItem->course)->thumbnail_url,
                    'progress' => $this->estimateClassProgress($classItem),
                ];
            });

        $learningCourses = $enrolledCourses
            ->take(6)
            ->values()
            ->map(function ($courseItem) use ($user) {
                $progress = $this->estimateCourseProgress($courseItem->pivot->status ?? null);

                return [
                    'id' => $courseItem->id,
                    'title' => $courseItem->title,
                    'teacher' => optional($courseItem->instructor)->name ?: 'Giảng viên',
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
        $attendanceRate = $this->estimateAttendanceRate($assignedClasses);

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
                    'title' => 'Attendance',
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
     * Estimate progress for one class card.
     *
     * @param  mixed  $classItem
     * @return int
     */
    protected function estimateClassProgress($classItem): int
    {
        if ($classItem->status === 'completed') {
            return 100;
        }

        if ($classItem->start_at && $classItem->start_at->isFuture()) {
            return 0;
        }

        return 60;
    }

    /**
     * Estimate course progress based on enrollment status.
     *
     * @param  string|null  $enrollmentStatus
     * @return int
     */
    protected function estimateCourseProgress(?string $enrollmentStatus): int
    {
        if ($enrollmentStatus === 'completed') {
            return 100;
        }

        if ($enrollmentStatus === 'active') {
            return 65;
        }

        if ($enrollmentStatus === 'pending') {
            return 10;
        }

        return 45;
    }

    /**
     * Estimate attendance percentage.
     *
     * @param  \Illuminate\Support\Collection  $assignedClasses
     * @return int
     */
    protected function estimateAttendanceRate(Collection $assignedClasses): int
    {
        if ($assignedClasses->isEmpty()) {
            return 0;
        }

        $presentCount = $assignedClasses->filter(function ($classItem) {
            $pivotStatus = (string) optional($classItem->pivot)->status;

            return in_array($pivotStatus, ['active', 'present', 'completed'], true);
        })->count();

        return (int) round(($presentCount / max(1, $assignedClasses->count())) * 100);
    }
}

