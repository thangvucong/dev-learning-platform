<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CourseClassStudentSeeder extends Seeder
{
    /**
     * Backward-compatible wrapper for older seed command usage.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ClassEnrollmentSeeder::class);
    }
}
