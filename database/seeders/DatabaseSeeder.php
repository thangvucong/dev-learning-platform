<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            LevelSeeder::class,
            CourseSeeder::class,
            TrackSeeder::class,
            CourseAttributeSeeder::class,
            CurrencySeeder::class,
            CoursePriceSeeder::class,
            UserSeeder::class,
            PostSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
        ]);
    }
}
