<?php

namespace App\Policies;

use App\Models\CourseClass;
use App\Models\User;

class CourseClassPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage classes')
            || $user->can('view instructor classes')
            || $user->can('view own classes');
    }

    public function view(User $user, CourseClass $courseClass): bool
    {
        if ($user->can('manage classes')) {
            return true;
        }

        if ($user->can('view instructor classes') && (int) $courseClass->instructor_id === (int) $user->id) {
            return true;
        }

        return $user->can('view own classes')
            && $courseClass->students()->whereKey($user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->can('manage classes');
    }

    public function update(User $user, CourseClass $courseClass): bool
    {
        return $user->can('manage classes');
    }

    public function delete(User $user, CourseClass $courseClass): bool
    {
        return $user->can('manage classes');
    }
}
