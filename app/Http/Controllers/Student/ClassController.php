<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentClassService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    /**
     * @var \App\Services\Student\StudentClassService
     */
    protected StudentClassService $classService;

    /**
     * @param  \App\Services\Student\StudentClassService  $classService
     */
    public function __construct(StudentClassService $classService)
    {
        $this->classService = $classService;
    }

    /**
     * Show "Lớp học của tôi" list page.
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

        $payload = $this->classService->buildList($request->user(), $filters);

        return view('pages.student.classes.index', $payload);
    }

    /**
     * Show one class detail workspace.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, int $id): View
    {
        $payload = $this->classService->buildDetail($request->user(), $id);

        return view('pages.student.classes.show', $payload);
    }
}

