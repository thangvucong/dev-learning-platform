<?php

namespace App\Services;

use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Log;

class HomeService
{
    protected const COURSE_LIMIT = 8;

    protected const POST_LIMIT = 10;

    protected const HERO_BANNER_LIMIT = 4;

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
                'bannerCourses' => $this->buildHeroBannerCourses($courses),
            ];
        } catch (\Throwable $th) {
            Log::error('Error getting home page source data: ' . $th->getMessage());

            return [
                'courses' => [],
                'posts' => [],
                'bannerCourses' => $this->buildHeroBannerCourses(collect()),
            ];
        }
    }

    /**
     * Build the hero banner slide items from available courses.
     *
     * @param  \Illuminate\Support\Collection<int, mixed>|\Illuminate\Database\Eloquent\Collection<int, mixed>  $courses
     * @return array<int, array<string, mixed>>
     */
    protected function buildHeroBannerCourses($courses): array
    {
        $gradients = [
            'linear-gradient(90deg, #fe5f2a 0%, #ff9820 100%)',
            'linear-gradient(90deg, #1d4ed8 0%, #06b6d4 100%)',
            'linear-gradient(90deg, #7c3aed 0%, #ec4899 100%)',
            'linear-gradient(90deg, #0f766e 0%, #22c55e 100%)',
        ];

        $fallback = [
            [
                'title' => 'Laravel Backend',
                'description' => 'Xây nền tảng backend vững chắc với Laravel: routing, Eloquent, auth, queue, deploy.',
                'button_text' => 'Xem lộ trình',
                'button_url' => '#',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80',
                'background_gradient' => $gradients[0],
                'badge_text' => 'Hot',
            ],
            [
                'title' => 'Fullstack Zoom',
                'description' => 'Học live, được review code trực tiếp, mentor & trợ giảng đồng hành xuyên suốt.',
                'button_text' => 'Nhận tư vấn',
                'button_url' => '#',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1200&q=80',
                'background_gradient' => $gradients[1],
                'badge_text' => 'Live',
            ],
            [
                'title' => 'Docker cho Laravel',
                'description' => 'Chuẩn hoá môi trường dev, build & deploy nhanh, an toàn, dễ scale theo team.',
                'button_text' => 'Bắt đầu ngay',
                'button_url' => '#',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1605379399642-870262d3d051?auto=format&fit=crop&w=1200&q=80',
                'background_gradient' => $gradients[2],
                'badge_text' => null,
            ],
            [
                'title' => 'Tailwind UI',
                'description' => 'Thiết kế UI hiện đại cho web app: layout, component, responsive & motion tinh tế.',
                'button_text' => 'Khám phá',
                'button_url' => '#',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1559028012-481c04fa702d?auto=format&fit=crop&w=1200&q=80',
                'background_gradient' => $gradients[3],
                'badge_text' => 'New',
            ],
        ];

        if (empty($courses) || (method_exists($courses, 'isEmpty') && $courses->isEmpty())) {
            return array_slice($fallback, 0, self::HERO_BANNER_LIMIT);
        }

        $items = [];
        $coursesSlice = method_exists($courses, 'take')
            ? $courses->take(self::HERO_BANNER_LIMIT)->values()
            : collect($courses)->take(self::HERO_BANNER_LIMIT)->values();

        foreach ($coursesSlice as $index => $course) {
            $title = (string) ($course->title ?? $course['title'] ?? $fallback[$index]['title']);
            $description = (string) ($course->short_description ?? $course['short_description'] ?? $fallback[$index]['description']);
            $slug = (string) ($course->slug ?? $course['slug'] ?? '');
            $thumbnailUrl = (string) ($course->thumbnail_url ?? $course['thumbnail_url'] ?? '');
            if (empty($thumbnailUrl)) {
                $thumbnailUrl = $fallback[$index]['thumbnail_url'];
            }

            $items[] = [
                'title' => $title ?: $fallback[$index]['title'],
                'description' => $description ?: $fallback[$index]['description'],
                'button_text' => 'Xem chi tiết',
                'button_url' => !empty($slug) ? route('courses.show', $slug) : '#',
                'thumbnail_url' => $thumbnailUrl,
                'background_gradient' => $gradients[$index % count($gradients)],
                'badge_text' => $index === 0 ? 'Nổi bật' : null,
            ];
        }

        return $items;
    }
}
