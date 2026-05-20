<?php

namespace App\Services\Student;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;

class StudentCourseService
{
    /**
     * Build list payload for student courses page.
     *
     * @param  \App\Models\User  $user
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function buildList(User $user, array $filters): array
    {
        $courses = $user->enrolledCourses()
            ->with(['tracks'])
            ->get();
        $assignedClassesByCourse = $this->assignedClassesByCourse($user);

        $keyword = trim((string) ($filters['q'] ?? ''));
        $statusFilter = trim((string) ($filters['status'] ?? ''));

        $mappedCourses = $courses->map(function ($course) use ($assignedClassesByCourse) {
            $courseClasses = $assignedClassesByCourse->get($course->id, collect());
            $metrics = $this->resolveCourseMetrics($course, $courseClasses);

            return [
                'id' => (int) $course->id,
                'title' => (string) $course->title,
                'thumbnail' => $course->thumbnail_url,
                'teacher' => $this->resolveCourseTeacher($courseClasses),
                'progress' => $metrics['progress'],
                'attendance_rate' => $metrics['attendance_rate'],
                'completed_sessions' => $metrics['completed_sessions'],
                'total_sessions' => $metrics['total_sessions'],
                'status' => $metrics['status'],
                'next_session' => $metrics['next_session'],
                'continue_label' => 'Tiếp tục học ' . (string) $course->title,
            ];
        });

        if ($keyword !== '') {
            $mappedCourses = $mappedCourses->filter(function (array $courseItem) use ($keyword) {
                $haystack = mb_strtolower(implode(' ', [
                    (string) ($courseItem['title'] ?? ''),
                    (string) ($courseItem['teacher'] ?? ''),
                ]));

                return mb_stripos($haystack, mb_strtolower($keyword)) !== false;
            })->values();
        }

        if ($statusFilter !== '' && in_array($statusFilter, ['ongoing', 'completed', 'not_started'], true)) {
            $mappedCourses = $mappedCourses->where('status', $statusFilter)->values();
        }

        $continueCourse = $mappedCourses
            ->where('status', 'ongoing')
            ->sortByDesc('progress')
            ->first();

        if (!$continueCourse) {
            $continueCourse = $mappedCourses->first();
        }

        return [
            'filters' => [
                'q' => $keyword,
                'status' => $statusFilter,
            ],
            'continue_course' => $continueCourse,
            'courses' => $mappedCourses,
            'stats' => [
                'total' => $mappedCourses->count(),
                'ongoing' => $mappedCourses->where('status', 'ongoing')->count(),
                'completed' => $mappedCourses->where('status', 'completed')->count(),
                'not_started' => $mappedCourses->where('status', 'not_started')->count(),
            ],
        ];
    }

    /**
     * Build detail payload for one course.
     *
     * @param  \App\Models\User  $user
     * @param  int  $courseId
     * @return array<string, mixed>
     */
    public function buildDetail(User $user, int $courseId): array
    {
        $course = $user->enrolledCourses()
            ->where('courses.id', $courseId)
            ->with(['tracks'])
            ->first();

        abort_if(!$course, 404);

        $courseClasses = $this->assignedClassesByCourse($user)->get($course->id, collect());
        $metrics = $this->resolveCourseMetrics($course, $courseClasses);
        $roadmap = $this->buildRoadmapItems($course, $metrics['progress']);
        $teacher = $this->resolveCourseTeacher($courseClasses);
        $teacherUser = optional($courseClasses->first())->instructor;

        return [
            'course' => [
                'id' => (int) $course->id,
                'title' => (string) $course->title,
                'description' => (string) ($course->description ?: 'Khóa học theo định hướng project-based learning roadmap.'),
                'thumbnail' => $course->thumbnail_url,
                'teacher' => $teacher,
                'teacher_email' => optional($teacherUser)->email,
                'progress' => $metrics['progress'],
                'attendance_rate' => $metrics['attendance_rate'],
                'status' => $metrics['status'],
                'next_session' => $metrics['next_session'],
                'estimated_completion' => $metrics['estimated_completion'],
                'modules_total' => $roadmap->count(),
                'modules_completed' => $roadmap->where('state', 'completed')->count(),
                'classes_total' => $courseClasses->count(),
                'sessions_completed' => $metrics['completed_sessions'],
                'sessions_total' => $metrics['total_sessions'],
            ],
            'overview' => [
                'description' => (string) ($course->description ?: 'Bạn sẽ xây nền tảng kỹ thuật, thực hành qua mini-project và hoàn thành capstone.'),
                'skills' => [
                    'Nắm chắc tư duy kiến trúc và luồng dữ liệu',
                    'Tự xây mini-feature theo chuẩn production',
                    'Làm chủ workflow học tập theo roadmap',
                ],
                'statistics' => [
                    ['label' => 'Tiến độ tổng', 'value' => $metrics['progress'] . '%'],
                    ['label' => 'Điểm danh', 'value' => $metrics['attendance_rate'] . '%'],
                    ['label' => 'Buổi hoàn thành', 'value' => $metrics['completed_sessions'] . '/' . $metrics['total_sessions']],
                    ['label' => 'Lớp thuộc khóa', 'value' => (string) $courseClasses->count()],
                ],
            ],
            'roadmap' => $roadmap,
            'classes' => $this->mapCourseClasses($courseClasses),
            'materials' => $this->buildMaterials(),
            'progress' => [
                'study_streak' => $this->calculateStudyStreak($courseClasses),
                'estimated_completion' => $metrics['estimated_completion'],
                'timeline' => $this->buildProgressTimeline($metrics['progress']),
            ],
        ];
    }

    /**
     * Resolve course metrics.
     *
     * @param  \App\Models\Course  $course
     * @param  \Illuminate\Support\Collection  $courseClasses
     * @return array<string, mixed>
     */
    protected function resolveCourseMetrics(Course $course, Collection $courseClasses): array
    {
        $pivotStatus = (string) optional($course->pivot)->status;
        $status = $this->normalizeCourseStatus($pivotStatus);

        $sessions = $courseClasses
            ->flatMap(function ($classItem) {
                return $classItem->sessions instanceof Collection ? $classItem->sessions : collect();
            })
            ->values();

        $totalSessions = $sessions->count();
        $completedSessions = $sessions->filter(function ($session) {
            return $this->isCompletedSession($session);
        })->count();

        $progress = $totalSessions > 0
            ? (int) round(($completedSessions / $totalSessions) * 100)
            : ($status === 'completed' ? 100 : 0);

        $attendanceRecords = $sessions
            ->flatMap(function ($session) {
                return $session->attendances instanceof Collection ? $session->attendances : collect();
            })
            ->values();
        $attendedCount = $attendanceRecords->filter(function ($attendance) {
            return in_array((string) $attendance->status, ['present', 'late'], true);
        })->count();
        $attendanceRate = $attendanceRecords->isNotEmpty()
            ? (int) round(($attendedCount / $attendanceRecords->count()) * 100)
            : 0;

        $nextSession = $this->resolveNextSessionText($sessions, $status);

        return [
            'status' => $status,
            'progress' => $progress,
            'attendance_rate' => $attendanceRate,
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'next_session' => $nextSession,
            'estimated_completion' => $this->resolveEstimatedCompletion($sessions, $status),
        ];
    }

    protected function assignedClassesByCourse(User $user): Collection
    {
        return $user->assignedClasses()
            ->with(['instructor', 'sessions.attendances' => function ($query) use ($user) {
                $query->where('student_id', $user->id);
            }])
            ->get()
            ->groupBy('course_id');
    }

    protected function normalizeCourseStatus(string $status): string
    {
        if (in_array($status, ['completed', 'not_started'], true)) {
            return $status;
        }

        if (in_array($status, ['active', 'ongoing'], true)) {
            return 'ongoing';
        }

        return 'not_started';
    }

    protected function isCompletedSession($session): bool
    {
        if ((string) ($session->status ?? '') === 'completed') {
            return true;
        }

        return $session->end_at !== null && $session->end_at->isPast();
    }

    protected function resolveCourseTeacher(Collection $courseClasses): string
    {
        $instructor = optional($courseClasses->first())->instructor;

        return optional($instructor)->name ?: 'Giảng viên';
    }

    protected function resolveNextSessionText(Collection $sessions, string $status): string
    {
        if ($status === 'completed') {
            return 'Đã hoàn thành';
        }

        $nextSession = $sessions
            ->filter(function ($session) {
                return $session->start_at !== null && $session->start_at->isFuture();
            })
            ->sortBy('start_at')
            ->first();

        return $nextSession ? $nextSession->start_at->format('d/m/Y H:i') : 'Đang cập nhật';
    }

    protected function resolveEstimatedCompletion(Collection $sessions, string $status): string
    {
        if ($status === 'completed') {
            return 'Đã hoàn thành';
        }

        $lastSession = $sessions
            ->filter(function ($session) {
                return $session->end_at !== null;
            })
            ->sortByDesc('end_at')
            ->first();

        return $lastSession ? $lastSession->end_at->format('d/m/Y') : 'Đang cập nhật';
    }

    /**
     * Build roadmap modules by course status.
     *
     * @param  string  $status
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function buildRoadmapItems(Course $course, int $progress): Collection
    {
        $tracks = $course->tracks instanceof Collection
            ? $course->tracks->sortBy('position')->values()
            : collect();

        if ($tracks->isEmpty()) {
            return collect();
        }

        $completedCount = (int) floor(($progress / 100) * $tracks->count());

        return $tracks->map(function ($track, int $index) use ($completedCount, $progress, $tracks) {
            $state = $index < $completedCount
                ? 'completed'
                : ($index === $completedCount && $progress < 100 ? 'current' : 'locked');

            if ($progress >= 100) {
                $state = 'completed';
            }

            return [
                'title' => (string) $track->title,
                'subtitle' => (string) ($track->description ?: 'Đang cập nhật'),
                'state' => $state,
                'sessions' => max(1, (int) ceil(12 / max(1, $tracks->count()))),
            ];
        });
    }

    /**
     * Map classes list for course detail tab.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function mapCourseClasses(Collection $courseClasses): Collection
    {
        return $courseClasses->map(function ($classItem) {
            return [
                'name' => (string) $classItem->name,
                'mentor' => optional($classItem->instructor)->name ?: 'Giảng viên',
                'schedule' => optional($classItem->start_at)->format('d/m/Y H:i') ?: 'Đang cập nhật',
                'location' => (string) ($classItem->location ?: 'Online'),
            ];
        })->values();
    }

    protected function calculateStudyStreak(Collection $courseClasses): int
    {
        $records = $courseClasses
            ->flatMap(function ($classItem) {
                $sessions = $classItem->sessions instanceof Collection ? $classItem->sessions : collect();

                return $sessions->flatMap(function ($session) {
                    return $session->attendances instanceof Collection ? $session->attendances : collect();
                });
            })
            ->sortByDesc('created_at')
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
     * Build materials tab data.
     *
     * @return \Illuminate\Support\Collection<int, array<string, string>>
     */
    protected function buildMaterials(): Collection
    {
        return collect([
            ['type' => 'PDF', 'name' => 'Learning handbook', 'status' => 'available'],
            ['type' => 'Slide', 'name' => 'Module slides', 'status' => 'available'],
            ['type' => 'Source code', 'name' => 'Practice repository', 'status' => 'available'],
            ['type' => 'Recording', 'name' => 'Session recording', 'status' => 'coming_soon'],
        ]);
    }

    /**
     * Build timeline points for progress tab.
     *
     * @param  int  $progress
     * @return \Illuminate\Support\Collection<int, array<string, string>>
     */
    protected function buildProgressTimeline(int $progress): Collection
    {
        $timeline = [
            ['label' => 'Kickoff', 'value' => '100%'],
            ['label' => 'Core Modules', 'value' => max(10, $progress - 18) . '%'],
            ['label' => 'Project Sprint', 'value' => max(5, $progress - 8) . '%'],
            ['label' => 'Capstone', 'value' => $progress . '%'],
        ];

        return collect($timeline);
    }

}
