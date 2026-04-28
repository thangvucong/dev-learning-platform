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
            ->flatMap(fn ($role) => explode('|', $role))
            ->map(fn ($role) => trim($role))
            ->filter();

        $hasRole = method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole($roles->toArray())
            : in_array($user->role ?? null, $roles->toArray());

        if (!$hasRole) {
            abort(403, 'Forbidden - Insufficient role');
        }

        return $next($request);
    }
}
