<?php

namespace App\ViewModels;

class HomeViewModel
{
    /**
     * Build the view data payload for the home page.
     *
     * @param  array<string, \Illuminate\Support\Collection>  $sourceData
     * @return array<string, \Illuminate\Support\Collection>
     */
    public function build(array $sourceData): array
    {
        return [
            'courses' => $sourceData['courses']->map(function ($course) {
                return $this->mapCourseCard($course);
            }),
            'posts' => $sourceData['posts']->map(function ($post) {
                return $this->mapPostCard($post);
            }),
        ];
    }

    /**
     * Map a course model to the home page card payload.
     *
     * @param  mixed  $course
     * @return array<string, mixed>
     */
    protected function mapCourseCard($course): array
    {
        $activePrice = $this->resolveActivePrice($course);
        $nearestOpeningClass = $this->resolveNearestOpeningClass($course);

        return [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'description' => $course->description,
            'thumbnail_url' => $course->thumbnail_url,
            'price' => optional($activePrice)->price,
            'old_price' => optional($activePrice)->compare_price,
            'currency_symbol' => optional(optional($activePrice)->currency)->symbol,
            'duration' => $course->duration,
            'published_at' => optional($course->published_at)->toDateTimeString(),
            'next_opening_at' => optional(optional($nearestOpeningClass)->start_at)->toDateTimeString(),
            'user' => $course->instructor ? [
                'id' => $course->instructor->id,
                'name' => $course->instructor->name,
                'email' => $course->instructor->email,
                'avatar_url' => $course->instructor->avatar_url,
            ] : null,
        ];
    }

    /**
     * Resolve the active course price for the home page payload.
     *
     * @param  mixed  $course
     * @return mixed
     */
    protected function resolveActivePrice($course)
    {
        return $course->prices->first();
    }

    /**
     * Resolve the nearest opening class for the home page payload.
     *
     * @param  mixed  $course
     * @return mixed
     */
    protected function resolveNearestOpeningClass($course)
    {
        return $course->classes->first();
    }

    /**
     * Map a post model to the home page card payload.
     *
     * @param  mixed  $post
     * @return array<string, mixed>
     */
    protected function mapPostCard($post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'description' => $post->description,
            'thumbnail' => $post->thumbnail,
            'published_at' => optional($post->published_at)->toDateTimeString(),
            'user' => $post->user ? [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'email' => $post->user->email,
                'avatar_url' => $post->user->avatar_url,
            ] : null,
        ];
    }
}
