<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\Teacher\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class TeacherClassController extends Controller
{
    protected $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }
   public function indexView()
{
    $user = auth()->user();
    $teacherId = $user->id;

 
    $courseCount = \App\Models\Course::query()
        ->whereHas('classes', function ($query) use ($teacherId) {
            $query->where('instructor_id', $teacherId);
        })
        ->count();
    
 
    $studentCount = DB::table('class_enrollments')
        ->join('classes', 'class_enrollments.class_id', '=', 'classes.id')
        ->where('classes.instructor_id', $teacherId)
        ->count();

    $welcome = [
        'name' => $user->name,
        'avatar' => $user->avatar_url,
        'greeting' => 'Chào giảng viên',
        'today_classes' => 2 
    ];

    $stats = [
        ['title' => 'KHÓA HỌC ĐANG DẠY', 'value' => $courseCount, 'suffix' => 'khóa', 'icon' => 'fa-book', 'tone' => 'emerald'],
        ['title' => 'TỔNG HỌC VIÊN', 'value' => $studentCount, 'suffix' => 'bạn', 'icon' => 'fa-users', 'tone' => 'blue'],
        ['title' => 'GIỜ DẠY THÁNG NÀY', 'value' => 45, 'suffix' => 'giờ', 'icon' => 'fa-clock', 'tone' => 'purple'],
        ['title' => 'ĐÁNH GIÁ TRUNG BÌNH', 'value' => 4.9, 'suffix' => '/5', 'icon' => 'fa-star', 'tone' => 'amber'],
    ];

    return view('pages.teacher.dashboardTeacher', compact('welcome', 'stats'));
}

    /**
     * API trả về danh sách lớp học của giảng viên đang đăng nhập
     */
   /**
     * API trả về danh sách lớp học kèm sinh viên của giảng viên đang đăng nhập
     */
    public function index(): JsonResponse
    {
        try {
            $teacherId = Auth::id(); //

        
            $classes = $this->teacherService->getClassesWithStudents($teacherId); //

            return response()->json([
                'success' => true,
                'data'    => $classes,
                'message' => 'Lấy danh sách lớp học thành công.'
            ], 200); //
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500); //
        }
    }
    public function getSchedule(): JsonResponse
{
    try {
      
        $schedule = $this->teacherService->getTeacherMonthlySchedule(Auth::id()); 
        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    
    
}
