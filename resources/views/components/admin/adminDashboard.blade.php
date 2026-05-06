<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#0f172a] text-white">

    <div class="flex min-h-screen">
        <!-- SIDEBAR FIXED -->
        <aside class="w-64 bg-[#1e293b] border-r border-slate-700 fixed h-full z-50">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-10">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg"></div>
                    <span class="text-xl font-bold tracking-tight text-white">Manager Admin</span>
                </div>

                <nav class="space-y-4">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-4">Hệ thống</p>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-500 font-medium' : 'text-slate-400 hover:bg-slate-800' }} rounded-lg transition-all">
                            Dashboard
                        </a>
                    </div>

                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-4">Quản lý</p>
                        <div class="space-y-1">
                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center gap-3 px-3 py-2 {{ request()->routeIs('admin.users.*') ? 'bg-emerald-500/10 text-emerald-500 font-medium' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} rounded-lg transition-all">
                                Quản lý người dùng
                            </a>
                            <a href="{{ route('admin.courses.managerCourses') }}"
                                class="flex items-center gap-3 px-3 py-2 {{ request()->routeIs('admin.courses.*') ? 'bg-emerald-500/10 text-emerald-500 font-medium' : 'text-slate-400 hover:bg-slate-800' }} rounded-lg transition-all">
                                Quản lý khóa học
                            </a>
                            <a href="{{ route('admin.classes.managerClasses') }}"
                                class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                                Quản lý lớp học
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- User Info Sidebar - Cố định ở đáy -->
            <div class="absolute bottom-0 left-0 right-0 p-4 bg-[#1e293b] border-t border-slate-700">
                <div class="bg-slate-800/50 rounded-xl p-3 flex items-center gap-3 border border-slate-700">
                    <div
                        class="w-9 h-9 rounded-full bg-emerald-500 flex items-center justify-center font-bold text-white">
                        Q</div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold truncate text-white">Nguyễn Tiến Quang</p>
                        <p class="text-[10px] text-slate-400 uppercase font-medium">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 ml-64 p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')

    @flasher_render
</body>

</html>
