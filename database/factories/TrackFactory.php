<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackFactory extends Factory
{
    protected $model = Track::class;

    public function definition()
    {
        return [
            'course_id' => Course::factory(),
            'parent_id' => null,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->sentence(12),
            'position' => $this->faker->numberBetween(1, 20),
        ];
    }

    public function parent()
    {
        return $this->state(function () {
            return [
                'parent_id' => null,
            ];
        });
    }

    public function childOf(Track $track)
    {
        return $this->state(function () use ($track) {
            return [
                'course_id' => $track->course_id,
                'parent_id' => $track->id,
            ];
        });
    }
}
