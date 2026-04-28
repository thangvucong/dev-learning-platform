<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository
{
    /**
     * Get published posts sorted by latest published date.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedPosts(int $limit): Collection
    {
        return Post::query()
            ->with('user:id,name,email,avatar_url')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
