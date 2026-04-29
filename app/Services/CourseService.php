<?php

namespace App\Services;

use App\Repositories\CourseRepository;

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
    public function getCourseDetailSourceData(string $slug): array
    {
        $course = $this->courseRepository->findPublishedCourseDetailBySlug($slug);

        return [
            'course' => $course,
            'level' => $course->level,
            'instructor' => $course->instructor,
            'prices' => $course->prices,
            'classes' => $course->classes,
            'tracks' => $course->tracks,
            'attributes' => $course->attributes,
        ];
    }
}
