<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use Illuminate\Http\Request;
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

        return view('pages.courses.index', [
            'course' => $courseDetailData['course'],
            'instructor' => $courseDetailData['instructor'],
            'classes' => $courseDetailData['classes'],
        ]);
    }


    // Thêm vào trong class CourseController

/**
 * Cập nhật giảng viên mới cho khóa học
 * * @param \Illuminate\Http\Request $request
 * @param int $id (ID của khóa học)
 */
public function updateInstructor(\Illuminate\Http\Request $request, $id)
{
   
    $request->validate([
        'instructor_id' => 'required|exists:users,id', 
    ]);

    $result = $this->courseService->updateCourseInstructor($id, $request->instructor_id);

    if ($result) {
        return back()->with('success', 'Đã thay đổi giảng viên thành công!');
    }

    return back()->with('error', 'Có lỗi xảy ra khi cập nhật.');
}
}
