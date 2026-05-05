<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreCourseRequest;
use App\Models\Currency;
use App\Models\User;
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
        $instructors = User::query()
            ->role('teacher')
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();

        $currencies = Currency::query()
            ->where('is_active', true)
            ->select(['id', 'code', 'symbol'])
            ->orderBy('code')
            ->get();

        return view('components.admin.managerCourses', [
            'instructors' => $instructors,
            'currencies' => $currencies,
        ]);
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