<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use App\ViewModels\CourseDetailViewModel;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected CourseService $courseService;

    protected CourseDetailViewModel $courseDetailViewModel;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\CourseService  $courseService
     * @param  \App\ViewModels\CourseDetailViewModel  $courseDetailViewModel
     */
    public function __construct(
        CourseService $courseService,
        CourseDetailViewModel $courseDetailViewModel
    )
    {
        $this->courseService = $courseService;
        $this->courseDetailViewModel = $courseDetailViewModel;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function show($slug)
    {
        $courseDetailSourceData = $this->courseService->getCourseDetailSourceData($slug);
        $courseDetailViewData = $this->courseDetailViewModel->build($courseDetailSourceData);

        // dd($courseDetailViewData);

        return view('pages.courses.index', [
            'courseDetailData' => $courseDetailViewData,
        ]);
    }
}
