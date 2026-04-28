<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use App\ViewModels\HomeViewModel;

class HomeController extends Controller
{
    protected HomeService $homeService;

    protected HomeViewModel $homeViewModel;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\HomeService  $homeService
     * @param  \App\ViewModels\HomeViewModel  $homeViewModel
     */
    public function __construct(HomeService $homeService, HomeViewModel $homeViewModel)
    {
        $this->homeService = $homeService;
        $this->homeViewModel = $homeViewModel;
    }

    /**
     * Display home page with courses and posts.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $homeSourceData = $this->homeService->getHomePageSourceData();
        $homeViewData = $this->homeViewModel->build($homeSourceData);

        return view('pages.home.index', [
            'homeData' => $homeViewData,
        ]);
    }
}
