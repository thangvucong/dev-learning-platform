@extends('components.admin.adminDashboard')

@section('title', 'Chi tiết người dùng')

@section('content')
    @php
        $roleName = $user->getRoleNames()->first() ?: 'student';
    @endphp
    <div class="max-w-4xl">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-emerald-400 hover:text-emerald-300">
                ← Quay lại danh sách người dùng
            </a>
        </div>

        <div class="bg-[#1e293b] border border-slate-700 rounded-2xl p-6">
            <div class="flex items-center gap-4 mb-6">
                @if (!empty($user->avatar_url))
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                        class="h-16 w-16 rounded-full object-cover border border-slate-600">
                @else
                    <div
                        class="h-16 w-16 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper(substr((string) $user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $user->name }}</h1>
                    <p class="text-slate-400 text-sm">{{ $user->email }}</p>
                </div>
            </div>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 p-4">
                    <dt class="text-slate-400 mb-1">ID người dùng</dt>
                    <dd class="text-slate-100 font-semibold">#{{ $user->id }}</dd>
                </div>
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 p-4">
                    <dt class="text-slate-400 mb-1">Vai trò</dt>
                    <dd class="text-slate-100 font-semibold">{{ $roleName }}</dd>
                </div>
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 p-4">
                    <dt class="text-slate-400 mb-1">Trạng thái</dt>
                    <dd class="text-slate-100 font-semibold">{{ $user->is_active ? 'Hoạt động' : 'Đang khóa' }}</dd>
                </div>
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 p-4">
                    <dt class="text-slate-400 mb-1">Số điện thoại</dt>
                    <dd class="text-slate-100 font-semibold">{{ $user->phone ?: 'Chưa cập nhật' }}</dd>
                </div>
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 p-4">
                    <dt class="text-slate-400 mb-1">Tổng khóa học đã mua</dt>
                    <dd class="text-slate-100 font-semibold">{{ (int) $user->purchased_courses_count }}</dd>
                </div>
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 p-4">
                    <dt class="text-slate-400 mb-1">Tổng lớp đã tham gia</dt>
                    <dd class="text-slate-100 font-semibold">{{ (int) $user->joined_classes_count }}</dd>
                </div>
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 p-4">
                    <dt class="text-slate-400 mb-1">Lần đăng nhập cuối</dt>
                    <dd class="text-slate-100 font-semibold">
                        {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Chưa ghi nhận' }}
                    </dd>
                </div>
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 p-4">
                    <dt class="text-slate-400 mb-1">Ngày tạo</dt>
                    <dd class="text-slate-100 font-semibold">{{ optional($user->created_at)->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
