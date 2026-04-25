<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition()
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2],
            ['code' => 'VND', 'name' => 'Vietnamese Dong', 'symbol' => 'd', 'decimal_places' => 0],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => 'EUR', 'decimal_places' => 2],
        ];

        $currency = $this->faker->unique()->randomElement($currencies);

        return [
            'code' => $currency['code'],
            'name' => $currency['name'],
            'symbol' => $currency['symbol'],
            'decimal_places' => $currency['decimal_places'],
            'is_active' => true,
        ];
    }

    public function inactive()
    {
        return $this->state(function () {
            return [
                'is_active' => false,
            ];
        });
    }
}
