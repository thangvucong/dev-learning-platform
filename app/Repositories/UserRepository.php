<?php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {
    public function countAll() {
        return User::count();
    }

    public function getRecentUsers($limit = 5) {
        return User::select('id', 'name', 'email', 'role', 'created_at')
                   ->latest()
                   ->take($limit)
                   ->get();
    }
}