<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::factory()->count(3)->create();

        User::query()->updateOrCreate(
            ['email' => 'student1@example.com'],
            [
                'name' => 'Nguyen Van Student 1',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'student2@example.com'],
            [
                'name' => 'Tran Thi Student 2',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
