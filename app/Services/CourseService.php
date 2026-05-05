<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Illuminate\Support\Facades\Log;

class CourseService
{
    protected CourseRepository $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        
        $this->courseRepository = $courseRepository; 
    }

    /**
     * Get source data for the course detail page.
     *
     * @param  string  $slug
     * @return array<string, mixed>
     */
    public function getCourseDetailSourceData(string $slug): array|null
    {
        try {
            $course = $this->courseRepository->findPublishedCourseDetailBySlug($slug);

            return [
                'course' => $course,
                'instructor' => $course->instructor,
                'classes' => $course->classes ?? [],
            ];
        } catch (\Throwable $th) {
            Log::error('Error getting course detail source data: ' . $th->getMessage());
            return [];
        }
    }

    public function getManagerListData(int $perPage = 10)
    {
        try {
            /** @var \Illuminate\Pagination\LengthAwarePaginator $courses */
          
            $courses = $this->courseRepo->getAllCoursesPaginated($perPage);

         $items = $courses->getCollection()->map(function ($course) {
    // Lấy giá đầu tiên đang active
    $activePrice = $course->prices->first();
    $priceValue = $activePrice ? $activePrice->price : ($course->price ?? 0);

    return [
        'id'          => $course->id,
        'name'        => $course->title, 
        'instructor'  => $course->instructor->name ?? 'N/A',
        'price'       => number_format($priceValue, 0, ',', '.') . 'đ',
        'class_count' => $course->classes->count(),
        'status'      => $course->status == 1 ? 'Hiển thị' : 'Ẩn',
        
        'classes'     => $course->classes->map(function($class) {
            return [
                'code'             => $class->code ?? 'N/A',
                'name'             => $class->name,
                'status'           => $class->status,
                'capacity'         => $class->capacity ?? 0,
                'current_students' => $class->users_count ?? 0, 
                'location'         => $class->location ?? 'Chưa xác định',
                'start_date'       => $class->start_at ? date('d/m/Y', strtotime($class->start_at)) : 'N/A',
            ];
        }),
    ];
});

            $courses->setCollection($items);
            return $courses;

        } catch (\Exception $e) {
           
            Log::error("Lỗi tại CourseService@getManagerListData: " . $e->getMessage());
            throw $e; 
        }
    }
}