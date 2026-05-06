<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentScheduleService;
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
        $filters = [
            'week_offset' => (int) $request->query('week_offset', 0),
            'view' => (string) $request->query('view', 'week'),
            'class_id' => (int) $request->query('class_id', 0),
            'session_id' => (string) $request->query('session_id', ''),
        ];

        $payload = $this->scheduleService->build($request->user(), $filters);

        return view('pages.student.schedule.index', $payload);
    }
}

