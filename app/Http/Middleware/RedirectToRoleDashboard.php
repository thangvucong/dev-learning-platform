<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\AuthRedirect;
use Closure;
use Illuminate\Http\Request;

class RedirectToRoleDashboard
{
    /**
     * Redirect authenticated users to role workspace.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user instanceof User) {
            return $next($request);
        }

        $target = AuthRedirect::to($user);

        // Only redirect when user is trying to access generic dashboard.
        // Prevent redirect loops by skipping if already on target path.
        if ($request->path() === 'dashboard' && ltrim($target, '/') !== $request->path()) {
            return redirect()->to($target);
        }

        return $next($request);
    }
}

