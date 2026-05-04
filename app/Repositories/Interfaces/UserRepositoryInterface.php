<?php
namespace App\Repositories\Interfaces;

interface UserRepositoryInterface {
    public function countAll();
    public function getRecentUsers($limit);
}