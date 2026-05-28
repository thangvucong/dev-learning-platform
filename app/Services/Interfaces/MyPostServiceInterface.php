<?php

namespace App\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MyPostServiceInterface
{
    /**
     * Build view data for my-posts dashboard.
     *
     * @param  int  $userId
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function buildMyPostsDashboard(int $userId, array $filters = []): array;

    /**
     * Delete a post owned by given user.
     *
     * @param  int  $userId
     * @param  int  $postId
     * @return bool
     */
    public function deleteMyPost(int $userId, int $postId): bool;

    /**
     * Move a rejected/draft post to pending again.
     *
     * @param  int  $userId
     * @param  int  $postId
     * @return bool
     */
    public function resubmitForReview(int $userId, int $postId): bool;
}

