<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get paginated users for admin list with filters.
     *
     * @param  array<string, mixed>  $filters
     * @param  int  $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAdminUsersPaginated(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->with('roles:id,name')
            ->withCount([
                'orders as purchased_courses_count' => function (Builder $query) {
                    $query->where('status', 'paid');
                },
                'assignedClasses as joined_classes_count',
            ])
            ->when(!empty($filters['keyword']), function (Builder $query) use ($filters) {
                $keyword = trim((string) $filters['keyword']);
                $query->where(function (Builder $subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                });
            })
            ->when(!empty($filters['role']), function (Builder $query) use ($filters) {
                $query->role((string) $filters['role']);
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', function (Builder $query) use ($filters) {
                $query->where('is_active', (int) $filters['status'] === 1);
            })
            ->when(!empty($filters['created_from']), function (Builder $query) use ($filters) {
                $query->whereDate('created_at', '>=', (string) $filters['created_from']);
            })
            ->when(!empty($filters['created_to']), function (Builder $query) use ($filters) {
                $query->whereDate('created_at', '<=', (string) $filters['created_to']);
            })
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get stats counters for admin user list page.
     *
     * @return array<string, int>
     */
    public function getAdminUserStats(): array
    {
        return [
            'total_users' => User::query()->count(),
            'active_users' => User::query()->where('is_active', true)->count(),
            'admins' => User::query()->role(User::ROLE_ADMIN)->count(),
            'new_users_this_month' => User::query()
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];
    }

    /**
     * Count all users for dashboard.
     *
     * @return int
     */
    public function countAll()
    {
        return User::query()->count();
    }

    /**
     * Get recent users for dashboard widget.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentUsers($limit = 5)
    {
        return User::query()
            ->with('roles:id,name')
            ->select('id', 'name', 'email', 'created_at')
            ->latest()
            ->take((int) $limit)
            ->get();
    }

    /**
     * Giảng viên cho form quản lý khóa học.
     */
    public function findTeachersForSelect(): Collection
    {
        return User::query()
            ->role(User::ROLE_INSTRUCTOR)
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();
    }
}
