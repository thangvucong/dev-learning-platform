<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\CourseClass;
use Illuminate\Database\Seeder;

class ClassSessionSeeder extends Seeder
{
    /**
     * Seed class session data.
     *
     * @return void
     */
    public function run()
    {
        ClassSession::query()->delete();

        $classes = CourseClass::query()
            ->orderBy('course_id')
            ->orderBy('start_at')
            ->get();

        if ($classes->isEmpty()) {
            throw new \RuntimeException(
                'ClassSessionSeeder: không có lớp học nào. Chạy CourseClassSeeder trước khi seed class_sessions.'
            );
        }

        $classes->each(function (CourseClass $courseClass) {
            for ($sessionNo = 1; $sessionNo <= 10; $sessionNo++) {
                ClassSession::factory()
                    ->forClass($courseClass, $sessionNo)
                    ->create();
            }
        });
    }
}
