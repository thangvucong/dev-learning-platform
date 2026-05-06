<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function countAll(): int;

    public function getRecentUsers(int $limit = 5): Collection;

    /**
     * Giảng viên cho form quản lý khóa học (theo cột users.role).
     */
    public function findTeachersForSelect(): Collection;
}
