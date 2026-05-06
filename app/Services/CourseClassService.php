<?php
namespace App\Services;

use App\Repositories\CourseClassRepository;

class CourseClassService
{
    protected $classRepo;

    public function __construct(CourseClassRepository $classRepo)
    {
        $this->classRepo = $classRepo;
    }

    public function getListClasses($perPage = 10)
    {
        $classes = $this->classRepo->getAllClassesPaginated($perPage);

        
        $items = $classes->getCollection()->map(function ($class) {
            return [
                'id'         => $class->id,
                'name'       => $class->name,
                'code'       => $class->code,
                'course'     => $class->course->name ?? 'N/A',
                // Lớp học hiện tại không lưu instructor_id theo schema hiện tại.
                'mode'       => $class->mode ?? 'offline',
                'status'     => $class->status ?? 'upcoming',
                'capacity'   => (int) ($class->capacity ?? 30),
                'current_students' => (int) ($class->current_students ?? 0),
                'start_at'   => $class->start_at ? date('d/m/Y H:i', strtotime((string) $class->start_at)) : null,
                'end_at'     => $class->end_at ? date('d/m/Y H:i', strtotime((string) $class->end_at)) : null,
                'location'   => $class->location,
            ];
        });

        $classes->setCollection($items);
        return $classes;
    }
}