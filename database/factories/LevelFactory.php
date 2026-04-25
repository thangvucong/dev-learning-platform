<?php

namespace Database\Factories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    protected $model = Level::class;

    public function definition()
    {
        $names = ['Beginner', 'Intermediate', 'Advanced'];
        $name = $this->faker->randomElement($names);

        return [
            'name' => $name,
            'description' => 'Suitable for ' . strtolower($name) . ' learners on the platform.',
        ];
    }
}
