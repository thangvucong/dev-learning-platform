<form method="GET" action="{{ route('admin.users.index') }}"
    class="bg-[#1e293b] border border-slate-700 rounded-2xl p-5 mb-6">
    <div class="grid grid-cols-1 lg:grid-cols-6 gap-4">
        <div class="lg:col-span-2">
            <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Tìm kiếm</label>
            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}"
                placeholder="Tên, email, số điện thoại..."
                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Vai trò</label>
            <select name="role"
                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
                <option value="" @selected((string) ($filters['role'] ?? '') === '')>Tất cả</option>
                <option value="admin" @selected((string) ($filters['role'] ?? '') === 'admin')>Admin</option>
                <option value="teacher" @selected((string) ($filters['role'] ?? '') === 'teacher')>Teacher</option>
                <option value="student" @selected((string) ($filters['role'] ?? '') === 'student')>Student</option>
            </select>
        </div>

        <div>
            <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Trạng thái</label>
            <select name="status"
                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
                <option value="">Tất cả</option>
                <option value="1" @selected(($filters['status'] ?? '') === '1')>Đang hoạt động</option>
                <option value="0" @selected(($filters['status'] ?? '') === '0')>Đang khóa</option>
            </select>
        </div>

        <div>
            <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Từ ngày</label>
            <input type="date" name="created_from" value="{{ $filters['created_from'] ?? '' }}"
                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Đến ngày</label>
            <input type="date" name="created_to" value="{{ $filters['created_to'] ?? '' }}"
                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
        </div>
    </div>

    <div class="mt-5 flex flex-wrap items-center gap-3">
        <button type="submit"
            class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
            Áp dụng bộ lọc
        </button>
        <a href="{{ route('admin.users.index') }}"
            class="bg-slate-800 hover:bg-slate-700 border border-slate-600 text-slate-200 px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
            Xóa lọc
        </a>

        <div class="ml-auto flex items-center gap-2">
            <label class="text-xs uppercase tracking-wider text-slate-400">Mỗi trang</label>
            <select name="per_page"
                class="bg-slate-900/60 border border-slate-700 text-slate-100 text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-emerald-500"
                onchange="this.form.submit()">
                @foreach ([10, 20, 50] as $size)
                    <option value="{{ $size }}" @selected((int) $perPage === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>
