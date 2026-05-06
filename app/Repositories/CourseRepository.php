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
        'instructor:id,name',
        'classes' => function($q) {

            $q->select(['id', 'course_id', 'name', 'status', 'start_at', 'code', 'capacity', 'location'])
              ->withCount('users'); 
        },
        'attributes:id,course_id,type,content'
    ])
    ->latest()
  
    ->paginate($perPage);
}

    /**
     * Get published courses sorted by latest published date (Dành cho Client).
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedCourses(int $limit): Collection
    {
        return Course::query()
            ->select([
                'id', 'title', 'slug', 'thumbnail_url', 'price', 'published_at', 'instructor_id'
            ])
            ->with([
                'instructor:id,name,email,avatar_url',
                'classes' => function ($query) {
                    $query->select([
                        'id', 'course_id', 'name', 'status', 'start_at', 'end_at', 'location'
                    ])
                    ->whereNotNull('start_at')
                    ->where('start_at', '>=', now())
                    ->orderBy('start_at')
                    ->limit(3);
                }
            ])
            ->where('status', 1)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
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
        $course = Course::query()
            ->with([
                'instructor:id,name,email,avatar_url',
                'classes' => function ($query) {
                    $query->select([
                        'id', 'course_id', 'name', 'status', 'start_at', 'end_at', 'location'
                    ])
                    ->whereNotNull('start_at')
                    ->where('start_at', '>=', now())
                    ->orderBy('start_at')
                    ->limit(3);
                }
            ])
            ->where('slug', $slug)
            ->where('status', 1)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();

        if (!$course) {
            throw (new ModelNotFoundException())->setModel(Course::class, [$slug]);
        }

        return $course;
    }

    /**
     * Đếm tổng số khóa học cho Dashboard.
     */
    public function countAll(): int
    {
        return Course::count();
    }

    // public function getRecentCourses(int $limit): Collection
    public function getRecentCourses(int $limit): Collection
    {
        return Course::query()
            ->with(['instructor:id,name'])
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
                'price'
            ])
            ->whereKey($courseId)
            ->where('status', 1)
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
}