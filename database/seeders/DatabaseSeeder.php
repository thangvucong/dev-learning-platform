<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            LevelSeeder::class,
            UserSeeder::class,
            CourseSeeder::class,
            TrackSeeder::class,
            CourseAttributeSeeder::class,
            CurrencySeeder::class,
            CoursePriceSeeder::class,
            PostSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            EnrollmentSeeder::class,
            CourseClassSeeder::class,
            CourseClassStudentSeeder::class,
        ]);
    }
}
