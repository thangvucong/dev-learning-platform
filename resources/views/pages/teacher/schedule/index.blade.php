@extends('components.admin.adminDashboard')

@section('title', 'Lịch giảng dạy')

@push('styles')
    @php
        $scheduleCssPath = public_path('assets/css/student-schedule.css');
        $scheduleCssVersion = file_exists($scheduleCssPath) ? filemtime($scheduleCssPath) : time();
    @endphp
    <link rel="stylesheet" href="{{ mix('assets/css/student-schedule.css') }}?v={{ $scheduleCssVersion }}">
@endpush

@section('content')
    <div class="space-y-5">
        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-4">
            <form id="schedule-filter-form" method="GET" action="{{ route('teacher.schedule.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="week_offset" value="{{ $header['week_offset'] }}">
                <div>
                    <label class="text-sm text-slate-300 mb-2 block">Chế độ xem</label>
                    <select name="view"
                        class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                        <option value="day" @selected(($filters['view'] ?? 'week') === 'day')>Ngày</option>
                        <option value="week" @selected(($filters['view'] ?? 'week') === 'week')>Tuần</option>
                        <option value="month" @selected(($filters['view'] ?? 'week') === 'month')>Tháng</option>
                        <option value="list" @selected(($filters['view'] ?? 'week') === 'list')>Danh sách</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-slate-300 mb-2 block">Lọc theo lớp</label>
                    <select name="class_id"
                        class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                        <option value="0">Tất cả lớp</option>
                        @foreach ($classes as $classItem)
                            <option value="{{ $classItem['id'] }}" @selected((int) ($filters['class_id'] ?? 0) === (int) $classItem['id'])>
                                {{ $classItem['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    <button id="schedule-filter-submit" type="submit"
                        class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
                        Áp dụng
                    </button>
                    <a href="{{ route('teacher.schedule.index') }}"
                        class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700 transition-colors inline-flex items-center">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2">
                @include('components.student.schedule.calendar-panel', [
                    'header' => $header,
                    'sessions' => $sessions,
                ])
            </div>

            <div class="xl:col-span-1">
                @include('components.teacher.schedule.session-detail', ['session' => $selected_session])
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    @php
        $scheduleJsPath = public_path('assets/js/student-schedule.js');
        $scheduleJsVersion = file_exists($scheduleJsPath) ? filemtime($scheduleJsPath) : time();
    @endphp
    <script>
        window.StudentScheduleConfig = {
            dataUrl: @json(route('teacher.schedule.data')),
            attendanceBaseUrl: @json(url('/teacher/schedule/sessions')),
            assignmentBaseUrl: @json(url('/teacher/schedule/sessions')),
            classSessionBaseUrl: @json(url('/teacher/schedule/classes')),
        };
    </script>
    <script src="{{ mix('assets/js/student-schedule.js') }}?v={{ $scheduleJsVersion }}"></script>
@endpush
