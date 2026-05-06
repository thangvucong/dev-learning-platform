<?php
namespace App\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

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
}