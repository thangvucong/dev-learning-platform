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
                'instructor' => $class->instructor->name ?? 'N/A',
                'mode'       => $class->mode, 
                'status'     => $class->status, 
                'capacity'   => $class->capacity,
                'start_at'   => $class->start_at ? date('d/m/Y H:i', strtotime($class->start_at)) : null,
                'location'   => $class->location,
            ];
        });

        $classes->setCollection($items);
        return $classes;
    }
}