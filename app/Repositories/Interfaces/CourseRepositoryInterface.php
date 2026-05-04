<?php
namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Course;

interface CourseRepositoryInterface {
    public function getPublishedCourses(int $limit): Collection;
    public function findPublishedCourseDetailBySlug(string $slug): Course;
    public function countAll();
    public function getRecentCourses($limit);
    public function findPublishedCourseForCheckout(int $courseId): Course;
}