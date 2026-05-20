<?php

namespace App\Services\Search;

use App\Repositories\Search\GlobalSearchRepository;
use Illuminate\Support\Str;

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
        $query = trim($query);
        $limit = max(1, min(8, $limit));

        if (empty($query) || strlen($query) < 2) {
            return [
                'courses' => [],
                'posts' => [],
                'total' => 0,
            ];
        }

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
            $instructor = optional($course->classes->first())->instructor;

            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description ? Str::limit($course->description, 80) : 'Không có mô tả',
                'slug' => $course->slug,
                'thumbnail' => media_url(
                    $course->thumbnail_url,
                    'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=240&q=80'
                ),
                'type' => 'course',
                'type_label' => 'Khóa học',
                'meta' => [
                    'instructor' => optional($instructor)->name ?: 'Đang cập nhật',
                    'instructor_avatar' => media_url(
                        optional($instructor)->avatar_url,
                        'https://ui-avatars.com/api/?name=' . urlencode((string) (optional($instructor)->name ?: 'GV'))
                    ),
                    'rating' => $course->rating_avg,
                    'rating_count' => $course->rating_count,
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
            return [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description ? Str::limit($post->description, 80) : 'Không có mô tả',
                'slug' => $post->slug,
                'thumbnail' => media_url(
                    $post->thumbnail ?: $post->image,
                    'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=240&q=80'
                ),
                'type' => 'post',
                'type_label' => 'Bài viết',
                'meta' => [
                    'author' => optional($post->user)->name ?: 'Ẩn danh',
                    'author_avatar' => media_url(
                        optional($post->user)->avatar_url,
                        'https://ui-avatars.com/api/?name=' . urlencode((string) (optional($post->user)->name ?: 'TG'))
                    ),
                    'date' => $post->created_at?->format('d/m/Y') ?? 'N/A',
                    'views_count' => $post->views_count,
                ],
                'url' => route('posts.show', ['slug' => $post->slug]),
            ];
        })->toArray();
    }
}
