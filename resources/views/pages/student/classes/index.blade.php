@extends('components.admin.adminDashboard')

@section('title', 'Lớp học của tôi')

@section('content')
    <div class="space-y-6">
        <section
            class="rounded-3xl border border-slate-700 bg-gradient-to-r from-[#111827] via-[#0f172a] to-[#1e293b] p-4 md:p-4">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Lớp học của tôi</h1>
                    <p class="text-sm text-slate-300 mt-2">Xem lớp hiện tại và vào lớp nhanh khi đến giờ.</p>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-4">
            <p class="text-sm font-semibold text-white mb-3">Bộ lọc lớp học</p>
            <form method="GET" action="{{ route('user.classes.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-4">
                    <label class="text-sm text-slate-300 mb-2 block">Tìm kiếm lớp học</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                        placeholder="Tên lớp, khóa học, mentor..."
                        class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-slate-300 mb-2 block">Trạng thái</label>
                    <select name="status"
                        class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="ongoing" @selected(($filters['status'] ?? '') === 'ongoing')>Đang học</option>
                        <option value="upcoming" @selected(($filters['status'] ?? '') === 'upcoming')>Sắp bắt đầu</option>
                        <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Đã hoàn thành</option>
                    </select>
                </div>
                <div class="md:col-span-6 flex items-center gap-2">
                    <button type="submit"
                        class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
                        Áp dụng lọc
                    </button>
                    <a href="{{ route('user.classes.index') }}"
                        class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700 transition-colors inline-flex items-center">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </section>

        <section>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($classes as $classCard)
                    @include('components.student.class-card', [
                        'id' => $classCard['id'],
                        'name' => $classCard['name'],
                        'course' => $classCard['course_title'],
                        'teacher' => $classCard['teacher'],
                        'nextSession' => $classCard['next_session'],
                        'thumbnail' => $classCard['thumbnail'],
                        'progress' => $classCard['progress'],
                        'status' => $classCard['status'],
                        'attendanceRate' => $classCard['attendance_rate'],
                    ])
                @empty
                    <div
                        class="md:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-slate-600 bg-[#111827] p-10 text-center text-slate-400">
                        <i class="fa-regular fa-folder-open text-3xl mb-3"></i>
                        <p>Không có lớp học phù hợp với bộ lọc hiện tại.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
