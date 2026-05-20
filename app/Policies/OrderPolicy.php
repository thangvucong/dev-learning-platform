<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view own orders');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->can('view own orders') && (int) $order->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('checkout courses');
    }
}
