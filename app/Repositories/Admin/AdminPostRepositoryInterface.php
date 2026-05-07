<?php

namespace App\Repositories\Admin;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminPostRepositoryInterface
{
    /**
     * Paginate posts by status with filters for admin moderation.
     *
     * @param  string  $status
     * @param  array<string, mixed>  $filters
     * @param  int  $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateByStatus(string $status, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get overview counts grouped by status + total views.
     *
     * @return array<string, int>
     */
    public function getOverviewStats(): array;

    /**
     * Find post for moderation detail.
     *
     * @param  int  $id
     * @return \App\Models\Post|null
     */
    public function findById(int $id): ?Post;

    /**
     * Update post by id.
     *
     * @param  int  $id
     * @param  array<string, mixed>  $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool;

    /**
     * Delete post by id.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool;
}

