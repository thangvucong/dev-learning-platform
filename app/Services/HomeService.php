<?php

namespace App\Services;

use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Log;

class HomeService
{
    protected const COURSE_LIMIT = 8;

    protected const POST_LIMIT = 10;

    protected CourseRepositoryInterface $courseRepository;

    protected PostRepository $postRepository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\Interfaces\CourseRepositoryInterface  $courseRepository
     * @param  \App\Repositories\PostRepository  $postRepository
     */
    public function __construct(
        CourseRepositoryInterface $courseRepository,
        PostRepository $postRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Get the source data for the home page.
     *
     * @return array<string, \Illuminate\Database\Eloquent\Collection>|null
     */
    public function getHomePageSourceData(): array|null
    {
        try {
            $courses = $this->courseRepository->getPublishedCourses(self::COURSE_LIMIT);
            $posts = $this->postRepository->getPublishedPosts(self::POST_LIMIT);

            return [
                'courses' => $courses,
                'posts' => $posts,
            ];
        } catch (\Throwable $th) {
            Log::error('Error getting home page source data: ' . $th->getMessage());

            return [
                'courses' => [],
                'posts' => [],
            ];
        }
    }
}
