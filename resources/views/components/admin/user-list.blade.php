@extends('components.admin.adminDashboard')

@section('title', 'Quản lý người dùng')

@section('content')
    <header class="flex flex-wrap justify-between items-center gap-3 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Quản lý người dùng</h1>
            <p class="text-sm text-slate-400">Theo dõi tài khoản, trạng thái và hoạt động học tập của người dùng.</p>
        </div>
    </header>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-[#1e293b] border border-slate-700 rounded-2xl p-5">
            <p class="text-xs uppercase tracking-widest text-slate-400 mb-2">Tổng người dùng</p>
            <p class="text-3xl font-bold text-white">{{ number_format((int) ($stats['total_users'] ?? 0)) }}</p>
        </div>
        <div class="bg-[#1e293b] border border-slate-700 rounded-2xl p-5">
            <p class="text-xs uppercase tracking-widest text-slate-400 mb-2">Đang hoạt động</p>
            <p class="text-3xl font-bold text-emerald-400">{{ number_format((int) ($stats['active_users'] ?? 0)) }}</p>
        </div>
        <div class="bg-[#1e293b] border border-slate-700 rounded-2xl p-5">
            <p class="text-xs uppercase tracking-widest text-slate-400 mb-2">Quản trị viên</p>
            <p class="text-3xl font-bold text-indigo-400">{{ number_format((int) ($stats['admins'] ?? 0)) }}</p>
        </div>
        <div class="bg-[#1e293b] border border-slate-700 rounded-2xl p-5">
            <p class="text-xs uppercase tracking-widest text-slate-400 mb-2">Mới tháng này</p>
            <p class="text-3xl font-bold text-amber-400">{{ number_format((int) ($stats['new_users_this_month'] ?? 0)) }}
            </p>
        </div>
    </section>

    @include('components.admin.partials.user-filters', [
        'filters' => $filters,
        'perPage' => $perPage,
    ])

    <div class="bg-[#1e293b] rounded-2xl border border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1300px]">
                <thead class="bg-slate-800/60 text-slate-400 text-[10px] uppercase tracking-widest">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Avatar</th>
                        <th class="px-5 py-4 font-semibold">ID</th>
                        <th class="px-5 py-4 font-semibold">Họ tên</th>
                        <th class="px-5 py-4 font-semibold">Email</th>
                        <th class="px-5 py-4 font-semibold">Vai trò</th>
                        <th class="px-5 py-4 font-semibold">Trạng thái</th>
                        <th class="px-5 py-4 font-semibold text-center">Khóa học đã mua</th>
                        <th class="px-5 py-4 font-semibold text-center">Lớp đã tham gia</th>
                        <th class="px-5 py-4 font-semibold">Lần đăng nhập cuối</th>
                        <th class="px-5 py-4 font-semibold">Ngày tạo</th>
                        <th class="px-5 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                @if (!empty($user->avatar_url))
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                        class="h-10 w-10 rounded-full object-cover border border-slate-600">
                                @else
                                    <div
                                        class="h-10 w-10 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr((string) $user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-500 font-mono text-sm">#{{ $user->id }}</td>
                            <td class="px-5 py-4">
                                <p class="text-slate-100 font-semibold text-sm">{{ $user->name }}</p>
                                <p class="text-slate-500 text-xs">{{ $user->phone ?: 'Chưa có SĐT' }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-200 text-sm">{{ $user->email }}</td>
                            <td class="px-5 py-4">
                                <span
                                    class="px-2 py-1 rounded-md text-[10px] uppercase font-bold border
                                    {{ $user->role === 'admin'
                                        ? 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20'
                                        : ($user->role === 'teacher'
                                            ? 'bg-sky-500/10 text-sky-300 border-sky-500/20'
                                            : 'bg-slate-700/80 text-slate-300 border-slate-600') }}">
                                    {{ $user->role ?: 'student' }}
                                </span>
                            </td>
                            <td class="py-4">
                                <span
                                    class="px-2 py-1 rounded-md text-[10px] uppercase font-bold border
                                    {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20' : 'bg-red-500/10 text-red-300 border-red-500/20' }}">
                                    {{ $user->is_active ? 'Hoạt động' : 'Khóa' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center text-slate-100 font-semibold">
                                {{ (int) $user->purchased_courses_count }}
                            </td>
                            <td class="px-5 py-4 text-center text-slate-100 font-semibold">
                                {{ (int) $user->joined_classes_count }}
                            </td>
                            <td class="px-5 py-4 text-slate-400 text-xs">
                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Chưa ghi nhận' }}
                            </td>
                            <td class="px-5 py-4 text-slate-400 text-xs">{{ optional($user->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                        class="p-2 rounded-md border border-slate-600 text-slate-200 hover:bg-slate-700 transition-colors"
                                        title="Xem chi tiết" aria-label="Xem chi tiết">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="p-2 rounded-md border border-blue-500/30 text-blue-300 hover:bg-blue-500/10 transition-colors"
                                        title="Chỉnh sửa" aria-label="Chỉnh sửa">
                                        <i class="fas fa-pen-to-square text-xs"></i>
                                    </a>

                                    <form method="POST" action="{{ route('admin.users.toggleStatus', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="p-2 rounded-md border {{ $user->is_active ? 'border-amber-500/30 text-amber-300 hover:bg-amber-500/10' : 'border-emerald-500/30 text-emerald-300 hover:bg-emerald-500/10' }}"
                                            title="{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}"
                                            aria-label="{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                            <i class="fas {{ $user->is_active ? 'fa-lock' : 'fa-unlock' }} text-xs"></i>
                                            <span class="sr-only">{{ $user->is_active ? 'Khóa' : 'Mở khóa' }}</span>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                        onsubmit="return confirm('Bạn chắc chắn muốn xóa người dùng này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 rounded-md border border-red-500/30 text-red-300 hover:bg-red-500/10 transition-colors"
                                            title="Xóa user" aria-label="Xóa user">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-10 text-center text-slate-500">
                                Không tìm thấy người dùng phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div
            class="border-t border-slate-700 px-5 py-4 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
            <p>
                Hiển thị {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }}
                / {{ number_format((int) $users->total()) }} người dùng
            </p>
            <div>
                {{ $users->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endsection
