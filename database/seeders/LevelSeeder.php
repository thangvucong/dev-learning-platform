<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run()
    {
        Level::query()->delete();

        Level::factory()
            ->count(3)
            ->sequence(
                [
                    'name' => 'Beginner',
                    'description' => 'For learners who are just starting with web development.',
                ],
                [
                    'name' => 'Intermediate',
                    'description' => 'For developers with basic experience building web applications.',
                ],
                [
                    'name' => 'Advanced',
                    'description' => 'For experienced developers focusing on architecture and scaling.',
                ]
            )
            ->create();
    }
}
