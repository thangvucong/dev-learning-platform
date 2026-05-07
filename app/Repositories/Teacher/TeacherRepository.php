<?php

namespace App\Repositories\Teacher;

use App\Models\Course;
use App\Models\CourseClass;
use Illuminate\Support\Facades\DB;

class TeacherRepository
{
    /**
     * Lấy danh sách lớp học của một giảng viên dựa trên instructor_id
     */
    public function getClassesByTeacher(int $teacherId)
    {
        return DB::table('classes')
            ->join('courses', 'classes.course_id', '=', 'courses.id')
            ->where('courses.instructor_id', $teacherId)
            ->select('classes.*', 'courses.title as course_name')
            ->get();
    }
    public function getClassesWithStudents(int $teacherId)
{
 
    return DB::table('classes')
        ->join('courses', 'classes.course_id', '=', 'courses.id')
        ->leftJoin('class_user', 'classes.id', '=', 'class_user.class_id')
        ->leftJoin('users', 'class_user.user_id', '=', 'users.id')
        ->where('courses.instructor_id', $teacherId)
        ->select(
            'classes.id as class_id',
            'classes.name as class_name',
            'classes.status as class_status',
            'courses.title as course_name',
            'users.id as student_id',
            'users.name as student_name',
            'users.email as student_email',
            'users.avatar_url as student_avatar'
        )
        ->get()
        ->groupBy('class_id')
        ->map(function ($items) {
            $first = $items->first();
            return [
                'id' => $first->class_id,
                'name' => $first->class_name,
                'status' => $first->class_status,
                'course_name' => $first->course_name,
              
                'students' => $items->whereNotNull('student_id')->map(function ($student) {
                    return [
                        'id' => $student->student_id,
                        'name' => $student->student_name,
                        'email' => $student->student_email,
                        'avatar' => $student->student_avatar
                    ];
                })->values()
            ];
        })->values();
}
}