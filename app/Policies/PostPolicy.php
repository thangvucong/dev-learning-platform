<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage posts') || $user->can('manage own posts');
    }

    public function view(User $user, Post $post): bool
    {
        return $user->can('manage posts') || (int) $post->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create posts');
    }

    public function update(User $user, Post $post): bool
    {
        return $user->can('manage posts')
            || ($user->can('manage own posts') && (int) $post->user_id === (int) $user->id);
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('manage posts')
            || ($user->can('manage own posts') && (int) $post->user_id === (int) $user->id);
    }

    public function moderate(User $user): bool
    {
        return $user->can('manage posts');
    }
}
