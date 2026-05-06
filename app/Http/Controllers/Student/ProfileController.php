<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentProfileService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * @var \App\Services\Student\StudentProfileService
     */
    protected StudentProfileService $profileService;

    /**
     * @param  \App\Services\Student\StudentProfileService  $profileService
     */
    public function __construct(StudentProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Show learning profile page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $payload = $this->profileService->build($request->user());

        return view('pages.student.profile.index', $payload);
    }
}

