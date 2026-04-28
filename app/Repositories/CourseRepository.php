<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository
{
    /**
     * Get published courses sorted by latest published date.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedCourses(int $limit): Collection
    {
        return Course::query()
            ->with([
                'instructor:id,name,email,avatar_url',
                'classes' => function ($query) {
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
                        ->where('start_at', '>=', now())
                        ->orderBy('start_at');
                },
                'prices' => function ($query) {
                    $now = now();

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
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}