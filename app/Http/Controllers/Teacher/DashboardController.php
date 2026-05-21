<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\Teacher\TeacherDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected TeacherDashboardService $dashboardService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\Teacher\TeacherDashboardService  $dashboardService
     */
    public function __construct(TeacherDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display teacher dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('pages.teacher.dashboard', $this->dashboardService->build($request->user()));
    }
}
