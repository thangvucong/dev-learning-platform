<?php

namespace App\Http\Controllers\Admin;

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
        return view('components.admin.managerCourses');
    }

    public function getListData(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $data = $this->courseService->getManagerListData($perPage);
        
        return response()->json($data);
    }
}