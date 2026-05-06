<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * @var \App\Services\Student\StudentDashboardService
     */
    protected StudentDashboardService $dashboardService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\Student\StudentDashboardService  $dashboardService
     */
    public function __construct(StudentDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display student dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $data = $this->dashboardService->build($request->user());

        return view('pages.student.dashboard', $data);
    }
}

