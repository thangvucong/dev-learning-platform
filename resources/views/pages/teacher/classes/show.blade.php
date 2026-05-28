@extends('components.admin.adminDashboard')

@section('title', $class['name'] . ' - Lớp học')

@section('content')
    @php
        $statusMap = [
            'upcoming' => ['label' => 'Sắp diễn ra', 'class' => 'bg-blue-500/10 text-blue-300 border-blue-500/30'],
            'ongoing' => ['label' => 'Đang dạy', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30'],
            'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-slate-500/10 text-slate-300 border-slate-500/30'],
            'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-red-500/10 text-red-300 border-red-500/30'],
            'live' => ['label' => 'Đang diễn ra', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30'],
        ];
        $classStatus = $statusMap[$class['status']] ?? $statusMap['ongoing'];
    @endphp

    <div class="space-y-5">
        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-5">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-xs uppercase tracking-wider text-slate-400">{{ $class['course_name'] }}</p>
                        <span class="rounded-full border px-2 py-0.5 text-xs font-semibold {{ $classStatus['class'] }}">{{ $classStatus['label'] }}</span>
                    </div>
                    <h1 class="mt-2 text-2xl font-bold text-white">{{ $class['name'] }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ $class['description'] }}</p>
                    <div class="mt-4 grid grid-cols-1 gap-2 text-sm text-slate-300 md:grid-cols-2">
                        <p><i class="fa-solid fa-code mr-2"></i>{{ $class['code'] }}</p>
                        <p><i class="fa-solid fa-location-dot mr-2"></i>{{ $class['location'] }}</p>
                        <p><i class="fa-regular fa-clock mr-2"></i>{{ $class['start_at'] }} - {{ $class['end_at'] }}</p>
                        <p><i class="fa-solid fa-users mr-2"></i>{{ $class['students_count'] }}/{{ $class['capacity'] }} học viên</p>
                    </div>
                </div>
                <div class="flex w-full justify-start sm:w-auto xl:justify-end">
                    <a href="{{ route('teacher.classes.students.export', ['courseClass' => $class['id']]) }}"
                        class="h-11 w-11 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700 hover:text-white inline-flex items-center justify-center"
                        title="Xuất danh sách học viên CSV" aria-label="Xuất danh sách học viên CSV">
                        <i class="fa-solid fa-file-csv text-lg"></i>
                    </a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @include('components.student.stat-card', ['title' => 'Học viên', 'value' => $class['students_count'], 'suffix' => 'bạn', 'icon' => 'fa-solid fa-users', 'tone' => 'blue'])
            @include('components.student.stat-card', ['title' => 'Buổi học', 'value' => $class['sessions_count'], 'suffix' => 'buổi', 'icon' => 'fa-solid fa-calendar-days', 'tone' => 'emerald'])
            @include('components.student.stat-card', ['title' => 'Tiến độ', 'value' => $class['progress'], 'suffix' => '%', 'icon' => 'fa-solid fa-chart-line', 'tone' => 'violet'])
            @include('components.student.stat-card', ['title' => 'Điểm danh', 'value' => $class['attendance_rate'], 'suffix' => '%', 'icon' => 'fa-solid fa-clipboard-check', 'tone' => 'amber'])
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827]">
            <div class="px-4 pt-4 sm:px-6">
                <div id="teacher-class-tabs" class="inline-flex min-w-max rounded-xl bg-slate-900/60 border border-slate-700 p-1 gap-1">
                    @foreach ([
                        'overview' => 'Tổng quan',
                        'students' => 'Học viên',
                        'schedule' => 'Lịch học',
                        'attendance' => 'Điểm danh',
                    ] as $tabKey => $tabLabel)
                        <button type="button"
                            class="teacher-class-tab px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $loop->first ? 'bg-emerald-500 text-white' : 'text-slate-300 hover:bg-slate-700' }}"
                            data-tab-target="{{ $tabKey }}">
                            {{ $tabLabel }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <div class="teacher-class-panel" data-tab-panel="overview">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                            <p class="text-xs uppercase tracking-wider text-slate-400">Buổi tiếp theo</p>
                            <p class="mt-2 text-lg font-semibold text-white">{{ $class['next_session'] }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                            <p class="text-xs uppercase tracking-wider text-slate-400">Buổi đã hoàn thành</p>
                            <p class="mt-2 text-lg font-semibold text-white">{{ $overview['completed_sessions'] }}/{{ $class['sessions_count'] }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                            <p class="text-xs uppercase tracking-wider text-slate-400">Tổng điểm danh</p>
                            <p class="mt-2 text-lg font-semibold text-white">
                                {{ $overview['present_count'] }} có mặt · {{ $overview['late_count'] }} trễ · {{ $overview['absent_count'] }} vắng
                            </p>
                        </div>
                    </div>
                </div>

                <div class="teacher-class-panel hidden" data-tab-panel="students">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left">
                            <thead class="text-xs uppercase tracking-wider text-slate-500">
                                <tr class="border-b border-slate-700">
                                    <th class="pb-3">Học viên</th>
                                    <th class="pb-3">Trạng thái</th>
                                    <th class="pb-3">Ngày vào lớp</th>
                                    <th class="pb-3">Điểm danh</th>
                                    <th class="pb-3">Tóm tắt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($students as $student)
                                    <tr>
                                        <td class="py-4">
                                            <p class="text-sm font-semibold text-white">{{ $student['name'] }}</p>
                                            <p class="mt-1 text-xs text-slate-400">{{ $student['email'] }}</p>
                                        </td>
                                        <td class="py-4 text-sm text-slate-300">{{ $student['status'] }}</td>
                                        <td class="py-4 text-sm text-slate-300">{{ $student['assigned_at'] ?: 'Đang cập nhật' }}</td>
                                        <td class="py-4 text-sm text-slate-300">{{ $student['attendance_rate'] }}%</td>
                                        <td class="py-4 text-sm text-slate-300">
                                            {{ $student['present'] }} có mặt · {{ $student['late'] }} trễ · {{ $student['absent'] }} vắng
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400">Lớp chưa có học viên.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="teacher-class-panel hidden" data-tab-panel="schedule">
                    <div class="space-y-3">
                        @forelse ($sessions as $session)
                            @php
                                $status = $statusMap[$session['status']] ?? $statusMap['upcoming'];
                            @endphp
                            <div id="attendance-session-{{ $session['id'] }}" class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $session['title'] }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $session['start_at'] }} - {{ $session['end_at'] }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $session['meeting_info'] }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-full border px-2 py-1 text-xs font-semibold {{ $status['class'] }}">{{ $status['label'] }}</span>
                                        @if (!empty($session['join_url']))
                                            <a href="{{ $session['join_url'] }}" target="_blank" rel="noopener noreferrer"
                                                class="h-9 px-3 rounded-lg bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 inline-flex items-center">
                                                Vào lớp
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400">Lớp chưa có lịch học.</p>
                        @endforelse
                    </div>
                </div>

                <div class="teacher-class-panel hidden" data-tab-panel="attendance">
                    <div class="space-y-3">
                        @forelse ($attendance_sessions as $session)
                            @php
                                $status = $statusMap[$session['status']] ?? $statusMap['upcoming'];
                            @endphp
                            <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $session['title'] }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $session['start_at'] }}</p>
                                        <p class="mt-2 text-sm text-slate-300">
                                            {{ $session['recorded_count'] }}/{{ $session['student_count'] }} đã ghi nhận · {{ $session['rate'] }}%
                                        </p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <span class="rounded-full border px-2 py-1 {{ $status['class'] }}">{{ $status['label'] }}</span>
                                        <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2 py-1 text-emerald-300">{{ $session['present'] }} có mặt</span>
                                        <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-1 text-amber-300">{{ $session['late'] }} trễ</span>
                                        <span class="rounded-full border border-red-500/30 bg-red-500/10 px-2 py-1 text-red-300">{{ $session['absent'] }} vắng</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400">Chưa có buổi học để hiển thị điểm danh.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = Array.prototype.slice.call(document.querySelectorAll('.teacher-class-tab'));
            const panels = Array.prototype.slice.call(document.querySelectorAll('.teacher-class-panel'));

            function activateTab(target) {
                buttons.forEach(function (button) {
                    const active = button.getAttribute('data-tab-target') === target;
                    button.classList.toggle('bg-emerald-500', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('text-slate-300', !active);
                    button.classList.toggle('hover:bg-slate-700', !active);
                });

                panels.forEach(function (panel) {
                    panel.classList.toggle('hidden', panel.getAttribute('data-tab-panel') !== target);
                });
            }

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const target = button.getAttribute('data-tab-target');
                    history.replaceState(null, '', '#' + target);
                    activateTab(target);
                });
            });

            window.addEventListener('hashchange', function () {
                const target = window.location.hash ? window.location.hash.substring(1) : 'overview';

                if (buttons.some(function (button) { return button.getAttribute('data-tab-target') === target; })) {
                    activateTab(target);
                }
            });

            const initial = window.location.hash ? window.location.hash.substring(1) : 'overview';
            if (buttons.some(function (button) { return button.getAttribute('data-tab-target') === initial; })) {
                activateTab(initial);
            }
        });
    </script>
@endpush
