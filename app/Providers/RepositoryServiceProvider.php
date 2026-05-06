<?php

namespace App\Providers;

use App\Repositories\CourseRepository;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
    }
}
