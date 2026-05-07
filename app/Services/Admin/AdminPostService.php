<?php

namespace App\Services\Admin;

use App\Models\Post;
use App\Repositories\Admin\AdminPostRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AdminPostService
{
    protected AdminPostRepositoryInterface $repository;

    /**
     * @param  \App\Repositories\Admin\AdminPostRepositoryInterface  $repository
     */
    public function __construct(AdminPostRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Build view data for admin post moderation index.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function buildIndexViewData(array $filters = []): array
    {
        $status = (string) Arr::get($filters, 'status', Post::STATUS_PENDING);
        $allowed = [Post::STATUS_PENDING, Post::STATUS_PUBLISHED, Post::STATUS_DRAFT, Post::STATUS_REJECTED];
        if (!in_array($status, $allowed, true)) {
            $status = Post::STATUS_PENDING;
        }

        $q = trim((string) Arr::get($filters, 'q', ''));
        $sort = (string) Arr::get($filters, 'sort', 'newest');
        $sort = in_array($sort, ['newest', 'oldest'], true) ? $sort : 'newest';

        $stats = $this->repository->getOverviewStats();
        $posts = $this->repository->paginateByStatus($status, [
            'q' => $q,
            'sort' => $sort,
        ], 15);

        $items = collect($posts->items())->map(function (Post $post) {
            $minutes = $this->estimateReadingTime((string) $post->content);

            return [
                'id' => $post->id,
                'title' => (string) $post->title,
                'slug' => (string) $post->slug,
                'status' => (string) $post->status,
                'description' => $post->description ? Str::limit((string) $post->description, 90) : Str::limit($this->stripMarkdown((string) $post->content), 90),
                'thumbnail' => $post->thumbnail,
                'views_count' => (int) ($post->views_count ?? 0),
                'created_at' => $post->created_at,
                'reject_reason' => (string) ($post->reject_reason ?? ''),
                'author' => [
                    'name' => (string) data_get($post->user, 'name', 'Unknown'),
                    'email' => (string) data_get($post->user, 'email', ''),
                    'avatar_url' => (string) data_get($post->user, 'avatar_url', ''),
                ],
                'reading_time' => $minutes,
            ];
        });

        if (method_exists($posts, 'setCollection')) {
            $posts->setCollection($items);
        }

        return [
            'status' => $status,
            'q' => $q,
            'sort' => $sort,
            'stats' => $stats,
            'posts' => $posts,
            'tabs' => [
                Post::STATUS_PENDING => 'Chờ duyệt',
                Post::STATUS_PUBLISHED => 'Đã xuất bản',
                Post::STATUS_DRAFT => 'Bản nháp',
                Post::STATUS_REJECTED => 'Đã từ chối',
            ],
        ];
    }

    /**
     * Approve a pending post.
     *
     * @param  int  $postId
     * @return bool
     */
    public function approve(int $postId): bool
    {
        $post = $this->repository->findById($postId);
        if (!$post || $post->status !== Post::STATUS_PENDING) {
            return false;
        }

        return $this->repository->update($postId, [
            'status' => Post::STATUS_PUBLISHED,
            'reject_reason' => null,
        ]);
    }

    /**
     * Reject a pending post with reason.
     *
     * @param  int  $postId
     * @param  string  $reason
     * @return bool
     */
    public function reject(int $postId, string $reason): bool
    {
        $post = $this->repository->findById($postId);
        if (!$post || $post->status !== Post::STATUS_PENDING) {
            return false;
        }

        $reason = trim($reason);
        if ($reason === '') {
            return false;
        }

        return $this->repository->update($postId, [
            'status' => Post::STATUS_REJECTED,
            'reject_reason' => $reason,
        ]);
    }

    /**
     * Unpublish a published post back to draft (hide).
     *
     * @param  int  $postId
     * @return bool
     */
    public function unpublish(int $postId): bool
    {
        $post = $this->repository->findById($postId);
        if (!$post || $post->status !== Post::STATUS_PUBLISHED) {
            return false;
        }

        return $this->repository->update($postId, [
            'status' => Post::STATUS_DRAFT,
        ]);
    }

    /**
     * Delete a post.
     *
     * @param  int  $postId
     * @return bool
     */
    public function delete(int $postId): bool
    {
        return $this->repository->delete($postId);
    }

    /**
     * Get post for preview.
     *
     * @param  int  $postId
     * @return \App\Models\Post|null
     */
    public function getForPreview(int $postId): ?Post
    {
        return $this->repository->findById($postId);
    }

    /**
     * @param  string  $markdown
     * @return int
     */
    protected function estimateReadingTime(string $markdown): int
    {
        $text = $this->stripMarkdown($markdown);
        $parts = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $words = count($parts);

        return max(1, (int) ceil($words / 200));
    }

    /**
     * @param  string  $markdown
     * @return string
     */
    protected function stripMarkdown(string $markdown): string
    {
        $text = $markdown;
        $text = preg_replace('/```[\s\S]*?```/m', ' ', $text) ?? $text;
        $text = preg_replace('/`[^`]*`/m', ' ', $text) ?? $text;
        $text = preg_replace('/!\[[^\]]*\]\([^)]+\)/m', ' ', $text) ?? $text;
        $text = preg_replace('/\[[^\]]*\]\([^)]+\)/m', ' ', $text) ?? $text;
        $text = preg_replace('/^#{1,6}\s+/m', '', $text) ?? $text;
        $text = preg_replace('/[*_~#>-]+/m', ' ', $text) ?? $text;
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? $text);

        return $text;
    }
}

