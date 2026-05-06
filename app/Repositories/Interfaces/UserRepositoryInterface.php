<?php

namespace App\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface {
    /**
     * @return int
     */
    public function countAll();

    /**
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentUsers($limit);

    /**
     * @param  array<string, mixed>  $filters
     * @param  int  $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAdminUsersPaginated(array $filters, int $perPage = 10): LengthAwarePaginator;

    /**
     * @return array<string, int>
     */
    public function getAdminUserStats(): array;

    /**
     * Giảng viên cho form quản lý khóa học (theo cột users.role).
     */
    public function findTeachersForSelect(): Collection;
}
