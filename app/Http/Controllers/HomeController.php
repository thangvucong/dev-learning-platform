<?php

namespace App\Http\Controllers;

use App\Services\HomeService;

class HomeController extends Controller
{
    protected HomeService $homeService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\HomeService  $homeService
     */
    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Display home page with courses and posts.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $homeData = $this->homeService->getHomePageSourceData();

        return view('pages.home.index', [
            'courses' => $homeData['courses'],
            'posts' => $homeData['posts'],
            'bannerCourses' => $homeData['bannerCourses'] ?? [],
        ]);
    }
}
