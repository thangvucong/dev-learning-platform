<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        Order::query()->delete();

        $currency = Currency::query()->where('is_active', true)->first();
        $users = User::query()
            ->whereIn('email', ['student1@example.com', 'student2@example.com'])
            ->get();

        foreach ($users as $user) {
            Order::factory()
                ->count(2)
                ->paid()
                ->withItems(random_int(1, 2))
                ->state(function () use ($user, $currency) {
                    return [
                        'user_id' => $user->id,
                        'currency_id' => $currency->id,
                        'total_amount' => 0,
                    ];
                })
                ->create();
        }
    }
}
