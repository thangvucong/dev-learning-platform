<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;

interface CourseRepositoryInterface 
{
    
    public function getAllCoursesPaginated(int $perPage = 10): LengthAwarePaginator;

    
    public function getPublishedCourses(int $limit): Collection;

    
    public function findPublishedCourseDetailBySlug(string $slug): Course;

    
    public function countAll(): int;

    
    public function getRecentCourses(int $limit): Collection;
    public function findPublishedCourseForCheckout(int $courseId): Course;

}