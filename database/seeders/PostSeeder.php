<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Seed posts data.
     *
     * @return void
     */
    public function run()
    {
        Post::query()->delete();

        $userIds = User::query()->pluck('id')->all();

        if (empty($userIds)) {
            $userIds = User::factory()->count(3)->create()->pluck('id')->all();
        }

        Post::factory()
            ->count(15)
            ->published()
            ->state(function () use ($userIds) {
                return [
                    'user_id' => $userIds[array_rand($userIds)],
                ];
            })
            ->create();
    }
}
