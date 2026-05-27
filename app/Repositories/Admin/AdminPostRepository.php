<?php

namespace App\Repositories\Admin;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class AdminPostRepository implements AdminPostRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function paginateByStatus(string $status, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $q = trim((string) Arr::get($filters, 'q', ''));
        $sort = (string) Arr::get($filters, 'sort', 'newest');
        $sort = in_array($sort, ['newest', 'oldest'], true) ? $sort : 'newest';

        $query = Post::query()
            ->with('user:id,name,email,avatar_url')
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
                'ai_reviewed_at',
                'created_at',
                'updated_at',
            ]);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', '%' . $q . '%')
                    ->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', '%' . $q . '%')
                            ->orWhere('email', 'like', '%' . $q . '%');
                    });
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at');
        } else {
            $query->orderByDesc('created_at');
        }

        return $query->paginate($perPage)->appends(array_filter([
            'status' => $status,
            'q' => $q,
            'sort' => $sort,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function getOverviewStats(): array
    {
        $counts = Post::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        $totalViews = (int) Post::query()->sum('views_count');
        $total = array_sum(array_map('intval', $counts));

        return [
            'total' => (int) $total,
            Post::STATUS_PENDING_AI_REVIEW => (int) ($counts[Post::STATUS_PENDING_AI_REVIEW] ?? 0),
            Post::STATUS_PENDING_HUMAN_REVIEW => (int) ($counts[Post::STATUS_PENDING_HUMAN_REVIEW] ?? 0) + (int) ($counts[Post::STATUS_PENDING] ?? 0),
            Post::STATUS_PUBLISHED => (int) ($counts[Post::STATUS_PUBLISHED] ?? 0),
            Post::STATUS_DRAFT => (int) ($counts[Post::STATUS_DRAFT] ?? 0),
            Post::STATUS_REJECTED => (int) ($counts[Post::STATUS_REJECTED] ?? 0),
            'total_views' => $totalViews,
        ];
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
}
