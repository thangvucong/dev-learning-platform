<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Seed users data.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->count(3)
            ->create()
            ->each(function (User $user) {
                $user->assignRole(User::ROLE_STUDENT);
            });

        User::factory()
        ->count(3)
        ->create()
        ->each(function (User $user) {
            $user->assignRole(User::ROLE_INSTRUCTOR);
        });

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Thắng Công',
                'password' => Hash::make('12345678'), 
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now(),
                'remember_token' => Str::random(10)
            ]
        );
      
        $admin->syncRoles([User::ROLE_ADMIN]);
    }
}
