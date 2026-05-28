<?php

namespace App\Repositories\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CourseRepositoryInterface
{
    public function getAllCoursesPaginated(int $perPage = 10): LengthAwarePaginator;

    public function getPublishedCourses(int $limit): Collection;

    public function findPublishedCourseDetailBySlug(string $slug): Course;

    public function countAll(): int;

    public function getRecentCourses(int $limit): Collection;

    public function findPublishedCourseForCheckout(int $courseId): Course;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createCourse(array $attributes): Course;


    
/**
 * Cập nhật khóa học
 * @param int $id
 * @param array $attributes
 * @return bool
 */
public function update(int $id, array $attributes): bool;
}
