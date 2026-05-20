<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage courses') || $user->can('view own courses');
    }

    public function view(User $user, Course $course): bool
    {
        if ($user->can('manage courses')) {
            return true;
        }

        return $user->can('view own courses')
            && $course->enrollments()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->can('manage courses');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->can('manage courses');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->can('manage courses');
    }
}
