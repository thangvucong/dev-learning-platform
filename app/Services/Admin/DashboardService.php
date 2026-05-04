<?php
namespace App\Services\Admin;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;

class DashboardService {
    protected $userRepo;
    protected $courseRepo;

    public function __construct(
        UserRepositoryInterface $userRepo,
        CourseRepositoryInterface $courseRepo
    ) {
        $this->userRepo = $userRepo;
        $this->courseRepo = $courseRepo;
    }

    public function getDashboardData() {
        return [
            'total_users'   => $this->userRepo->countAll(),
            'total_courses' => $this->courseRepo->countAll(),
            'recent_users'  => $this->userRepo->getRecentUsers(5),
            'online_users'  => rand(50, 150), 
        ];
    }
}