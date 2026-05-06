<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function countAll(): int
    {
        return User::count();
    }

    public function getRecentUsers(int $limit = 5): Collection
    {
        return User::query()
            ->select(['id', 'name', 'email', 'role', 'created_at'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Giảng viên cho form quản lý khóa học (theo cột users.role).
     */
    public function findTeachersForSelect(): Collection
    {
        return User::query()
            ->where('role', 'teacher')
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();
    }
}