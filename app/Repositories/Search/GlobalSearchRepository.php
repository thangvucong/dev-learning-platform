<?php

namespace App\Repositories\Search;

use App\Models\Course;
use App\Models\Post;
use Illuminate\Support\Collection;

class GlobalSearchRepository
{
    /**
     * Search courses by query
     */
    public function searchCourses(string $query, int $limit = 5): Collection
    {
        $keyword = mb_strtolower(trim($query));
        $now = now();

        return Course::query()
            ->select([
                'id',
                'title',
                'slug',
                'description',
                'thumbnail_url',
                'rating_avg',
                'rating_count',
                'published_at',
            ])
            ->with([
                'classes' => function ($classQuery) {
                    $classQuery->select(['id', 'course_id', 'instructor_id', 'start_at'])
                        ->with('instructor:id,name,avatar_url')
                        ->orderByRaw('CASE WHEN start_at IS NULL THEN 1 ELSE 0 END')
                        ->orderBy('start_at')
                        ->orderBy('id');
                },
            ])
            ->where('status', Course::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->where(function ($q) use ($query) {
                $keyword = mb_strtolower(trim($query));

                $q->whereRaw('LOWER(title) LIKE ?', ['%' . $keyword . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%']);
            })
            ->orderByRaw('CASE WHEN LOWER(title) LIKE ? THEN 0 ELSE 1 END', [$keyword . '%'])
            ->orderByDesc('rating_count')
            ->orderByDesc('rating_avg')
            ->limit(max(1, $limit))
            ->get();
    }

    /**
     * Search posts by query
     */
    public function searchPosts(string $query, int $limit = 5): Collection
    {
        $keyword = mb_strtolower(trim($query));

        return Post::query()
            ->select(['id', 'title', 'slug', 'description', 'thumbnail', 'image', 'views_count', 'user_id', 'created_at'])
            ->with('user:id,name,avatar_url')
            ->where('status', Post::STATUS_PUBLISHED)
            ->where(function ($q) use ($query) {
                $keyword = mb_strtolower(trim($query));

                $q->whereRaw('LOWER(title) LIKE ?', ['%' . $keyword . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%'])
                    ->orWhereRaw('LOWER(content) LIKE ?', ['%' . $keyword . '%']);
            })
            ->orderByRaw('CASE WHEN LOWER(title) LIKE ? THEN 0 ELSE 1 END', [$keyword . '%'])
            ->orderByDesc('views_count')
            ->orderByDesc('created_at')
            ->limit(max(1, $limit))
            ->get();
    }
}
