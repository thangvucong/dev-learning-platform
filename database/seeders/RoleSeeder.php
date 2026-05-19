<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Role::query()->firstOrCreate(['name' => User::ROLE_ADMIN, 'guard_name' => 'web']);
        $instructor = Role::query()->firstOrCreate(['name' => User::ROLE_INSTRUCTOR, 'guard_name' => 'web']);
        $student = Role::query()->firstOrCreate(['name' => User::ROLE_STUDENT, 'guard_name' => 'web']);

        $admin->syncPermissions(PermissionSeeder::PERMISSIONS);
        $instructor->syncPermissions([ 
            'access instructor dashboard',
            'view instructor classes',
            'view instructor schedule',
            'create posts',
            'manage own posts',
            'upload editor images',
        ]);
        $student->syncPermissions([
            'access student dashboard',
            'view own courses',
            'view own classes',
            'view own schedule',
            'manage own profile',
            'create posts',
            'manage own posts',
            'checkout courses',
            'view own orders',
            'upload editor images',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
