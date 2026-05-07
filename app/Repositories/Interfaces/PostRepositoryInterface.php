<?php

namespace App\Repositories\Interfaces;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    /**
     * Create a new post.
     *
     * @param  array<string, mixed>  $attributes
     * @return \App\Models\Post
     */
    public function create(array $attributes): Post;

    /**
     * Update an existing post by id.
     *
     * @param  int  $id
     * @param  array<string, mixed>  $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool;

    /**
     * Delete an existing post by id.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find visible post by slug.
     *
     * @param  string  $slug
     * @param  int|null  $viewerId
     * @return \App\Models\Post|null
     */
    public function findVisibleBySlug(string $slug, ?int $viewerId = null): ?Post;

    /**
     * Find a post by id (owner/admin use).
     *
     * @param  int  $id
     * @return \App\Models\Post|null
     */
    public function findById(int $id): ?Post;

    /**
     * Paginate posts of a user by status + filters.
     *
     * @param  int  $userId
     * @param  string  $status
     * @param  array<string, mixed>  $filters
     * @param  int  $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateMyPostsByStatus(int $userId, string $status, array $filters = [], int $perPage = 12): LengthAwarePaginator;

    /**
     * Get counts by status for a user.
     *
     * @param  int  $userId
     * @return array<string, int>
     */
    public function countMyPostsByStatus(int $userId): array;

    /**
     * Sum views_count for a user.
     *
     * @param  int  $userId
     * @return int
     */
    public function sumMyViews(int $userId): int;

    /**
     * Get limited published posts.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedPosts(int $limit): \Illuminate\Database\Eloquent\Collection;
}

