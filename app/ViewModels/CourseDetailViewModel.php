<?php

namespace App\ViewModels;

class CourseDetailViewModel
{
    /**
     * Build the view data payload for the course detail page.
     *
     * @param  array<string, mixed>  $sourceData
     * @return array<string, mixed>
     */
    public function build(array $sourceData): array
    {
        $course = $sourceData['course'];
        $activePrice = $sourceData['prices']->first();
        $nearestOpeningClass = $this->resolveNearestOpeningClass($sourceData['classes']);
        $tracks = $sourceData['tracks']
            ->whereNull('parent_id')
            ->values()
            ->map(function ($track) {
                return [
                    'id' => $track->id,
                    'title' => $track->title,
                    'description' => $track->description,
                    'position' => $track->position,
                    'children' => $track->children
                        ->sortBy('position')
                        ->values()
                        ->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'title' => $child->title,
                                'description' => $child->description,
                                'position' => $child->position,
                            ];
                        }),
                ];
            });

        return [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => $course->description,
                'thumbnail_url' => $course->thumbnail_url,
                'intro_video_url' => $course->intro_video_url,
                'duration' => $course->duration,
                'is_free' => $course->is_free,
                'published_at' => optional($course->published_at)->toDateTimeString(),
                'price' => optional($activePrice)->price,
                'old_price' => optional($activePrice)->compare_price,
                'currency_symbol' => optional(optional($activePrice)->currency)->symbol,
                'level' => $sourceData['level'] ? [
                    'id' => $sourceData['level']->id,
                    'name' => $sourceData['level']->name,
                    'description' => $sourceData['level']->description,
                ] : null,
                'instructor' => $sourceData['instructor'] ? [
                    'id' => $sourceData['instructor']->id,
                    'name' => $sourceData['instructor']->name,
                    'email' => $sourceData['instructor']->email,
                    'avatar_url' => $sourceData['instructor']->avatar_url,
                ] : null,
                'next_opening_at' => optional(optional($nearestOpeningClass)->start_at)->toDateTimeString(),
                'next_class' => $nearestOpeningClass ? [
                    'id' => $nearestOpeningClass->id,
                    'name' => $nearestOpeningClass->name,
                    'code' => $nearestOpeningClass->code,
                    'mode' => $nearestOpeningClass->mode,
                    'status' => $nearestOpeningClass->status,
                    'capacity' => $nearestOpeningClass->capacity,
                    'start_at' => optional($nearestOpeningClass->start_at)->toDateTimeString(),
                    'end_at' => optional($nearestOpeningClass->end_at)->toDateTimeString(),
                    'location' => $nearestOpeningClass->location,
                ] : null,
            ],
            'summary' => [
                'chapters_count' => $tracks->count(),
                'lessons_count' => $tracks->sum(function ($track) {
                    return $track['children']->count();
                }),
                'duration_human' => $this->formatDurationMinutes((int) $course->duration),
            ],
            'tracks' => $tracks,
            'requirements' => $this->mapAttributesByType($sourceData['attributes'], 'requirement'),
            'benefits' => $this->mapAttributesByType($sourceData['attributes'], 'benefit'),
            'targets' => $this->mapAttributesByType($sourceData['attributes'], 'target'),
        ];
    }

    /**
     * Resolve the nearest opening class from the source collection.
     *
     * @param  \Illuminate\Support\Collection  $classes
     * @return mixed
     */
    protected function resolveNearestOpeningClass($classes)
    {
        $nearestUpcomingClass = $classes
            ->filter(function ($courseClass) {
                return !empty($courseClass->start_at) && $courseClass->start_at->greaterThanOrEqualTo(now());
            })
            ->sortBy('start_at')
            ->first();

        if ($nearestUpcomingClass) {
            return $nearestUpcomingClass;
        }

        return null;
    }

    /**
     * Map course attributes by type.
     *
     * @param  \Illuminate\Support\Collection  $attributes
     * @param  string  $type
     * @return \Illuminate\Support\Collection
     */
    protected function mapAttributesByType($attributes, string $type)
    {
        return $attributes
            ->where('type', $type)
            ->values()
            ->map(function ($attribute) {
                return [
                    'id' => $attribute->id,
                    'content' => $attribute->content,
                    'position' => $attribute->position,
                ];
            });
    }

    /**
     * Format duration from minutes to "X giờ Y phút".
     *
     * @param  int  $minutes
     * @return string
     */
    protected function formatDurationMinutes(int $minutes): string
    {
        if ($minutes <= 0) {
            return '0 phút';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours <= 0) {
            return $remainingMinutes . ' phút';
        }

        if ($remainingMinutes <= 0) {
            return $hours . ' giờ';
        }

        return $hours . ' giờ ' . $remainingMinutes . ' phút';
    }
}
