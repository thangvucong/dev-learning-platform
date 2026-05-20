<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseAttribute;
use App\Models\CourseDiscount;
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
            $detail = $this->mapCourseDetail($course);

            return [
                'course' => $detail['course'],
                'instructor' => $detail['instructor'],
                'classes' => $detail['classes'],
                'courseDetailData' => $detail,
            ];
        } catch (\Throwable $th) {
            Log::error('Error getting course detail source data: ' . $th->getMessage());

            return [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapCourseDetail(Course $course): array
    {
        $originalPrice = (int) ($course->original_price ?? 0);
        $salePrice = $this->resolveCourseSalePrice($course, $originalPrice);
        $classes = $course->classes->values();
        $primaryClass = $classes->first();
        $instructor = $primaryClass ? $primaryClass->instructor : null;
        $tracks = $this->mapTracks($course);
        $lessonsCount = $tracks->sum(function (array $track) {
            return $track['children']->count();
        });

        return [
            'course' => [
                'id' => (int) $course->id,
                'title' => (string) $course->title,
                'slug' => (string) $course->slug,
                'description' => (string) ($course->description ?: ''),
                'thumbnail_url' => $course->thumbnail_url,
                'intro_video_url' => $course->intro_video_url,
                'original_price' => $originalPrice,
                'sale_price' => $salePrice,
                'has_discount' => $salePrice < $originalPrice,
                'rating_avg' => (float) $course->rating_avg,
                'rating_count' => (int) $course->rating_count,
                'chapters_count' => $tracks->count(),
                'lessons_count' => $lessonsCount,
                'published_at' => $course->published_at,
            ],
            'instructor' => [
                'name' => optional($instructor)->name ?: 'Giảng viên',
                'email' => optional($instructor)->email ?: null,
                'avatar_url' => optional($instructor)->avatar_url
                    ?: 'https://files.f8.edu.vn/f8-prod/avatars/699286a5e7330.png',
            ],
            'classes' => $classes,
            'tracks' => $tracks,
            'benefits' => $this->mapAttributesByType($course, CourseAttribute::TYPE_BENEFIT),
            'requirements' => $this->mapAttributesByType($course, CourseAttribute::TYPE_REQUIREMENT),
            'targets' => $this->mapAttributesByType($course, CourseAttribute::TYPE_TARGET),
            'summary' => [
                'chapters_count' => $tracks->count(),
                'lessons_count' => $lessonsCount,
                'classes_count' => $classes->count(),
                'next_class_start_at' => optional($primaryClass)->start_at,
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function mapTracks(Course $course)
    {
        $tracks = $course->tracks->sortBy([
            ['position', 'asc'],
            ['id', 'asc'],
        ])->values();
        $childrenByParent = $tracks->whereNotNull('parent_id')->groupBy('parent_id');

        return $tracks
            ->whereNull('parent_id')
            ->map(function ($track) use ($childrenByParent) {
                return [
                    'id' => (int) $track->id,
                    'title' => (string) $track->title,
                    'description' => (string) ($track->description ?: ''),
                    'position' => (int) $track->position,
                    'children' => $childrenByParent
                        ->get($track->id, collect())
                        ->sortBy([
                            ['position', 'asc'],
                            ['id', 'asc'],
                        ])
                        ->values()
                        ->map(function ($child) {
                            return [
                                'id' => (int) $child->id,
                                'title' => (string) $child->title,
                                'description' => (string) ($child->description ?: ''),
                                'position' => (int) $child->position,
                            ];
                        }),
                ];
            })
            ->values();
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, string>>
     */
    protected function mapAttributesByType(Course $course, string $type)
    {
        return $course->attributes
            ->where('type', $type)
            ->values()
            ->map(function ($attribute) {
                return [
                    'content' => (string) $attribute->content,
                ];
            });
    }

    protected function resolveCourseSalePrice(Course $course, int $originalPrice): int
    {
        if ($originalPrice <= 0 || $course->activeDiscounts->isEmpty()) {
            return max(0, $originalPrice);
        }

        return (int) $course->activeDiscounts
            ->map(function ($discount) use ($originalPrice) {
                return $this->applyDiscount($originalPrice, $discount);
            })
            ->filter(function (int $price) {
                return $price >= 0;
            })
            ->min();
    }

    protected function applyDiscount(int $originalPrice, $discount): int
    {
        $amount = (int) $discount->amount;

        if ($discount->type === CourseDiscount::TYPE_PERCENT) {
            return max(0, $originalPrice - (int) round($originalPrice * min($amount, 100) / 100));
        }

        if ($discount->type === CourseDiscount::TYPE_FIXED) {
            return max(0, $originalPrice - $amount);
        }

        if ($discount->type === CourseDiscount::TYPE_FINAL_PRICE) {
            return max(0, min($originalPrice, $amount));
        }

        return $originalPrice;
    }
public function getManagerListData(int $perPage = 10)
{
    try {
        // Đảm bảo Repository đã eager load: instructor và classes
        $courses = $this->courseRepository->getAllCoursesPaginated($perPage);

        $courses->getCollection()->transform(function ($course) {
            $primaryClass = $course->classes->first();

            return [
                'id'          => $course->id,
                'name'        => $course->title,
                'instructor'  => optional(optional($primaryClass)->instructor)->name ?? 'N/A',
                'price'       => number_format((int) $course->original_price, 0, ',', '.') . 'đ',
                'class_count' => $course->classes->count(),
                'status'      => $course->status == 1 ? 'Hiển thị' : 'Ẩn',
                'classes'     => $course->classes->map(fn($class) => [
                    'code'             => $class->code ?? 'N/A',
                    'name'             => $class->name,
                    'capacity'         => $class->capacity ?? 0,
                    'current_students' => $class->users_count ?? 0,
                    'start_date'       => $class->start_at ? $class->start_at->format('d/m/Y') : 'N/A',
                ])->values()->all(),
            ];
        });

        return $courses;
    } catch (\Exception $e) {
        Log::error('Lỗi tại CourseService@getManagerListData: ' . $e->getMessage());
        throw $e;
    }
}
    // public function getManagerListData(int $perPage = 10)
    // {
    //     try {
    //         /** @var \Illuminate\Pagination\LengthAwarePaginator $courses */
    //         $courses = $this->courseRepository->getAllCoursesPaginated($perPage);

    //         $items = $courses->getCollection()->map(function ($course) {
    //             return [
    //                 'id' => $course->id,
    //                 'name' => $course->title,
    //                 'instructor' => $course->instructor->name ?? 'N/A',
    //                 'price' => number_format($course->price, 0, ',', '.') . 'đ',
    //                 'class_count' => $course->classes->count(),
    //                 'status' => $course->status == 1 ? 'Hiển thị' : 'Ẩn',
    //                 'classes' => $course->classes->map(function ($class) {
    //                     return [
    //                         'code' => $class->code ?? 'N/A',
    //                         'name' => $class->name,
    //                         'status' => $class->status,
    //                         'capacity' => $class->capacity ?? 0,
    //                         'current_students' => $class->users_count ?? 0,
    //                         'location' => $class->location ?? 'Chưa xác định',
    //                         'start_date' => $class->start_at ? date('d/m/Y', strtotime($class->start_at)) : 'N/A',
    //                     ];
    //                 })->values()->all(),
    //             ];
    //         });

    //         $courses->setCollection($items);

    //         return $courses;
    //     } catch (\Exception $e) {
    //         Log::error('Lỗi tại CourseService@getManagerListData: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }

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

            $course = $this->courseRepository->createCourse([
                'title' => $title,
                'slug' => $slug !== '' ? $slug : $generatedSlug,
                'description' => Arr::get($payload, 'description'),
                'thumbnail_url' => Arr::get($payload, 'thumbnail_url'),
                'intro_video_url' => Arr::get($payload, 'intro_video_url'),
                'original_price' => (int) Arr::get($payload, 'price', 0),
                'status' => $status,
                'published_at' => $publishedAt ?: null,
            ]);

            return $course->fresh(['classes.instructor']);
        });
    }
    


   

/**
 * Thay đổi giảng viên cho khóa học
 * * @param int $courseId
 * @param int $instructorId
 * @return bool
 */
public function updateCourseInstructor(int $courseId, int $instructorId): bool
{
    try {
        return DB::table('classes')
            ->where('course_id', $courseId)
            ->update(['instructor_id' => $instructorId]) > 0;
    } catch (\Exception $e) {
        Log::error("Lỗi thay đổi giảng viên tại CourseService: " . $e->getMessage());
        return false;
    }
}
}