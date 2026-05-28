<?php

namespace App\Providers;

use App\Services\Interfaces\MyPostServiceInterface;
use App\Services\Interfaces\PostServiceInterface;
use App\Services\MyPostService;
use App\Services\PostService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PostServiceInterface::class, PostService::class);
        $this->app->bind(MyPostServiceInterface::class, MyPostService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $appUrl = (string) config('app.url', '');
        $shouldForceHttps = config('app.force_https') === true
            || strpos($appUrl, 'https://') === 0;

        if ($shouldForceHttps) {
            URL::forceScheme('https');
        }
    }
}
