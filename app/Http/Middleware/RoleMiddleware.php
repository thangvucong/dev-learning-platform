<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(401, 'Unauthenticated');
        }

        $user = Auth::user();

        if (empty($roles)) {
            return $next($request);
        }

        $roles = collect($roles)
            ->flatMap(function ($role) {
                return explode('|', $role);
            })
            ->map(function ($role) {
                return trim($role);
            })
            ->filter()
            ->values()
            ->toArray();

        $userRole = $user->role ?? null;
        $hasRole = $userRole !== null && $userRole !== '' && in_array($userRole, $roles, true);

        if (!$hasRole) {
            abort(403, 'Forbidden - Insufficient role');
        }

        return $next($request);
    }
}
