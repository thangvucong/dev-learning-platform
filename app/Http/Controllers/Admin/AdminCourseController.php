<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreCourseRequest;
use App\Services\CourseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        return view('components.admin.managerCourses', $this->courseService->getManagersCourseIndexViewData());
    }

    public function getListData(Request $request)
    {
        $perPage = (int) $request->get('perPage', 10);
        $perPage = max(1, min($perPage, 100));
        $data = $this->courseService->getManagerListData($perPage);
        
        return response()->json($data);
    }

    public function store(StoreCourseRequest $request)
    {
        $this->courseService->createCourseForAdmin($request->validated());

        return redirect()
            ->route('admin.courses.managerCourses')
            ->with('success', 'Tạo khóa học thành công.');
    }
}