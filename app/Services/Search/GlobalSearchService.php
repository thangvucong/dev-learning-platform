<?php

namespace App\Services\Search;

use App\Repositories\Search\GlobalSearchRepository;

class GlobalSearchService
{
    protected GlobalSearchRepository $repository;

    public function __construct(GlobalSearchRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Perform global search
     */
    public function search(string $query, int $limit = 5): array
    {
        // Trim and validate query
        $query = trim($query);

        if (empty($query) || strlen($query) < 2) {
            return [
                'courses' => [],
                'posts' => [],
                'total' => 0,
            ];
        }

        // Search both types
        $courses = $this->repository->searchCourses($query, $limit);
        $posts = $this->repository->searchPosts($query, $limit);

        return [
            'courses' => $this->transformCourses($courses),
            'posts' => $this->transformPosts($posts),
            'total' => $courses->count() + $posts->count(),
        ];
    }

    /**
     * Transform course data for frontend
     */
    private function transformCourses($courses): array
    {
        return $courses->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description ? \Str::limit($course->description, 80) : 'Không có mô tả',
                'slug' => $course->slug,
                'thumbnail' => $course->thumbnail_url ?? asset('images/default-course.png'),
                'type' => 'course',
                'type_label' => 'Khóa học',
                'meta' => [
                    'instructor' => $course->instructor?->name ?? 'Unknown',
                    'instructor_avatar' => $course->instructor?->avatar_url ?? asset('images/default-avatar.png'),
                ],
                'url' => route('courses.show', $course->slug),
            ];
        })->toArray();
    }

    /**
     * Transform post data for frontend
     */
    private function transformPosts($posts): array
    {
        return $posts->map(function ($post) {
            // Temporary URL - update route name when PostController is created
            $postUrl = route('home') . '/posts/' . $post->slug;
            
            return [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description ? \Str::limit($post->description, 80) : 'Không có mô tả',
                'slug' => $post->slug,
                'thumbnail' => $post->thumbnail ?? asset('images/default-post.png'),
                'type' => 'post',
                'type_label' => 'Bài viết',
                'meta' => [
                    'author' => $post->user?->name ?? 'Unknown',
                    'author_avatar' => $post->user?->avatar_url ?? asset('images/default-avatar.png'),
                    'date' => $post->published_at?->format('d/m/Y') ?? 'N/A',
                ],
                'url' => $postUrl,
            ];
        })->toArray();
    }
}
