<?php

namespace App\Services\AI;

use App\Models\Course;
use App\Models\Post;
use App\DTO\AI\ChatbotIntent;
use App\Repositories\ChatbotKnowledgeRepository;
use Illuminate\Support\Str;

class ChatbotContextService
{
    protected ChatbotKnowledgeRepository $repository;

    protected ChatbotIntentDetector $intentDetector;

    public function __construct(ChatbotKnowledgeRepository $repository, ChatbotIntentDetector $intentDetector)
    {
        $this->repository = $repository;
        $this->intentDetector = $intentDetector;
    }

    /**
     * @param  string  $pageType
     * @param  string|null  $pageRef
     * @param  string  $message
     * @return array<string, mixed>
     */
    public function build(string $pageType, ?string $pageRef, string $message): array
    {
        $sources = [];
        $context = [
            'page_type' => $pageType,
            'current_page' => null,
            'related_courses' => [],
            'related_posts' => [],
        ];

        if ($pageType === 'post_detail' && $pageRef) {
            $post = $this->repository->findPublishedPostBySlug($pageRef);
            if ($post) {
                $context['current_page'] = $this->postContext($post, true);
                $sources[] = $this->source('current_page', (string) $post->title, route('posts.show', $post->slug));
            }
        }

        if ($pageType === 'course_detail' && $pageRef) {
            $course = $this->repository->findPublishedCourseBySlug($pageRef);
            if ($course) {
                $context['current_page'] = $this->courseContext($course, true);
                $sources[] = $this->source('current_page', (string) $course->title, route('courses.show', $course->slug));
            }
        }

        $intent = $this->intentDetector->detect($message, $pageType);
        $courses = collect();
        $posts = collect();

        if ($intent->type === ChatbotIntent::FEATURED_COURSES || ($pageType === 'home' && !$intent->hasKeyword())) {
            $courses = $this->repository->getFeaturedCourses(4);
        } elseif (in_array($intent->type, [ChatbotIntent::COURSE_SEARCH, ChatbotIntent::GENERIC], true) && $intent->hasKeyword()) {
            $courses = $this->repository->searchPublishedCourses($intent->keyword, 4);
        }

        if ($intent->type === ChatbotIntent::FEATURED_POSTS || ($pageType === 'posts_index' && !$intent->hasKeyword())) {
            $posts = $this->repository->getFeaturedPosts(4);
        } elseif (in_array($intent->type, [ChatbotIntent::POST_SEARCH, ChatbotIntent::GENERIC], true) && $intent->hasKeyword()) {
            $posts = $this->repository->searchPublishedPosts($intent->keyword, 4);
        }

        $context['intent'] = [
            'type' => $intent->type,
            'keyword' => $intent->keyword,
        ];

        $context['related_courses'] = $courses->map(function (Course $course) use (&$sources) {
            $sources[] = $this->source('course', (string) $course->title, route('courses.show', $course->slug));

            return $this->courseContext($course, false);
        })->values()->all();

        $context['related_posts'] = $posts->map(function (Post $post) use (&$sources) {
            $sources[] = $this->source('post', (string) $post->title, route('posts.show', $post->slug));

            return $this->postContext($post, false);
        })->values()->all();

        return [
            'context' => $context,
            'sources' => collect($sources)
                ->unique(function (array $source) {
                    return $source['type'] . ':' . $source['url'];
                })
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function postContext(Post $post, bool $includeContent): array
    {
        $context = [
            'type' => 'post',
            'title' => (string) $post->title,
            'slug' => (string) $post->slug,
            'description' => $this->limit((string) ($post->description ?? ''), 500),
            'author' => (string) data_get($post->user, 'name', 'Ẩn danh'),
            'views_count' => (int) ($post->views_count ?? 0),
            'published_date' => optional($post->created_at)->format('d/m/Y'),
            'url' => route('posts.show', $post->slug),
        ];

        if ($includeContent) {
            $context['content'] = $this->limit($this->stripMarkdown((string) $post->content), 4500);
        }

        return $context;
    }

    /**
     * @return array<string, mixed>
     */
    protected function courseContext(Course $course, bool $includeDetail): array
    {
        $primaryClass = $course->classes->first();
        $attributes = $includeDetail ? $this->repository->groupedAttributes($course) : [];

        $context = [
            'type' => 'course',
            'title' => (string) $course->title,
            'slug' => (string) $course->slug,
            'description' => $this->limit($this->stripMarkdown((string) ($course->description ?? '')), $includeDetail ? 2500 : 500),
            'original_price' => (int) ($course->original_price ?? 0),
            'sale_price' => $this->repository->salePrice($course),
            'rating_avg' => (float) ($course->rating_avg ?? 0),
            'rating_count' => (int) ($course->rating_count ?? 0),
            'instructor' => (string) data_get($primaryClass, 'instructor.name', 'Đang cập nhật'),
            'next_class_start_at' => optional(data_get($primaryClass, 'start_at'))->format('d/m/Y'),
            'url' => route('courses.show', $course->slug),
        ];

        if ($includeDetail) {
            $context['benefits'] = array_slice($attributes['benefits'] ?? [], 0, 8);
            $context['requirements'] = array_slice($attributes['requirements'] ?? [], 0, 8);
            $context['targets'] = array_slice($attributes['targets'] ?? [], 0, 8);
            $context['tracks'] = $course->tracks
                ->sortBy(['position', 'id'])
                ->take(20)
                ->map(function ($track) {
                    return [
                        'title' => (string) $track->title,
                        'description' => $this->limit((string) ($track->description ?? ''), 250),
                    ];
                })
                ->values()
                ->all();
        }

        return $context;
    }

    /**
     * @return array<string, string>
     */
    protected function source(string $type, string $title, string $url): array
    {
        return [
            'type' => $type,
            'title' => $title,
            'url' => $url,
        ];
    }

    protected function stripMarkdown(string $markdown): string
    {
        $text = $markdown;
        $text = preg_replace('/```[\s\S]*?```/m', ' ', $text) ?? $text;
        $text = preg_replace('/`[^`]*`/m', ' ', $text) ?? $text;
        $text = preg_replace('/!\[[^\]]*\]\([^)]+\)/m', ' ', $text) ?? $text;
        $text = preg_replace('/\[[^\]]*\]\([^)]+\)/m', '$1', $text) ?? $text;
        $text = preg_replace('/^#{1,6}\s+/m', '', $text) ?? $text;
        $text = preg_replace('/[*_~#>-]+/m', ' ', $text) ?? $text;

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    protected function limit(string $value, int $limit): string
    {
        return Str::limit(trim($value), $limit, '...');
    }
}
