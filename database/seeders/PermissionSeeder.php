<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * @var array<int, string>
     */
    public const PERMISSIONS = [
        'access admin dashboard',
        'manage users',
        'manage courses',
        'manage classes',
        'manage posts',
        'upload editor images',
        'access instructor dashboard',
        'view instructor classes',
        'view instructor schedule',
        'access student dashboard',
        'view own courses',
        'view own classes',
        'view own schedule',
        'manage own profile',
        'create posts',
        'manage own posts',
        'checkout courses',
        'view own orders',
    ];

    /**
     * @return void
     */
    public function run()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
