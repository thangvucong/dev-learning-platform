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
            ->role(User::ROLE_STUDENT)
            ->select(['id'])
            ->get();

        if ($users->isEmpty()) {
            $users = User::query()
                ->select(['id'])
                ->orderBy('id')
                ->limit(3)
                ->get();
        }

        if ($users->isEmpty()) {
            throw new \RuntimeException(
                'OrderSeeder: không có user nào. Chạy UserSeeder trước khi seed orders.'
            );
        }

        foreach ($users as $user) {
            Order::factory()
                ->count(2)
                ->paid()
                ->state(function () use ($user) {
                    return [
                        'user_id' => $user->id,
                        'subtotal_amount' => 0,
                        'discount_amount' => 0,
                        'total_amount' => 0,
                    ];
                })
                ->create();
        }
    }
}
