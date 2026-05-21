<?php

return [
    'default_role' => 'student',

    'roles' => [
        'admin' => [
            [
                'section' => 'Hệ thống',
                'items' => [
                    [
                        'title' => 'Dashboard',
                        'route' => 'admin.dashboard',
                        'icon' => 'fa-solid fa-gauge-high',
                        'active' => ['admin.dashboard'],
                    ],
                ],
            ],
            [
                'section' => 'Quản lý',
                'items' => [
                    [
                        'title' => 'Quản lý người dùng',
                        'route' => 'admin.users.index',
                        'icon' => 'fa-solid fa-users',
                        'active' => ['admin.users.*'],
                    ],
                    [
                        'title' => 'Quản lý khóa học',
                        'route' => 'admin.courses.managerCourses',
                        'icon' => 'fa-solid fa-book-open',
                        'active' => ['admin.courses.*'],
                    ],
                    [
                        'title' => 'Quản lý lớp học',
                        'route' => 'admin.classes.managerClasses',
                        'icon' => 'fa-solid fa-chalkboard-user',
                        'active' => ['admin.classes.*'],
                    ],
                    [
                        'title' => 'Quản lý bài viết',
                        'route' => 'admin.posts.index',
                        'icon' => 'fa-solid fa-newspaper',
                        'active' => ['admin.posts.*'],
                    ],
                ],
            ],
        ],

        'instructor' => [
            [
                'section' => 'Tổng quan',
                'items' => [
                    [
                        'title' => 'Dashboard',
                        'route' => 'teacher.dashboard',
                        'icon' => 'fa-solid fa-gauge-high',
                        'active' => ['teacher.dashboard'],
                    ],
                ],
            ],
            [
                'section' => 'Giảng dạy',
                'items' => [
                    [
                        'title' => 'Lịch giảng dạy',
                        'route' => 'teacher.schedule.index',
                        'icon' => 'fa-solid fa-calendar-days',
                        'active' => ['teacher.schedule.*'],
                    ],
                    [
                        'title' => 'Lớp học của tôi',
                        'route' => 'teacher.classes.index',
                        'icon' => 'fa-solid fa-chalkboard',
                        'active' => ['teacher.classes.*'],
                    ],
                    [
                        'title' => 'Quản lý buổi học',
                        'route' => 'teacher.sessions.index',
                        'icon' => 'fa-solid fa-person-chalkboard',
                        'active' => ['teacher.sessions.*'],
                    ],
                    [
                        'title' => 'Tài liệu',
                        'route' => 'teacher.materials.index',
                        'icon' => 'fa-solid fa-folder-open',
                        'active' => ['teacher.materials.*'],
                    ],
                ],
            ]
        ],

        'student' => [
            [
                'section' => 'Học tập',
                'items' => [
                    [
                        'title' => 'Dashboard',
                        'route' => 'user.dashboard',
                        'icon' => 'fa-solid fa-gauge-high',
                        'active' => ['user.dashboard'],
                    ],
                    [
                        'title' => 'Lịch học',
                        'route' => 'user.schedule.index',
                        'icon' => 'fa-solid fa-calendar-days',
                        'active' => ['user.schedule.*'],
                    ],
                    [
                        'title' => 'Lớp học của tôi',
                        'route' => 'user.classes.index',
                        'icon' => 'fa-solid fa-chalkboard',
                        'active' => ['user.classes.*'],
                    ],
                    [
                        'title' => 'Khóa học',
                        'route' => 'user.courses.index',
                        'icon' => 'fa-solid fa-book',
                        'active' => ['user.courses.*'],
                    ],
                    [
                        'title' => 'Hồ sơ cá nhân',
                        'route' => 'user.profile.index',
                        'icon' => 'fa-solid fa-id-card',
                        'active' => ['profile.*', 'user.profile.*'],
                    ],
                ],
            ],
        ],
    ],
];
