<?php

namespace App\Providers;

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
        //
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
