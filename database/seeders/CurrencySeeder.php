<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        Currency::query()->delete();

        Currency::factory()
            ->count(3)
            ->sequence(
                [
                    'code' => 'VND',
                    'name' => 'Vietnamese Dong',
                    'symbol' => 'd',
                    'decimal_places' => 0,
                    'is_active' => true,
                ],
                [
                    'code' => 'USD',
                    'name' => 'US Dollar',
                    'symbol' => '$',
                    'decimal_places' => 2,
                    'is_active' => true,
                ],
                [
                    'code' => 'EUR',
                    'name' => 'Euro',
                    'symbol' => 'EUR',
                    'decimal_places' => 2,
                    'is_active' => false,
                ]
            )
            ->create();
    }
}
