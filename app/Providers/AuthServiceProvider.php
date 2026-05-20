<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Order;
use App\Models\Post;
use App\Policies\CourseClassPolicy;
use App\Policies\CoursePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Course::class => CoursePolicy::class,
        CourseClass::class => CourseClassPolicy::class,
        Post::class => PostPolicy::class,
        Order::class => OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
