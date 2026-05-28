<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class PostRepository implements PostRepositoryInterface
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
            ->select([
                'id',
                'user_id',
                'title',
                'slug',
                'description',
                'thumbnail',
                'image',
                'views_count',
                'status',
                'created_at',
            ])
            ->with('user:id,name,email,avatar_url')
            ->where('status', Post::STATUS_PUBLISHED)
            ->orderByDesc('views_count')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a new post.
     *
     * @param  array<string, mixed>  $attributes
     * @return \App\Models\Post
     */
    public function create(array $attributes): Post
    {
        return Post::query()->create($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $attributes): bool
    {
        $post = Post::query()->find($id);
        if (!$post) {
            return false;
        }

        return (bool) $post->update($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $post = Post::query()->find($id);
        if (!$post) {
            return false;
        }

        return (bool) $post->delete();
    }

    /**
     * Check if a slug already exists.
     *
     * @param  string  $slug
     * @return bool
     */
    public function slugExists(string $slug): bool
    {
        return Post::query()->where('slug', $slug)->exists();
    }

    /**
     * Find a post by slug (no publish constraints).
     *
     * @param  string  $slug
     * @return \App\Models\Post|null
     */
    public function findBySlug(string $slug): ?Post
    {
        return Post::query()
            ->with('user:id,name,email,avatar_url')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Post
    {
        return Post::query()
            ->with('user:id,name,email,avatar_url')
            ->whereKey($id)
            ->first();
    }

    /**
     * Find a post by slug that is visible for given viewer.
     *
     * @param  string  $slug
     * @param  int|null  $viewerId
     * @return \App\Models\Post|null
     */
    public function findVisibleBySlug(string $slug, ?int $viewerId = null): ?Post
    {
        return Post::query()
            ->with('user:id,name,email,avatar_url')
            ->where('slug', $slug)
            ->when($viewerId, function ($q) use ($viewerId) {
                $q->where(function ($sub) use ($viewerId) {
                    $sub->where('status', Post::STATUS_PUBLISHED)
                        ->orWhere('user_id', $viewerId);
                });
            }, function ($q) {
                $q->where('status', Post::STATUS_PUBLISHED);
            })
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function paginateMyPostsByStatus(int $userId, string $status, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $keyword = trim((string) Arr::get($filters, 'q', ''));
        $sort = (string) Arr::get($filters, 'sort', 'newest');

        $query = Post::query()
            ->where('user_id', $userId)
            ->when($status === Post::STATUS_PENDING_HUMAN_REVIEW, function ($query) {
                $query->whereIn('status', [Post::STATUS_PENDING_HUMAN_REVIEW, Post::STATUS_PENDING]);
            }, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->select([
                'id',
                'user_id',
                'title',
                'slug',
                'content',
                'description',
                'thumbnail',
                'image',
                'views_count',
                'status',
                'reject_reason',
                'ai_review_status',
                'ai_decision',
                'ai_confidence',
                'ai_severity',
                'ai_flags',
                'ai_summary',
                'ai_escalation_reason',
                'created_at',
                'updated_at',
            ]);

        if ($keyword !== '') {
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at');
        } else {
            $query->orderByDesc('created_at');
        }

        return $query->paginate($perPage)->appends(array_filter([
            'status' => $status,
            'q' => $keyword,
            'sort' => $sort,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function countMyPostsByStatus(int $userId): array
    {
        $rows = Post::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->where('user_id', $userId)
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        return [
            Post::STATUS_PUBLISHED => (int) ($rows[Post::STATUS_PUBLISHED] ?? 0),
            Post::STATUS_DRAFT => (int) ($rows[Post::STATUS_DRAFT] ?? 0),
            Post::STATUS_PENDING_AI_REVIEW => (int) ($rows[Post::STATUS_PENDING_AI_REVIEW] ?? 0),
            Post::STATUS_PENDING_HUMAN_REVIEW => (int) ($rows[Post::STATUS_PENDING_HUMAN_REVIEW] ?? 0) + (int) ($rows[Post::STATUS_PENDING] ?? 0),
            Post::STATUS_REJECTED => (int) ($rows[Post::STATUS_REJECTED] ?? 0),
            'total' => array_sum(array_map('intval', $rows)),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function sumMyViews(int $userId): int
    {
        return (int) Post::query()
            ->where('user_id', $userId)
            ->sum('views_count');
    }
}
