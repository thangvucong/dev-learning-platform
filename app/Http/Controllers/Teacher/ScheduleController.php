<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\GradeAssignmentSubmissionRequest;
use App\Http\Requests\Teacher\StoreSessionAssignmentRequest;
use App\Models\AssignmentSubmission;
use App\Models\ClassSession;
use App\Models\CourseClass;
use App\Models\SessionAssignment;
use App\Services\Teacher\TeacherScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    protected TeacherScheduleService $scheduleService;

    public function __construct(TeacherScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request): View
    {
        $payload = $this->scheduleService->build($request->user(), $this->resolveFilters($request));

        return view('pages.teacher.schedule.index', $payload);
    }

    public function data(Request $request): JsonResponse
    {
        $payload = $this->scheduleService->build($request->user(), $this->resolveFilters($request));

        return response()->json($payload);
    }

    public function attendance(Request $request, ClassSession $classSession): JsonResponse
    {
        return response()->json(
            $this->scheduleService->buildAttendance($request->user(), $classSession)
        );
    }

    public function ensureAttendanceSession(Request $request, CourseClass $courseClass): JsonResponse
    {
        return response()->json(
            $this->scheduleService->ensureClassSessionForAttendance($request->user(), $courseClass)
        );
    }

    public function updateAttendance(Request $request, ClassSession $classSession, int $student): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:present,late,absent,excused'],
        ]);

        return response()->json(
            $this->scheduleService->updateStudentAttendance($request->user(), $classSession, $student, $validated['status'])
        );
    }

    public function bulkAttendance(Request $request, ClassSession $classSession): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:present,late,absent,excused'],
        ]);

        return response()->json(
            $this->scheduleService->bulkUpdateAttendance($request->user(), $classSession, $validated['status'])
        );
    }

    public function assignments(Request $request, ClassSession $classSession): JsonResponse
    {
        return response()->json(
            $this->scheduleService->buildAssignments($request->user(), $classSession)
        );
    }

    public function storeAssignment(StoreSessionAssignmentRequest $request, ClassSession $classSession): RedirectResponse
    {
        $this->scheduleService->createAssignment(
            $request->user(),
            $classSession,
            $request->validated(),
            $request->file('attachment')
        );

        toastr('Đã tạo bài tập thành công.', 'success');

        return back();
    }

    public function assignmentSubmissions(Request $request, SessionAssignment $sessionAssignment): JsonResponse
    {
        return response()->json(
            $this->scheduleService->buildAssignmentSubmissions($request->user(), $sessionAssignment)
        );
    }

    public function gradeSubmission(GradeAssignmentSubmissionRequest $request, AssignmentSubmission $assignmentSubmission): JsonResponse
    {
        return response()->json(
            $this->scheduleService->gradeSubmission(
                $request->user(),
                $assignmentSubmission,
                $request->validated()
            )
        );
    }

    protected function resolveFilters(Request $request): array
    {
        $view = (string) $request->query('view', 'week');
        if (!in_array($view, ['day', 'week', 'month', 'list'], true)) {
            $view = 'week';
        }

        return [
            'week_offset' => (int) $request->query('week_offset', 0),
            'view' => $view,
            'class_id' => (int) $request->query('class_id', 0),
            'session_id' => (string) $request->query('session_id', ''),
        ];
    }
}
