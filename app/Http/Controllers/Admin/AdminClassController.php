<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddClassStudentsRequest;
use App\Http\Requests\Admin\ImportClassStudentsRequest;
use App\Http\Requests\Admin\StoreCourseClassRequest;
use App\Models\CourseClass;
use App\Models\User;
use App\Services\CourseClassManagementService;
use App\Services\CourseClassService;
use App\Services\CourseClassStudentImportService;
use App\Services\CourseClassStudentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminClassController extends Controller
{
    protected $classService;
    protected CourseClassManagementService $classManagementService;
    protected CourseClassStudentService $studentService;
    protected CourseClassStudentImportService $studentImportService;

    public function __construct(
        CourseClassService $classService,
        CourseClassManagementService $classManagementService,
        CourseClassStudentService $studentService,
        CourseClassStudentImportService $studentImportService
    )
    {
        $this->classService = $classService;
        $this->classManagementService = $classManagementService;
        $this->studentService = $studentService;
        $this->studentImportService = $studentImportService;
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

    public function store(StoreCourseClassRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $class = $this->classManagementService->createClass($payload);

        return response()->json([
            'success' => true,
            'message' => 'Tạo lớp học thành công.',
            'data' => $class,
            'meta' => [
                'sessions_created' => (int) $class->sessions->count(),
            ],
        ], 201);
    }

    public function addStudents(CourseClass $courseClass, AddClassStudentsRequest $request): JsonResponse
    {
        $members = (string) $request->validated()['members'];
        $tokens = preg_split('/[,\r\n\t ]+/', $members, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $userIds = [];
        $emails = [];
        foreach ($tokens as $token) {
            $token = trim((string) $token);
            if ($token === '') {
                continue;
            }

            if (is_numeric($token)) {
                $userIds[] = (int) $token;
                continue;
            }

            if (filter_var($token, FILTER_VALIDATE_EMAIL)) {
                $emails[] = strtolower($token);
            }
        }

        $userIds = array_values(array_values(array_unique($userIds)));
        $emails = array_values(array_values(array_unique($emails)));

        $resolvedIds = [];
        if ($userIds !== []) {
            $resolvedIds = User::query()->whereIn('id', $userIds)->pluck('id')->map(static fn ($id) => (int) $id)->all();
        }
        if ($emails !== []) {
            $emailIds = User::query()->whereIn('email', $emails)->pluck('id')->map(static fn ($id) => (int) $id)->all();
            $resolvedIds = array_values(array_unique(array_merge($resolvedIds, $emailIds)));
        }

        $result = $this->studentService->addStudents($courseClass, $resolvedIds);

        return response()->json($result);
    }

    public function importStudents(CourseClass $courseClass, ImportClassStudentsRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $tokens = $this->studentImportService->extractMemberTokens($file);

        $userIds = [];
        $emails = [];
        foreach ($tokens as $token) {
            $token = trim((string) $token);
            if ($token === '') {
                continue;
            }

            if (is_numeric($token)) {
                $userIds[] = (int) $token;
                continue;
            }

            if (filter_var($token, FILTER_VALIDATE_EMAIL)) {
                $emails[] = strtolower($token);
            }
        }

        $userIds = array_values(array_unique($userIds));
        $emails = array_values(array_unique($emails));

        $resolvedIds = [];
        if ($userIds !== []) {
            $resolvedIds = User::query()->whereIn('id', $userIds)->pluck('id')->map(static fn ($id) => (int) $id)->all();
        }
        if ($emails !== []) {
            $emailIds = User::query()->whereIn('email', $emails)->pluck('id')->map(static fn ($id) => (int) $id)->all();
            $resolvedIds = array_values(array_unique(array_merge($resolvedIds, $emailIds)));
        }

        $result = $this->studentService->addStudents($courseClass, $resolvedIds);

        return response()->json($result);
    }
}