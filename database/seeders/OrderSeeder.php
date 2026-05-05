<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        Order::query()->delete();

        $users = User::query()
            ->whereIn('email', ['student1@example.com', 'student2@example.com'])
            ->get();

        foreach ($users as $user) {
            Order::factory()
                ->count(2)
                ->paid()
                ->withItems(random_int(1, 2))
                ->state(function () use ($user) {
                    return [
                        'user_id' => $user->id,
                        'total_amount' => 0,
                    ];
                })
                ->create();
        }
    }
}
