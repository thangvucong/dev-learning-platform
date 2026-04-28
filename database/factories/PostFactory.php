<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->unique()->sentence(6);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::lower(Str::random(6)),
            'content' => $this->faker->paragraphs(8, true),
            'description' => $this->faker->paragraph(2),
            'thumbnail' => $this->faker->imageUrl(640, 360, 'business', true),
            'image' => $this->faker->imageUrl(1200, 630, 'business', true),
            'views_count' => $this->faker->numberBetween(0, 5000),
            'is_published' => $this->faker->boolean(80),
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the post is published.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published()
    {
        return $this->state(function () {
            return [
                'is_published' => true,
                'published_at' => now()->subDays($this->faker->numberBetween(1, 90)),
            ];
        });
    }
}
