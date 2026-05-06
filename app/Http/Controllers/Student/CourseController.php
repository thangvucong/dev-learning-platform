<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentCourseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * @var \App\Services\Student\StudentCourseService
     */
    protected StudentCourseService $courseService;

    /**
     * @param  \App\Services\Student\StudentCourseService  $courseService
     */
    public function __construct(StudentCourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Show student courses page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->query('q', ''),
            'status' => $request->query('status', ''),
        ];

        $payload = $this->courseService->buildList($request->user(), $filters);

        return view('pages.student.courses.index', $payload);
    }

    /**
     * Show one course detail page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, int $id): View
    {
        $payload = $this->courseService->buildDetail($request->user(), $id);

        return view('pages.student.courses.show', $payload);
    }
}

