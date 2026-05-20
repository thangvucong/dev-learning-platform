<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Traits\HasRoles;

class SidebarMenuBuilder
{
    /**
     * Build sidebar sections by user role.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return array<int, array<string, mixed>>
     */
    public static function forUser(?Authenticatable $user): array
    {
        $rolesConfig = (array) config('sidebar.roles', []);
        $role = self::resolveRole($user);
        $sections = (array) ($rolesConfig[$role] ?? $rolesConfig[config('sidebar.default_role', 'student')] ?? []);

        return array_values(array_map(function (array $section) {
            $items = array_map([self::class, 'normalizeItem'], (array) ($section['items'] ?? []));
            $items = array_values(array_filter($items));

            return [
                'section' => (string) ($section['section'] ?? ''),
                'items' => $items,
            ];
        }, $sections));
    }

    /**
     * Resolve role from authenticated user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return string
     */
    protected static function resolveRole(?Authenticatable $user): string
    {
        if ($user && in_array(HasRoles::class, class_uses_recursive($user), true)) {
            $role = (string) $user->getRoleNames()->first();

            return $role !== '' ? $role : (string) config('sidebar.default_role', 'student');
        }

        $role = (string) config('sidebar.default_role', 'student');

        return $role !== '' ? $role : (string) config('sidebar.default_role', 'student');
    }

    /**
     * Normalize one sidebar item including children/active/open state.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>|null
     */
    protected static function normalizeItem(array $item): ?array
    {
        $title = (string) ($item['title'] ?? '');
        if ($title === '') {
            return null;
        }

        $children = array_map([self::class, 'normalizeItem'], (array) ($item['children'] ?? []));
        $children = array_values(array_filter($children));

        $routeName = (string) ($item['route'] ?? '');
        $activePatterns = (array) ($item['active'] ?? []);
        if ($routeName !== '' && $activePatterns === []) {
            $activePatterns[] = $routeName;
        }

        $isActive = self::isActiveRoute($activePatterns);
        $hasChildren = $children !== [];
        $hasActiveChild = collect($children)->contains(function (array $child) {
            return (bool) ($child['is_active'] ?? false) || (bool) ($child['is_open'] ?? false);
        });

        return [
            'title' => $title,
            'icon' => (string) ($item['icon'] ?? 'fa-regular fa-circle'),
            'route' => $routeName,
            'url' => self::resolveUrl($routeName),
            'has_route' => self::hasRoute($routeName),
            'is_active' => $isActive,
            'is_open' => $isActive || $hasActiveChild,
            'children' => $children,
            'has_children' => $hasChildren,
        ];
    }

    /**
     * Determine if any route pattern matches current route.
     *
     * @param  array<int, string>  $patterns
     * @return bool
     */
    protected static function isActiveRoute(array $patterns): bool
    {
        if ($patterns === []) {
            return false;
        }

        return request()->routeIs($patterns);
    }

    /**
     * Resolve URL from route name.
     *
     * @param  string  $routeName
     * @return string
     */
    protected static function resolveUrl(string $routeName): string
    {
        if ($routeName === '' || !self::hasRoute($routeName)) {
            return '#';
        }

        return route($routeName);
    }

    /**
     * Check route exists.
     *
     * @param  string  $routeName
     * @return bool
     */
    protected static function hasRoute(string $routeName): bool
    {
        return $routeName !== '' && Route::has($routeName);
    }
}
