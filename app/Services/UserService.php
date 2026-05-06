<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * @var \App\Repositories\Interfaces\UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\Interfaces\UserRepositoryInterface  $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get paginated users with filters for admin page.
     *
     * @param  array<string, mixed>  $filters
     * @param  int  $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAdminUserList(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->getAdminUsersPaginated($filters, $perPage);
    }

    /**
     * Get stats counters for admin user list.
     *
     * @return array<string, int>
     */
    public function getAdminUserStats(): array
    {
        return $this->userRepository->getAdminUserStats();
    }

    /**
     * Toggle user active status.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function toggleActive(User $user): void
    {
        $user->is_active = !$user->is_active;
        $user->save();
    }
}

