@extends('components.admin.adminDashboard')

@section('title', 'Chỉnh sửa người dùng')

@section('content')
    <div class="max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-emerald-400 hover:text-emerald-300">
                ← Quay lại danh sách người dùng
            </a>
        </div>

        <div class="bg-[#1e293b] border border-slate-700 rounded-2xl p-6">
            <h1 class="text-xl font-bold text-white mb-5">Chỉnh sửa người dùng #{{ $user->id }}</h1>

            @if ($errors->any())
                <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-300 rounded-xl px-4 py-3 text-sm">
                    <ul class="list-disc ml-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Họ tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Vai trò</label>
                        <select name="role"
                            class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Trạng thái</label>
                        <select name="is_active"
                            class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                    </div>
                </div>

                <div class="pt-3 flex gap-3">
                    <button type="submit"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-xl text-sm font-semibold transition-colors">
                        Lưu thay đổi
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="bg-slate-800 hover:bg-slate-700 border border-slate-600 text-slate-200 px-5 py-2 rounded-xl text-sm font-semibold transition-colors">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
