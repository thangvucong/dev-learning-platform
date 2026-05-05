<?php

namespace App\Http\Controllers;

use App\Services\CourseService;

class CourseController extends Controller
{
    protected CourseService $courseService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\CourseService  $courseService
     */
    public function __construct( CourseService $courseService) 
    {
        $this->courseService = $courseService;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function show($slug)
    {
        $courseDetailData = $this->courseService->getCourseDetailSourceData($slug);

        return view('pages.courses.index', [
            'course' => $courseDetailData['course'],
            'instructor' => $courseDetailData['instructor'],
            'classes' => $courseDetailData['classes'],
        ]);
    }
}
