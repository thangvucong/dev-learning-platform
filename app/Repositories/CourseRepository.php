<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRepository implements CourseRepositoryInterface
{
    /**
     * 
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCoursesPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Course::query()
            ->with([
                'classes' => function ($query) {
                    $query->select([
                        'id',
                        'course_id',
                        'instructor_id',
                        'name',
                        'status',
                        'start_at',
                        'code',
                        'capacity',
                        'location',
                    ])
                        ->with('instructor:id,name')
                        ->withCount('users');
                },
                'attributes:id,course_id,type,content'
            ])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get published courses sorted by latest published date.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedCourses(int $limit): Collection
    {
        $now = now();

        return Course::query()
            ->select([
                'id',
                'title',
                'slug',
                'description',
                'thumbnail_url',
                'original_price',
                'rating_avg',
                'rating_count',
                'published_at',
            ])
            ->with([
                'classes' => function ($query) {
                    $query->select([
                        'id',
                        'course_id',
                        'instructor_id',
                        'name',
                        'status',
                        'start_at',
                        'end_at',
                        'location',
                    ])
                        ->with('instructor:id,name,email,avatar_url')
                        ->orderByRaw('CASE WHEN start_at IS NULL THEN 1 ELSE 0 END')
                        ->orderBy('start_at')
                        ->orderBy('id');
                },
                'activeDiscounts' => function ($query) use ($now) {
                    $query->select([
                        'id',
                        'course_id',
                        'type',
                        'amount',
                        'starts_at',
                        'ends_at',
                        'repeat_type',
                        'day_of_week',
                        'is_active',
                    ])
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                        });
                },
            ])
            ->where('status', Course::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->where('rating_avg', '>', 4.5)
            ->orderByDesc('rating_count')
            ->orderByDesc('rating_avg')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Find published course detail by slug with related data.
     *
     * @param  string  $slug
     * @return \App\Models\Course
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findPublishedCourseDetailBySlug(string $slug): Course
    {
        $now = now();

        $course = Course::query()
            ->with([
                'classes' => function ($query) {
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
                        ->with('instructor:id,name,email,avatar_url')
                        ->withCount('sessions')
                        ->orderByRaw('CASE WHEN start_at IS NULL THEN 1 ELSE 0 END')
                        ->orderBy('start_at')
                        ->orderBy('id');
                },
                'attributes:id,course_id,type,content',
                'tracks' => function ($query) {
                    $query->select(['id', 'course_id', 'parent_id', 'title', 'description', 'position'])
                        ->orderBy('position')
                        ->orderBy('id');
                },
                'activeDiscounts' => function ($query) use ($now) {
                    $query->select([
                        'id',
                        'course_id',
                        'type',
                        'amount',
                        'starts_at',
                        'ends_at',
                        'repeat_type',
                        'day_of_week',
                        'is_active',
                    ])
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                        });
                },
            ])
            ->where('slug', $slug)
            ->where('status', Course::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->first();

        if (!$course) {
            throw (new ModelNotFoundException())->setModel(Course::class, [$slug]);
        }

        return $course;
    }

    /**
     * Count sum course
     */
    public function countAll(): int
    {
        return Course::count();
    }

    // public function getRecentCourses(int $limit): Collection
    public function getRecentCourses(int $limit): Collection
    {
        return Course::query()
            ->with([
                'classes' => function ($query) {
                    $query->select(['id', 'course_id', 'instructor_id', 'start_at'])
                        ->with('instructor:id,name')
                        ->orderByRaw('CASE WHEN start_at IS NULL THEN 1 ELSE 0 END')
                        ->orderBy('start_at')
                        ->orderBy('id');
                },
            ])
            ->latest()
            ->take($limit)
            ->get();
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
                'original_price',
            ])
            ->selectRaw('original_price as price')
            ->with([
                'activeDiscounts' => function ($query) use ($now) {
                    $query->select([
                        'id',
                        'course_id',
                        'type',
                        'amount',
                        'starts_at',
                        'ends_at',
                        'repeat_type',
                        'day_of_week',
                        'is_active',
                    ])
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($subQuery) use ($now) {
                            $subQuery->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                        });
                },
            ])
            ->whereKey($courseId)
            ->where('status', Course::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createCourse(array $attributes): Course
    {
        return Course::query()->create($attributes);
    }
    public function update(int $id, array $attributes): bool
{
    $course = Course::find($id);
    if ($course) {
        return $course->update($attributes);
    }
    return false;
}
}
