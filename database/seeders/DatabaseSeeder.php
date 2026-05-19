<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CourseSeeder::class,
            CourseDiscountSeeder::class,
            TrackSeeder::class,
            CourseAttributeSeeder::class,
            PostSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            EnrollmentSeeder::class,
            CourseClassSeeder::class,
            ClassSessionSeeder::class,
            ClassEnrollmentSeeder::class,
        ]);
    }
}
