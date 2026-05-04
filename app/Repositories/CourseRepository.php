<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Interfaces\CourseRepositoryInterface;
class CourseRepository implements CourseRepositoryInterface
{
    /**
     * Get published courses sorted by latest published date.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedCourses(int $limit): Collection
    {
        $now = now();

        return Course::query()
            ->with([
                'instructor:id,name,email,avatar_url',
                'classes' => function ($query) use ($now) {
                    $query->select([
                        'id',
                        'course_id',
                        'instructor_id',
                        'name',
                        'mode',
                        'status',
                        'start_at',
                        'end_at',
                        'location',
                    ])
                        ->whereNotNull('start_at')
                        ->where('start_at', '>=', $now)
                        ->orderBy('start_at');
                },
                'prices' => function ($query) use ($now) {
                    $query->select([
                        'id',
                        'course_id',
                        'currency_id',
                        'price',
                        'compare_price',
                        'starts_at',
                        'ends_at',
                        'is_active',
                    ])
                        ->where('is_active', true)
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                        })
                        ->orderByDesc('starts_at');
                },
                'prices.currency:id,symbol',
            ])
            ->where('status', 1)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Find published course detail by slug with related data.
     *
     * @param  string  $slug
     * @return \App\Models\Course
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findPublishedCourseDetailBySlug(string $slug): Course
    {
        $now = now();

        $course = Course::query()
            ->with([
                'level:id,name,description',
                'instructor:id,name,email,avatar_url',
                'classes' => function ($query) use ($now) {
                    $query->select([
                        'id',
                        'course_id',
                        'instructor_id',
                        'name',
                        'code',
                        'mode',
                        'status',
                        'capacity',
                        'start_at',
                        'end_at',
                        'location',
                    ])
                        ->orderByRaw('CASE WHEN start_at >= ? THEN 0 ELSE 1 END', [$now])
                        ->orderBy('start_at');
                },
                'prices' => function ($query) use ($now) {
                    $query->select([
                        'id',
                        'course_id',
                        'currency_id',
                        'price',
                        'compare_price',
                        'starts_at',
                        'ends_at',
                        'is_active',
                    ])
                        ->where('is_active', true)
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                        })
                        ->orderByDesc('starts_at');
                },
                'prices.currency:id,symbol',
                'attributes' => function ($query) {
                    $query->select([
                        'id',
                        'course_id',
                        'type',
                        'content',
                        'position',
                    ])->orderBy('position');
                },
                'tracks' => function ($query) {
                    $query->select([
                        'id',
                        'course_id',
                        'parent_id',
                        'title',
                        'description',
                        'position',
                    ])->orderBy('position');
                },
                'tracks.children' => function ($query) {
                    $query->select([
                        'id',
                        'course_id',
                        'parent_id',
                        'title',
                        'description',
                        'position',
                    ])->orderBy('position');
                },
            ])
            ->where('slug', $slug)
            ->where('status', 1)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->first();

        if (!$course) {
            throw (new ModelNotFoundException())->setModel(Course::class, [$slug]);
        }

        return $course;
    }

    /**
     * Find a published course by id for checkout (active price window, currency).
     *
     * @param  int  $courseId
     * @return \App\Models\Course
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findPublishedCourseForCheckout(int $courseId): Course
    {
        $now = now();

        return Course::query()
            ->select([
                'id',
                'title',
                'slug',
                'thumbnail_url',
                'is_free',
            ])
            ->with([
                'prices' => function ($query) use ($now) {
                    $query->select([
                        'id',
                        'course_id',
                        'currency_id',
                        'price',
                        'compare_price',
                        'starts_at',
                        'ends_at',
                        'is_active',
                    ])
                        ->where('is_active', true)
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                        })
                        ->orderByDesc('starts_at');
                },
                'prices.currency:id,symbol',
            ])
            ->whereKey($courseId)
            ->where('status', 1)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->firstOrFail();
    }

    public function countAll()
    {
        return Course::count();
    }

    public function getRecentCourses($limit)
    {
        // Giả sử lấy các khóa học mới nhất nếu Interface yêu cầu cho Course
        return Course::latest()->take($limit)->get();
    }
    
    /**
     * Find a published course by id for checkout (active price window, currency).
     *
     * @param  int  $courseId
     * @return \App\Models\Course
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findPublishedCourseForCheckout(int $courseId): Course
    {
        $now = now();

        return Course::query()
            ->select([
                'id',
                'title',
                'slug',
                'thumbnail_url',
                'is_free',
            ])
            ->with([
                'prices' => function ($query) use ($now) {
                    $query->select([
                        'id',
                        'course_id',
                        'currency_id',
                        'price',
                        'compare_price',
                        'starts_at',
                        'ends_at',
                        'is_active',
                    ])
                        ->where('is_active', true)
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                        })
                        ->orderByDesc('starts_at');
                },
                'prices.currency:id,symbol',
            ])
            ->whereKey($courseId)
            ->where('status', 1)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->firstOrFail();
    }
}