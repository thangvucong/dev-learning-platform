<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use App\Repositories\PostRepository;

class HomeService
{
    protected const COURSE_LIMIT = 8;

    protected const POST_LIMIT = 10;

    protected CourseRepository $courseRepository;

    protected PostRepository $postRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\CourseRepository  $courseRepository
     * @param  \App\Repositories\PostRepository  $postRepository
     */
    public function __construct(
        CourseRepository $courseRepository,
        PostRepository $postRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Get the source data for the home page.
     *
     * @return array<string, \Illuminate\Database\Eloquent\Collection>
     */
    public function getHomePageSourceData(): array
    {
        return [
            'courses' => $this->courseRepository->getPublishedCourses(self::COURSE_LIMIT),
            'posts' => $this->postRepository->getPublishedPosts(self::POST_LIMIT),
        ];
    }
}
