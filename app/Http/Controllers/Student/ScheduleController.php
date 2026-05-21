<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\SessionAssignment;
use App\Services\Student\StudentScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * @var \App\Services\Student\StudentScheduleService
     */
    protected StudentScheduleService $scheduleService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\Student\StudentScheduleService  $scheduleService
     */
    public function __construct(StudentScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display student schedule page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $filters = $this->resolveFilters($request);

        $payload = $this->scheduleService->build($request->user(), $filters);

        return view('pages.student.schedule.index', $payload);
    }

    /**
     * Return schedule payload as JSON for async calendar updates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $filters = $this->resolveFilters($request);
        $payload = $this->scheduleService->build($request->user(), $filters);

        return response()->json($payload);
    }

    public function assignments(Request $request, ClassSession $classSession): JsonResponse
    {
        return response()->json(
            $this->scheduleService->buildAssignments($request->user(), $classSession)
        );
    }

    public function submitAssignment(Request $request, SessionAssignment $sessionAssignment): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:20480'],
        ]);

        return response()->json(
            $this->scheduleService->submitAssignment(
                $request->user(),
                $sessionAssignment,
                $validated,
                $request->file('attachment')
            )
        );
    }

    /**
     * Normalize query filters for schedule state.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
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
