<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\CourseAttribute;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class ChatbotKnowledgeRepository
{
    public function findPublishedPostBySlug(string $slug): ?Post
    {
        return Post::query()
            ->select(['id', 'user_id', 'title', 'slug', 'content', 'description', 'views_count', 'created_at'])
            ->with('user:id,name')
            ->where('slug', $slug)
            ->where('status', Post::STATUS_PUBLISHED)
            ->first();
    }

    public function findPublishedCourseBySlug(string $slug): ?Course
    {
        $now = now();

        return Course::query()
            ->select([
                'id',
                'title',
                'slug',
                'description',
                'original_price',
                'rating_avg',
                'rating_count',
                'published_at',
            ])
            ->with([
                'classes' => function ($query) {
                    $query->select(['id', 'course_id', 'instructor_id', 'name', 'start_at', 'location'])
                        ->with('instructor:id,name')
                        ->orderByRaw('CASE WHEN start_at IS NULL THEN 1 ELSE 0 END')
                        ->orderBy('start_at')
                        ->orderBy('id');
                },
                'tracks:id,course_id,parent_id,title,description,position',
                'attributes:id,course_id,type,content',
                'activeDiscounts' => function ($query) use ($now) {
                    $query->select(['id', 'course_id', 'type', 'amount', 'starts_at', 'ends_at', 'is_active'])
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
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post>
     */
    public function getFeaturedPosts(int $limit = 4): Collection
    {
        return Post::query()
            ->select(['id', 'user_id', 'title', 'slug', 'description', 'views_count', 'created_at'])
            ->with('user:id,name')
            ->where('status', Post::STATUS_PUBLISHED)
            ->orderByDesc('views_count')
            ->orderByDesc('created_at')
            ->limit(max(1, $limit))
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post>
     */
    public function searchPublishedPosts(string $query, int $limit = 4): Collection
    {
        $keyword = trim($query);
        if ($keyword === '') {
            return new Collection();
        }

        return Post::query()
            ->select(['id', 'user_id', 'title', 'slug', 'description', 'views_count', 'created_at'])
            ->with('user:id,name')
            ->where('status', Post::STATUS_PUBLISHED)
            ->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('content', 'like', '%' . $keyword . '%');
            })
            ->orderByDesc('views_count')
            ->orderByDesc('created_at')
            ->limit(max(1, $limit))
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course>
     */
    public function getFeaturedCourses(int $limit = 4): Collection
    {
        $now = now();

        return $this->basePublishedCourseListQuery($now)
            ->orderByDesc('rating_count')
            ->orderByDesc('rating_avg')
            ->orderByDesc('published_at')
            ->limit(max(1, $limit))
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course>
     */
    public function searchPublishedCourses(string $query, int $limit = 4): Collection
    {
        $keyword = trim($query);
        if ($keyword === '') {
            return new Collection();
        }

        $now = now();

        return $this->basePublishedCourseListQuery($now)
            ->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%');
            })
            ->orderByDesc('rating_count')
            ->orderByDesc('rating_avg')
            ->orderByDesc('published_at')
            ->limit(max(1, $limit))
            ->get();
    }

    protected function basePublishedCourseListQuery($now)
    {
        return Course::query()
            ->select(['id', 'title', 'slug', 'description', 'original_price', 'rating_avg', 'rating_count', 'published_at'])
            ->with([
                'classes' => function ($classQuery) {
                    $classQuery->select(['id', 'course_id', 'instructor_id', 'start_at'])
                        ->with('instructor:id,name')
                        ->orderByRaw('CASE WHEN start_at IS NULL THEN 1 ELSE 0 END')
                        ->orderBy('start_at')
                        ->orderBy('id');
                },
                'activeDiscounts' => function ($query) use ($now) {
                    $query->select(['id', 'course_id', 'type', 'amount', 'starts_at', 'ends_at', 'is_active'])
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
            ->where('published_at', '<=', $now);
    }

    public function salePrice(Course $course): int
    {
        $originalPrice = (int) ($course->original_price ?? 0);
        if ($originalPrice <= 0 || $course->activeDiscounts->isEmpty()) {
            return max(0, $originalPrice);
        }

        return (int) $course->activeDiscounts
            ->map(function ($discount) use ($originalPrice) {
                $amount = (int) $discount->amount;
                if ($discount->type === 'percent') {
                    return max(0, $originalPrice - (int) round($originalPrice * min($amount, 100) / 100));
                }

                if ($discount->type === 'fixed') {
                    return max(0, $originalPrice - $amount);
                }

                if ($discount->type === 'final_price') {
                    return max(0, min($originalPrice, $amount));
                }

                return $originalPrice;
            })
            ->filter(function (int $price) {
                return $price >= 0;
            })
            ->min();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function groupedAttributes(Course $course): array
    {
        return [
            'benefits' => $course->attributes
                ->where('type', CourseAttribute::TYPE_BENEFIT)
                ->pluck('content')
                ->values()
                ->all(),
            'requirements' => $course->attributes
                ->where('type', CourseAttribute::TYPE_REQUIREMENT)
                ->pluck('content')
                ->values()
                ->all(),
            'targets' => $course->attributes
                ->where('type', CourseAttribute::TYPE_TARGET)
                ->pluck('content')
                ->values()
                ->all(),
        ];
    }
}
