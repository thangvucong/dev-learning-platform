<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Services\Interfaces\MyPostServiceInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MyPostService implements MyPostServiceInterface
{
    protected PostRepositoryInterface $postRepository;

    /**
     * @param  \App\Repositories\Interfaces\PostRepositoryInterface  $postRepository
     */
    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildMyPostsDashboard(int $userId, array $filters = []): array
    {
        $status = (string) Arr::get($filters, 'status', Post::STATUS_PUBLISHED);
        $allowed = [
            Post::STATUS_PUBLISHED,
            Post::STATUS_DRAFT,
            Post::STATUS_PENDING,
            Post::STATUS_REJECTED,
        ];
        if (!in_array($status, $allowed, true)) {
            $status = Post::STATUS_PUBLISHED;
        }

        $q = trim((string) Arr::get($filters, 'q', ''));
        $sort = (string) Arr::get($filters, 'sort', 'newest');
        $sort = in_array($sort, ['newest', 'oldest'], true) ? $sort : 'newest';

        $counts = $this->postRepository->countMyPostsByStatus($userId);
        $totalViews = $this->postRepository->sumMyViews($userId);

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $posts */
        $posts = $this->postRepository->paginateMyPostsByStatus($userId, $status, [
            'q' => $q,
            'sort' => $sort,
        ], 12);

        $items = collect($posts->items())->map(function ($post) {
            $markdown = (string) ($post->content ?? '');
            $wordCount = $this->estimateWordCount($markdown);
            $minutes = max(1, (int) ceil($wordCount / 200));

            return [
                'id' => $post->id,
                'title' => (string) $post->title,
                'slug' => (string) $post->slug,
                'description' => $post->description ? Str::limit((string) $post->description, 120) : Str::limit($this->stripMarkdown($markdown), 120),
                'thumbnail' => $post->thumbnail,
                'views_count' => (int) ($post->views_count ?? 0),
                'status' => (string) $post->status,
                'reject_reason' => (string) ($post->reject_reason ?? ''),
                'created_at' => $post->created_at,
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
            'counts' => $counts,
            'total_views' => $totalViews,
            'posts' => $posts,
            'tabs' => [
                Post::STATUS_PUBLISHED => 'Đã xuất bản',
                Post::STATUS_DRAFT => 'Bản nháp',
                Post::STATUS_PENDING => 'Chờ duyệt',
                Post::STATUS_REJECTED => 'Từ chối',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMyPost(int $userId, int $postId): bool
    {
        $post = $this->postRepository->findById($postId);
        if (!$post || (int) $post->user_id !== $userId) {
            return false;
        }

        return $this->postRepository->delete($postId);
    }

    /**
     * {@inheritdoc}
     */
    public function resubmitForReview(int $userId, int $postId): bool
    {
        $post = $this->postRepository->findById($postId);
        if (!$post || (int) $post->user_id !== $userId) {
            return false;
        }

        if (!in_array($post->status, [Post::STATUS_REJECTED, Post::STATUS_DRAFT], true)) {
            return false;
        }

        return $this->postRepository->update($postId, [
            'status' => Post::STATUS_PENDING,
            'reject_reason' => null,
        ]);
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

    /**
     * @param  string  $markdown
     * @return int
     */
    protected function estimateWordCount(string $markdown): int
    {
        $text = $this->stripMarkdown($markdown);
        $parts = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return count($parts);
    }
}

