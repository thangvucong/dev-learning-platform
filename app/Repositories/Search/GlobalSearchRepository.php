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
        return Course::query()
            ->where('status', 1)
            ->where(function ($q) use ($query) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%".strtolower($query)."%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%".strtolower($query)."%"]);
            })
            ->with('instructor:id,name,avatar_url')
            ->select(['id', 'title', 'slug', 'description', 'thumbnail_url', 'instructor_id'])
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Search posts by query
     */
    public function searchPosts(string $query, int $limit = 5): Collection
    {
        return Post::query()
            ->where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%".strtolower($query)."%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%".strtolower($query)."%"])
                  ->orWhereRaw('LOWER(content) LIKE ?', ["%".strtolower($query)."%"]);
            })
            ->with('user:id,name,avatar_url')
            ->select(['id', 'title', 'slug', 'description', 'thumbnail', 'user_id', 'published_at'])
            ->limit($limit)
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
