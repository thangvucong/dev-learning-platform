@extends('components.admin.adminDashboard')

@section('title', 'Lịch học')

@push('styles')
    <link rel="stylesheet" href="{{ mix('assets/css/student-schedule.css') }}">
@endpush

@section('content')
    <div class="space-y-6">
        <section class="rounded-3xl border border-slate-700 bg-gradient-to-r from-[#111827] via-[#0f172a] to-[#1e293b] p-6 md:p-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-400">Learning Workspace</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">{{ $header['title'] }}</h1>
                    <p class="text-sm text-slate-300 mt-2">Theo dõi lịch học theo tuần và vào lớp nhanh khi đến giờ.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-center">
                    @include('components.student.stat-card', ['title' => 'Tổng buổi', 'value' => $sessions_total, 'suffix' => 'buổi', 'icon' => 'fa-regular fa-calendar', 'tone' => 'blue'])
                    @include('components.student.stat-card', ['title' => 'Upcoming', 'value' => $upcoming_count, 'suffix' => '', 'icon' => 'fa-regular fa-clock', 'tone' => 'violet'])
                    @include('components.student.stat-card', ['title' => 'Live', 'value' => $live_count, 'suffix' => '', 'icon' => 'fa-solid fa-broadcast-tower', 'tone' => 'emerald'])
                    @include('components.student.stat-card', ['title' => 'Missed', 'value' => $missed_count, 'suffix' => '', 'icon' => 'fa-solid fa-triangle-exclamation', 'tone' => 'amber'])
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-4">
            <form method="GET" action="{{ route('user.schedule.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="week_offset" value="{{ $header['week_offset'] }}">
                <div>
                    <label class="text-xs uppercase tracking-wider text-slate-400 mb-2 block">View mode</label>
                    <select name="view"
                        class="w-full h-10 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-3 focus:outline-none focus:border-emerald-500">
                        <option value="day" @selected(($filters['view'] ?? 'week') === 'day')>Day</option>
                        <option value="week" @selected(($filters['view'] ?? 'week') === 'week')>Week</option>
                        <option value="month" @selected(($filters['view'] ?? 'week') === 'month')>Month</option>
                        <option value="list" @selected(($filters['view'] ?? 'week') === 'list')>List</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wider text-slate-400 mb-2 block">Lọc theo lớp</label>
                    <select name="class_id"
                        class="w-full h-10 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-3 focus:outline-none focus:border-emerald-500">
                        <option value="0">Tất cả lớp</option>
                        @foreach ($classes as $classItem)
                            <option value="{{ $classItem['id'] }}" @selected((int) ($filters['class_id'] ?? 0) === (int) $classItem['id'])>{{ $classItem['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit"
                        class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
                        Áp dụng
                    </button>
                    <a href="{{ route('user.schedule.index') }}"
                        class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700 transition-colors inline-flex items-center">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2 space-y-3">
                @include('components.student.schedule.calendar-panel', [
                    'header' => $header,
                    'sessions' => $sessions,
                ])
            </div>

            <div class="xl:col-span-1">
                @include('components.student.schedule.session-detail', ['session' => $selected_session])
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ mix('assets/js/student-schedule.js') }}"></script>
@endpush

