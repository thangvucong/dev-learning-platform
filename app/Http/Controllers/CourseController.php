<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use App\Models\User;

class CourseController extends Controller
{
    protected CourseService $courseService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\CourseService  $courseService
     */
    public function __construct( CourseService $courseService) 
    {
        $this->courseService = $courseService;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function show($slug)
    {
        $courseDetailData = $this->courseService->getCourseDetailSourceData($slug);

        abort_if(empty($courseDetailData), 404);

        return view('pages.courses.index', [
            'course' => $courseDetailData['course'],
            'instructor' => $courseDetailData['instructor'],
            'classes' => $courseDetailData['classes'],
            'courseDetailData' => $courseDetailData['courseDetailData'],
        ]);
    }

    /**
     * Update instructor new for course
     * * @param \Illuminate\Http\Request $request
     * @param int $id
     */
    public function updateInstructor(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'instructor_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = User::query()
                        ->role(User::ROLE_INSTRUCTOR)
                        ->whereKey((int) $value)
                        ->exists();

                    if (!$exists) {
                        $fail('Giảng viên được chọn không hợp lệ.');
                    }
                },
            ],
        ]);

        $result = $this->courseService->updateCourseInstructor($id, $request->instructor_id);

        if ($result) {
            return back()->with('success', 'Đã thay đổi giảng viên thành công!');
        }

        return back()->with('error', 'Có lỗi xảy ra khi cập nhật.');
    }
}
