<?php
namespace App\Repositories\Interfaces;

interface CourseRepositoryInterface {
    public function countAll();
    public function getRecentCourses($limit);
}