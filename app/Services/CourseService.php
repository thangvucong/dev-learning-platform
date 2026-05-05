<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Illuminate\Support\Facades\Log;

class CourseService
{
    protected $courseRepo;
    protected CourseRepository $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->courseRepo = $courseRepository; 
    }

    public function getCourseDetailSourceData(string $slug): array
    {
        $course = $this->courseRepository->findPublishedCourseDetailBySlug($slug);

        return [
            'course' => $course,
            'level' => null,
            'instructor' => $course->instructor,
            'prices' => $course->prices,
            'classes' => $course->classes,
            'tracks' => $course->tracks,
            'attributes' => $course->attributes,
        ];
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