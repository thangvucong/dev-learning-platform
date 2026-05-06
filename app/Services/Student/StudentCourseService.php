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
            ->with(['instructor', 'classes'])
            ->get();

        if ($courses->isEmpty()) {
            $courses = $this->buildMockCourses();
        }

        $keyword = trim((string) ($filters['q'] ?? ''));
        $statusFilter = trim((string) ($filters['status'] ?? ''));

        $mappedCourses = $courses->map(function ($course) use ($user) {
            $metrics = $this->resolveCourseMetrics($course, $user);

            return [
                'id' => (int) $course->id,
                'title' => (string) $course->title,
                'thumbnail' => $course->thumbnail_url,
                'teacher' => optional($course->instructor)->name ?: 'Mentor',
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
            ->with(['instructor', 'classes'])
            ->first();

        if (!$course) {
            $course = $this->buildMockCourses()->firstWhere('id', $courseId);
        }

        abort_if(!$course, 404);

        $metrics = $this->resolveCourseMetrics($course, $user);
        $roadmap = $this->buildRoadmapItems($metrics['status']);

        return [
            'course' => [
                'id' => (int) $course->id,
                'title' => (string) $course->title,
                'description' => (string) ($course->description ?: 'Khóa học theo định hướng project-based learning roadmap.'),
                'thumbnail' => $course->thumbnail_url,
                'teacher' => optional($course->instructor)->name ?: 'Mentor',
                'teacher_email' => optional($course->instructor)->email,
                'progress' => $metrics['progress'],
                'attendance_rate' => $metrics['attendance_rate'],
                'status' => $metrics['status'],
                'next_session' => $metrics['next_session'],
                'estimated_completion' => $metrics['estimated_completion'],
                'modules_total' => $roadmap->count(),
                'modules_completed' => $roadmap->where('state', 'completed')->count(),
                'classes_total' => $course->classes->count(),
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
                    ['label' => 'Attendance', 'value' => $metrics['attendance_rate'] . '%'],
                    ['label' => 'Buổi hoàn thành', 'value' => $metrics['completed_sessions'] . '/' . $metrics['total_sessions']],
                    ['label' => 'Lớp thuộc khóa', 'value' => (string) $course->classes->count()],
                ],
            ],
            'roadmap' => $roadmap,
            'classes' => $this->mapCourseClasses($course),
            'materials' => $this->buildMaterials(),
            'progress' => [
                'study_streak' => $metrics['status'] === 'ongoing' ? 8 : 2,
                'estimated_completion' => $metrics['estimated_completion'],
                'timeline' => $this->buildProgressTimeline($metrics['progress']),
            ],
        ];
    }

    /**
     * Resolve course metrics.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\User  $user
     * @return array<string, mixed>
     */
    protected function resolveCourseMetrics(Course $course, User $user): array
    {
        $pivotStatus = (string) optional($course->pivot)->status;
        $status = in_array($pivotStatus, ['ongoing', 'completed', 'not_started'], true)
            ? $pivotStatus
            : 'ongoing';

        $progress = $status === 'completed' ? 100 : ($status === 'not_started' ? 0 : 64);
        $attendanceRate = $status === 'completed' ? 95 : ($status === 'not_started' ? 0 : 82);

        $totalSessions = max(8, ($course->classes->count() * 4));
        $completedSessions = $status === 'completed' ? $totalSessions : (int) floor(($progress / 100) * $totalSessions);
        $nextSession = $status === 'completed'
            ? 'Đã hoàn thành'
            : now()->addDays(2)->format('d/m/Y H:i');

        return [
            'status' => $status,
            'progress' => $progress,
            'attendance_rate' => $attendanceRate,
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'next_session' => $nextSession,
            'estimated_completion' => now()->addWeeks(5)->format('d/m/Y'),
        ];
    }

    /**
     * Build roadmap modules by course status.
     *
     * @param  string  $status
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function buildRoadmapItems(string $status): Collection
    {
        $states = $status === 'completed'
            ? ['completed', 'completed', 'completed', 'completed']
            : ($status === 'not_started'
                ? ['current', 'locked', 'locked', 'locked']
                : ['completed', 'completed', 'current', 'locked']);

        return collect([
            ['title' => 'Nền tảng kỹ thuật', 'subtitle' => 'HTML/CSS + Git workflow'],
            ['title' => 'JavaScript Core', 'subtitle' => 'Logic, async flow, API integration'],
            ['title' => 'Framework Track', 'subtitle' => 'React/Laravel feature implementation'],
            ['title' => 'Deploy & Scale', 'subtitle' => 'Testing, deployment, CI basics'],
        ])->map(function (array $item, int $index) use ($states) {
            return [
                'title' => $item['title'],
                'subtitle' => $item['subtitle'],
                'state' => $states[$index] ?? 'locked',
                'sessions' => 4 + $index,
            ];
        });
    }

    /**
     * Map classes list for course detail tab.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function mapCourseClasses(Course $course): Collection
    {
        if ($course->classes->isEmpty()) {
            return collect([
                [
                    'name' => 'Cohort A - Evening',
                    'mentor' => optional($course->instructor)->name ?: 'Mentor',
                    'schedule' => 'Thứ 3, Thứ 5 - 19:00',
                    'location' => 'Online - Zoom',
                ],
            ]);
        }

        return $course->classes->map(function ($classItem) use ($course) {
            return [
                'name' => (string) $classItem->name,
                'mentor' => optional($course->instructor)->name ?: 'Mentor',
                'schedule' => optional($classItem->start_at)->format('d/m/Y H:i') ?: 'Đang cập nhật',
                'location' => (string) ($classItem->location ?: 'Online'),
            ];
        })->values();
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

    /**
     * Build fallback mock courses.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\Course>
     */
    protected function buildMockCourses(): Collection
    {
        $teacher = new \stdClass();
        $teacher->name = 'Nguyen Van Mentor';
        $teacher->email = 'mentor@example.com';

        return collect([
            [
                'id' => 901,
                'title' => 'Laravel REST API Journey',
                'description' => 'Lộ trình backend API theo mindset production.',
                'thumbnail_url' => null,
                'pivot_status' => 'ongoing',
            ],
            [
                'id' => 902,
                'title' => 'Frontend Roadmap Bootcamp',
                'description' => 'Xây nền tảng frontend hiện đại từ cơ bản đến dự án.',
                'thumbnail_url' => null,
                'pivot_status' => 'not_started',
            ],
            [
                'id' => 903,
                'title' => 'System Design Foundation',
                'description' => 'Tư duy thiết kế hệ thống cho sản phẩm thực tế.',
                'thumbnail_url' => null,
                'pivot_status' => 'completed',
            ],
        ])->map(function (array $item) use ($teacher) {
            $course = new Course();
            $course->id = $item['id'];
            $course->title = $item['title'];
            $course->description = $item['description'];
            $course->thumbnail_url = $item['thumbnail_url'];
            $course->setRelation('instructor', $teacher);
            $course->setRelation('classes', collect());
            $course->setRelation('pivot', (object) ['status' => $item['pivot_status']]);

            return $course;
        });
    }
}

