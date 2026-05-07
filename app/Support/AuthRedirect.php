<?php

namespace App\Support;

use App\Models\User;

class AuthRedirect
{
    /**
     * Resolve redirect path after authentication based on user role.
     *
     * @param  \App\Models\User  $user
     * @return string
     */
    public static function to(User $user): string
    {
        if ($user->isAdmin()) {
            return '/admin/dashboard';
        }

        if ($user->isTeacher()) {
            return '/teacher/dashboard';
        }

        return '/';
    }
}

