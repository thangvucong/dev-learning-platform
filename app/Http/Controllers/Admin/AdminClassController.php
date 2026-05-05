<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CourseClassService;
use Illuminate\Http\Request;

class AdminClassController extends Controller
{
    protected $classService;

    public function __construct(CourseClassService $classService)
    {
        $this->classService = $classService;
    }

    public function index()
    {
        // Trả về view quản lý lớp học
        return view('components.admin.managerClasses');
    }

    public function getListData(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $data = $this->classService->getListClasses($perPage);
        
        return response()->json($data);
    }
}