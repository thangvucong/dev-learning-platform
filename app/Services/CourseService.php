<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CourseService
{
    protected CourseRepositoryInterface $courseRepository;

    protected UserRepositoryInterface $userRepository;

    public function __construct(
        CourseRepositoryInterface $courseRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Dữ liệu view trang quản lý khóa học (admin).
     *
     * @return array<string, mixed>
     */
    public function getManagersCourseIndexViewData(): array
    {
        return [
            'instructors' => $this->userRepository->findTeachersForSelect(),
        ];
    }

    /**
     * Get source data for the course detail page.
     *
     * @param  string  $slug
     * @return array<string, mixed>
     */
    public function getCourseDetailSourceData(string $slug): ?array
    {
        try {
            $course = $this->courseRepository->findPublishedCourseDetailBySlug($slug);

            return [
                'course' => $course,
                'instructor' => $course->instructor,
                'classes' => $course->classes ?? [],
            ];
        } catch (\Throwable $th) {
            Log::error('Error getting course detail source data: ' . $th->getMessage());

            return [];
        }
    }

    public function getManagerListData(int $perPage = 10)
    {
        try {
            /** @var \Illuminate\Pagination\LengthAwarePaginator $courses */
            $courses = $this->courseRepository->getAllCoursesPaginated($perPage);

            $items = $courses->getCollection()->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->title,
                    'instructor' => $course->instructor->name ?? 'N/A',
                    'price' => number_format($course->price, 0, ',', '.') . 'đ',
                    'class_count' => $course->classes->count(),
                    'status' => $course->status == 1 ? 'Hiển thị' : 'Ẩn',
                    'classes' => $course->classes->map(function ($class) {
                        return [
                            'code' => $class->code ?? 'N/A',
                            'name' => $class->name,
                            'status' => $class->status,
                            'capacity' => $class->capacity ?? 0,
                            'current_students' => $class->users_count ?? 0,
                            'location' => $class->location ?? 'Chưa xác định',
                            'start_date' => $class->start_at ? date('d/m/Y', strtotime($class->start_at)) : 'N/A',
                        ];
                    })->values()->all(),
                ];
            });

            $courses->setCollection($items);

            return $courses;
        } catch (\Exception $e) {
            Log::error('Lỗi tại CourseService@getManagerListData: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tạo khóa học (admin), chỉ ghi vào bảng courses.
     *
     * @param  array<string, mixed>  $payload
     * @return \App\Models\Course
     */
    public function createCourseForAdmin(array $payload): Course
    {
        return DB::transaction(function () use ($payload) {
            $title = trim((string) Arr::get($payload, 'title', ''));
            $generatedSlug = Str::slug($title) . '-' . Str::lower(Str::random(6));
            $slug = trim((string) Arr::get($payload, 'slug', ''));

            $status = (int) Arr::get($payload, 'status', 0);
            $publishedAt = Arr::get($payload, 'published_at');
            if ($status === 1 && !$publishedAt) {
                $publishedAt = now();
            }

            $price = (float) Arr::get($payload, 'price', 0);

            $course = $this->courseRepository->createCourse([
                'instructor_id' => (int) Arr::get($payload, 'instructor_id'),
                'title' => $title,
                'slug' => $slug !== '' ? $slug : $generatedSlug,
                'description' => Arr::get($payload, 'description'),
                'thumbnail_url' => Arr::get($payload, 'thumbnail_url'),
                'intro_video_url' => Arr::get($payload, 'intro_video_url'),
                'price' => $price,
                'status' => $status,
                'published_at' => $publishedAt ?: null,
            ]);

            return $course->fresh(['instructor']);
        });
    }
}