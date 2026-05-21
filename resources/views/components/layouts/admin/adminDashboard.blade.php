@php
    use App\Support\SidebarMenuBuilder;

    $authUser = auth()->user();
    $sidebarSections = SidebarMenuBuilder::forUser($authUser);
    $displayName = trim((string) data_get($authUser, 'name', 'User'));
    $role = (string) optional($authUser)->getRoleNames()->first() ?: 'student';
    $displayRole = [
        'admin' => 'QUẢN TRỊ',
        'instructor' => 'GIẢNG VIÊN',
        'student' => 'HỌC VIÊN',
    ][$role] ?? strtoupper($role);
    $brandLabel = [
        'admin' => 'LMS Admin',
        'instructor' => 'LMS Teacher',
        'student' => 'LMS Student',
    ][$role] ?? 'LMS';
    $initial = strtoupper(substr($displayName, 0, 1));
@endphp
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @stack('styles')
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#0f172a] text-white">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-[#1e293b] border-r border-slate-700 fixed h-full z-50">
            <div class="p-4 h-full flex flex-col">
                <div class="flex items-center gap-2 mb-8">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg"></div>
                    <span class="text-xl font-bold tracking-tight text-white">{{ $brandLabel }}</span>
                </div>

                <nav class="space-y-5 flex-1 overflow-y-auto pr-1">
                    @foreach ($sidebarSections as $section)
                        <div>
                            @if (!empty($section['section']))
                                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-3">
                                    {{ $section['section'] }}
                                </p>
                            @endif
                            <div class="space-y-1">
                                @foreach ($section['items'] as $item)
                                    @include('components.layouts.admin.partials.sidebar-item', [
                                        'item' => $item,
                                    ])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
<div class="p-3 border-t border-slate-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-400 hover:bg-red-500/10 transition-colors">
                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </div>
                <div class="mt-4 pt-4 border-t border-slate-700">
                    <div class="bg-slate-800/50 rounded-xl p-3 flex items-center gap-3 border border-slate-700">
                        <div
                            class="w-9 h-9 rounded-full bg-emerald-500 flex items-center justify-center font-bold text-white">
                            {{ $initial !== '' ? $initial : 'U' }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold truncate text-white">{{ $displayName }}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-medium">{{ $displayRole }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex-1 ml-64 p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    @flasher_render
</body>

</html>
