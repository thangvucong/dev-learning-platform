@extends('components.admin.adminDashboard')

@section('title', 'Khóa học')

@section('content')
    <div class="space-y-6">
        <section
            class="rounded-3xl border border-slate-700 bg-gradient-to-r from-[#111827] via-[#0f172a] to-[#1e293b] p-4 md:p-4">
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Khóa học của tôi</h1>
            <p class="text-sm text-slate-300 mt-2">Theo dõi tiến độ học và quay lại khóa học đang học nhanh hơn.</p>
        </section>

        @if (!empty($continue_course))
            <section
                class="rounded-2xl border border-emerald-500/30 bg-gradient-to-r from-emerald-500/10 via-cyan-500/10 to-slate-900 p-5">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-emerald-300">Tiếp tục học</p>
                        <h2 class="text-xl font-bold text-white mt-1">{{ $continue_course['continue_label'] }}</h2>
                        <p class="text-sm text-slate-300 mt-2">Giảng viên {{ $continue_course['teacher'] }}</p>
                    </div>
                    <div class="w-full lg:w-80">
                        <div class="flex items-center justify-between text-xs text-slate-300 mb-1">
                            <span>Tiến độ</span>
                            <span>{{ (int) $continue_course['progress'] }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-700 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-emerald-400 to-cyan-400"
                                style="width: {{ (int) $continue_course['progress'] }}%"></div>
                        </div>
                        <a href="{{ route('user.courses.show', ['id' => $continue_course['id']]) }}"
                            class="mt-4 h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors inline-flex items-center">
                            Tiếp tục học
                        </a>
                    </div>
                </div>
            </section>
        @endif

        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-4">
            <p class="text-sm font-semibold text-white mb-3">Bộ lọc khóa học</p>
            <form method="GET" action="{{ route('user.courses.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-4">
                    <label class="text-sm text-slate-300 mb-2 block">Tìm kiếm khóa học</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                        placeholder="Tên khóa học, mentor..."
                        class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-slate-300 mb-2 block">Trạng thái</label>
                    <select name="status"
                        class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="ongoing" @selected(($filters['status'] ?? '') === 'ongoing')>Đang học</option>
                        <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Hoàn thành</option>
                        <option value="not_started" @selected(($filters['status'] ?? '') === 'not_started')>Chưa bắt đầu</option>
                    </select>
                </div>
                <div class="md:col-span-6 flex items-center gap-2">
                    <button type="submit"
                        class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
                        Áp dụng lọc
                    </button>
                    <a href="{{ route('user.courses.index') }}"
                        class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700 transition-colors inline-flex items-center">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </section>

        <section>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($courses as $courseItem)
                    @include('components.student.courses.course-card', [
                        'id' => $courseItem['id'],
                        'title' => $courseItem['title'],
                        'thumbnail' => $courseItem['thumbnail'],
                        'teacher' => $courseItem['teacher'],
                        'progress' => $courseItem['progress'],
                        'attendanceRate' => $courseItem['attendance_rate'],
                        'completedSessions' => $courseItem['completed_sessions'],
                        'totalSessions' => $courseItem['total_sessions'],
                        'status' => $courseItem['status'],
                        'nextSession' => $courseItem['next_session'],
                    ])
                @empty
                    <div
                        class="md:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-slate-600 bg-[#111827] p-10 text-center text-slate-400">
                        <i class="fa-regular fa-folder-open text-3xl mb-3"></i>
                        <p>Không có khóa học phù hợp với bộ lọc hiện tại.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
