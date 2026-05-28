<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseAttribute;
use Illuminate\Database\Seeder;

class CourseAttributeSeeder extends Seeder
{
    public function run()
    {
        CourseAttribute::query()->delete();

        $courses = Course::query()
            ->select(['id'])
            ->orderBy('id')
            ->get();

        if ($courses->isEmpty()) {
            throw new \RuntimeException(
                'CourseAttributeSeeder: không có khóa học nào. Chạy CourseSeeder trước khi seed course_attributes.'
            );
        }

        $courses->each(function (Course $course) {
            CourseAttribute::factory()
                ->count(3)
                ->requirement()
                ->state([
                    'course_id' => $course->id,
                ])
                ->sequence(
                    ['content' => 'Có máy tính cá nhân và kết nối internet ổn định.'],
                    ['content' => 'Nắm được kiến thức lập trình căn bản hoặc sẵn sàng học từ đầu.'],
                    ['content' => 'Dành thời gian thực hành sau mỗi buổi học.']
                )
                ->create();

            CourseAttribute::factory()
                ->count(3)
                ->benefit()
                ->state([
                    'course_id' => $course->id,
                ])
                ->sequence(
                    ['content' => 'Xây dựng được sản phẩm thực tế theo từng giai đoạn học.'],
                    ['content' => 'Hiểu cách tổ chức mã nguồn rõ ràng, dễ bảo trì.'],
                    ['content' => 'Có nền tảng để tham gia dự án hoặc phát triển sản phẩm cá nhân.']
                )
                ->create();

            CourseAttribute::factory()
                ->count(3)
                ->target()
                ->state([
                    'course_id' => $course->id,
                ])
                ->sequence(
                    ['content' => 'Người mới bắt đầu muốn học lập trình web bài bản.'],
                    ['content' => 'Sinh viên hoặc junior developer cần củng cố kỹ năng thực chiến.'],
                    ['content' => 'Người đi làm muốn chuyển hướng sang phát triển phần mềm.']
                )
                ->create();
        });
    }
}
