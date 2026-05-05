<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Illuminate\Support\Facades\Log;

class CourseService
{
    protected CourseRepository $courseRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\CourseRepository  $courseRepository
     */
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
        /** @var \Illuminate\Pagination\LengthAwarePaginator $courses */
        $courses = $this->courseRepository->getAllCoursesPaginated($perPage);

        $items = $courses->getCollection()->map(function ($course) {
            $activePrice = $course->prices->first();
            
            return [
                'id'            => $course->id,
                'name'          => $course->name,
                'instructor'    => $course->instructor->name ?? 'N/A',
                'price'         => $activePrice 
                                    ? number_format($activePrice->price, 0, ',', '.') . 'đ' 
                                    : 'Liên hệ',
                'class_count'   => $course->classes->count(),
                'status'        => $course->status == 1 ? 'Hiển thị' : 'Ẩn',
                'created_at'    => $course->created_at->format('d/m/Y'),
            ];
        });

    
        $courses->setCollection($items);

        return $courses;
    }
}