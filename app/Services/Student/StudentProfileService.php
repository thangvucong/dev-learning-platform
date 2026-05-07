<?php

namespace App\Services\Student;

use App\Models\User;
use Illuminate\Support\Collection;

class StudentProfileService
{
    /**
     * Build learning profile payload.
     *
     * @param  \App\Models\User  $user
     * @return array<string, mixed>
     */
    public function build(User $user): array
    {
        $user->loadMissing([
            'enrolledCourses.instructor',
            'assignedClasses.course.instructor',
        ]);

        $learningCourses = $user->enrolledCourses instanceof Collection ? $user->enrolledCourses : collect();
        $assignedClasses = $user->assignedClasses instanceof Collection ? $user->assignedClasses : collect();

        $ongoingCourses = $learningCourses->filter(function ($course) {
            return (string) optional($course->pivot)->status !== 'completed';
        })->values();

        $attendanceRate = $assignedClasses->isEmpty() ? 0 : 84;
        $overallProgress = $ongoingCourses->isEmpty() ? 0 : 68;
        $studyStreak = $ongoingCourses->isEmpty() ? 0 : 11;
        $totalSessions = max(0, $assignedClasses->count() * 4);
        $completedSessions = (int) floor(($overallProgress / 100) * $totalSessions);
        $completionRate = $learningCourses->count() > 0
            ? (int) round(($learningCourses->filter(function ($course) {
                return (string) optional($course->pivot)->status === 'completed';
            })->count() / $learningCourses->count()) * 100)
            : 0;

        return [
            'profile' => [
                'name' => (string) $user->name,
                'email' => (string) $user->email,
                'avatar' => $user->avatar_url,
                'bio' => data_get($user, 'bio', 'Xây dựng thói quen học tập nhất quán thông qua thực hành dựa trên lộ trình.'),
                'phone' => data_get($user, 'phone', 'Đang cập nhật'),
                'dob' => data_get($user, 'date_of_birth', 'Đang cập nhật'),
                'join_date' => optional($user->created_at)->format('d/m/Y') ?: now()->format('d/m/Y'),
                'socials' => [
                    ['label' => 'GitHub', 'url' => data_get($user, 'social_github', '#')],
                    ['label' => 'LinkedIn', 'url' => data_get($user, 'social_linkedin', '#')],
                    ['label' => 'Portfolio', 'url' => data_get($user, 'social_portfolio', '#')],
                ],
                'learning_streak' => $studyStreak,
                'attendance_rate' => $attendanceRate,
                'overall_progress' => $overallProgress,
            ],
            'stats' => [
                ['label' => 'Khóa đang học', 'value' => $ongoingCourses->count(), 'icon' => 'fa-solid fa-book-open', 'tone' => 'emerald'],
                ['label' => 'Tổng lớp học', 'value' => $assignedClasses->count(), 'icon' => 'fa-solid fa-chalkboard-user', 'tone' => 'blue'],
                ['label' => 'Điểm danh', 'value' => $attendanceRate . '%', 'icon' => 'fa-solid fa-user-check', 'tone' => 'violet'],
                ['label' => 'Chuỗi học tập', 'value' => $studyStreak . ' ngày', 'icon' => 'fa-solid fa-fire', 'tone' => 'amber'],
                ['label' => 'Buổi đã học', 'value' => $completedSessions . '/' . $totalSessions, 'icon' => 'fa-solid fa-list-check', 'tone' => 'emerald'],
                ['label' => 'Hoàn thành', 'value' => $completionRate . '%', 'icon' => 'fa-solid fa-flag-checkered', 'tone' => 'blue'],
            ],
            'learning' => [
                'courses' => $ongoingCourses->map(function ($course) {
                    $status = (string) optional($course->pivot)->status;
                    $progress = $status === 'completed' ? 100 : ($status === 'not_started' ? 0 : 64);

                    return [
                        'title' => (string) $course->title,
                        'mentor' => optional($course->instructor)->name ?: 'Mentor',
                        'progress' => $progress,
                        'status' => $status ?: 'ongoing',
                    ];
                })->values(),
                'timeline' => $this->buildLearningTimeline(),
                'recent_modules' => collect([
                    'REST API Authentication',
                    'Database Relationship Mapping',
                    'Validation & Error Handling',
                ]),
                'upcoming_classes' => $assignedClasses->take(3)->map(function ($classItem) {
                    return [
                        'name' => (string) $classItem->name,
                        'start_at' => optional($classItem->start_at)->format('d/m/Y H:i') ?: 'Đang cập nhật',
                    ];
                })->values(),
            ],
            'achievements' => [
                'badges' => collect([
                    ['name' => 'Attendance Hero', 'description' => 'Duy trì attendance trên 80%'],
                    ['name' => 'Consistency Runner', 'description' => 'Học liên tục 7 ngày'],
                    ['name' => 'Module Finisher', 'description' => 'Hoàn thành 3 module gần nhất'],
                ]),
                'milestones' => collect([
                    'Hoàn thành milestone Backend Core',
                    'Đạt streak học tập 10 ngày',
                    'Xong 60% lộ trình hiện tại',
                ]),
                'completed_courses' => $learningCourses->filter(function ($course) {
                    return (string) optional($course->pivot)->status === 'completed';
                })->count(),
            ],
            'security' => [
                'recent_logins' => collect([
                    ['device' => 'Chrome on Linux', 'ip' => '127.0.0.1', 'time' => now()->subHours(3)->format('d/m/Y H:i')],
                    ['device' => 'Mobile Safari', 'ip' => '127.0.0.1', 'time' => now()->subDay()->format('d/m/Y H:i')],
                ]),
                'sessions_placeholder' => 'Session management sẽ được tích hợp ở iteration tiếp theo.',
            ],
            'settings' => [
                'timezone' => 'Asia/Ho_Chi_Minh',
                'notifications_enabled' => true,
                'weekly_report' => false,
            ],
        ];
    }

    /**
     * Build timeline for learning journey tab.
     *
     * @return \Illuminate\Support\Collection<int, array<string, string>>
     */
    protected function buildLearningTimeline(): Collection
    {
        return collect([
            ['title' => 'Bắt đầu roadmap', 'date' => now()->subMonths(2)->format('d/m/Y')],
            ['title' => 'Hoàn thành nền tảng', 'date' => now()->subMonth()->format('d/m/Y')],
            ['title' => 'Đang ở module nâng cao', 'date' => now()->subWeek()->format('d/m/Y')],
            ['title' => 'Mục tiêu capstone', 'date' => now()->addWeeks(3)->format('d/m/Y')],
        ]);
    }
}

