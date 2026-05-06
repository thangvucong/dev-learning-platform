<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UserSeeder extends Seeder
{
    /**
     * Seed users data.
     *
     * @return void
     */
    public function run()
    {
        Role::query()->firstOrCreate(['name' => 'admin']);
        Role::query()->firstOrCreate(['name' => 'student']);
        Role::query()->firstOrCreate(['name' => 'teacher']);

        User::factory()->count(3)->create();

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Nguyễn Tiến Quang Admin',
                'password' => Hash::make('password'), 
                'email_verified_at' => now(),
                'role' => 'admin', 
            ]
        );
      
        $admin->syncRoles(['admin']);

        $teachers = [
            [
                'name' => 'Nguyen Van Teacher 1',
                'email' => 'teacher1@example.com',
                'avatar_url' => 'https://i.pravatar.cc/150?img=11',
            ],
            [
                'name' => 'Tran Thi Teacher 2',
                'email' => 'teacher2@example.com',
                'avatar_url' => 'https://i.pravatar.cc/150?img=12',
            ],
            [
                'name' => 'Le Van Teacher 3',
                'email' => 'teacher3@example.com',
                'avatar_url' => 'https://i.pravatar.cc/150?img=13',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'avatar_url' => 'https://i.pravatar.cc/150?img=14',
            ],
        ];

        foreach ($teachers as $teacherData) {
            $attributes = [
                'name' => $teacherData['name'],
                'avatar_url' => $teacherData['avatar_url'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ];
            // Không ghi đè role admin của tài khoản admin khi merge danh sách teacher.
            if ($teacherData['email'] !== 'admin@example.com') {
                $attributes['role'] = 'teacher';
            }

            $teacher = User::query()->updateOrCreate(
                ['email' => $teacherData['email']],
                $attributes
            );

            $teacher->syncRoles(['teacher']);
        }
        
        $admins = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'avatar_url' => 'https://i.pravatar.cc/150?img=14',
            ],
        ];

        foreach ($admins as $adminData) {
            $admin = User::query()->updateOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'avatar_url' => $adminData['avatar_url'],
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => now(),
                    'role' => 'admin',
                ]
            );

            $admin->syncRoles(['admin']);
        }

        $students = [
            [
                'name' => 'Nguyen Van Student 1',
                'email' => 'student1@example.com',
            ],
            [
                'name' => 'Tran Thi Student 2',
                'email' => 'student2@example.com',
            ],
        ];

        foreach ($students as $studentData) {
            $student = User::query()->updateOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'role' => 'student',
                ]
            );

            $student->syncRoles(['student']);
        }
    }
}
