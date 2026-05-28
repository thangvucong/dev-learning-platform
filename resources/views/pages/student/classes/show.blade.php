@extends('components.admin.adminDashboard')

@section('title', $class['name'] . ' - Lớp học')

@section('content')
    <div class="space-y-6">
        <section class="rounded-3xl border border-slate-700 bg-gradient-to-r from-[#111827] via-[#0f172a] to-[#1e293b] overflow-hidden">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-0">
                <div class="xl:col-span-2 p-6 md:p-8">
                    <p class="text-xs uppercase tracking-wider text-slate-400">{{ $class['course_title'] }}</p>
                    <h1 class="mt-2 text-2xl md:text-3xl font-bold text-white">{{ $class['name'] }}</h1>
                    <p class="mt-3 text-sm text-slate-300">{{ $class['description'] }}</p>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-300">
                        <p><i class="fa-solid fa-user mr-2"></i>{{ $class['teacher'] }}</p>
                        <p><i class="fa-regular fa-clock mr-2"></i>{{ $class['next_session'] ?: 'Chưa có lịch' }}</p>
                        <p><i class="fa-solid fa-location-dot mr-2"></i>{{ $class['location'] }}</p>
                        <p><i class="fa-solid fa-circle-check mr-2"></i>Attendance {{ (int) $class['attendance_rate'] }}%</p>
                    </div>
                </div>
                <div class="xl:col-span-1 p-6 md:p-8 border-t xl:border-t-0 xl:border-l border-slate-700 bg-slate-900/40">
                    <p class="text-xs uppercase tracking-wider text-slate-400">Tiến độ</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ (int) $class['progress'] }}%</p>
                    <div class="mt-3 h-2 rounded-full bg-slate-700 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-400" style="width: {{ max(0, min(100, (int) $class['progress'])) }}%"></div>
                    </div>
                    <button type="button" class="mt-5 w-full h-11 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition-colors">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i>Vào học
                    </button>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827]">
            <div class="px-4 sm:px-6 pt-4">
                <div class="overflow-x-auto">
                    <div id="class-tabs-nav" class="inline-flex min-w-max rounded-xl bg-slate-900/60 border border-slate-700 p-1 gap-1">
                        @foreach ([
                            'overview' => 'Tổng quan',
                            'schedules' => 'Lịch học',
                            'materials' => 'Tài liệu',
                            'members' => 'Thành viên',
                            'attendance' => 'Điểm danh',
                        ] as $tabKey => $tabLabel)
                            <button type="button"
                                class="class-tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $loop->first ? 'bg-emerald-500 text-white' : 'text-slate-300 hover:bg-slate-700' }}"
                                data-tab-target="{{ $tabKey }}">
                                {{ $tabLabel }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <div class="class-tab-panel" data-tab-panel="overview">
                    @include('pages.student.classes.tabs.overview', ['class' => $class, 'overview' => $overview])
                </div>
                <div class="class-tab-panel hidden" data-tab-panel="schedules">
                    @include('pages.student.classes.tabs.schedules', ['schedules' => $schedules])
                </div>
                <div class="class-tab-panel hidden" data-tab-panel="materials">
                    @include('pages.student.classes.tabs.materials', ['materials' => $materials])
                </div>
                <div class="class-tab-panel hidden" data-tab-panel="members">
                    @include('pages.student.classes.tabs.members', ['members' => $members])
                </div>
                <div class="class-tab-panel hidden" data-tab-panel="attendance">
                    @include('pages.student.classes.tabs.attendance', ['attendance' => $attendance])
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tabsNav = document.getElementById('class-tabs-nav');
            if (!tabsNav) {
                return;
            }

            var buttons = Array.prototype.slice.call(document.querySelectorAll('.class-tab-btn'));
            var panels = Array.prototype.slice.call(document.querySelectorAll('.class-tab-panel'));

            function activateTab(target) {
                buttons.forEach(function(btn) {
                    var isActive = btn.getAttribute('data-tab-target') === target;
                    btn.classList.toggle('bg-emerald-500', isActive);
                    btn.classList.toggle('text-white', isActive);
                    btn.classList.toggle('text-slate-300', !isActive);
                    btn.classList.toggle('hover:bg-slate-700', !isActive);
                });

                panels.forEach(function(panel) {
                    var isActive = panel.getAttribute('data-tab-panel') === target;
                    panel.classList.toggle('hidden', !isActive);
                });
            }

            buttons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    activateTab(btn.getAttribute('data-tab-target'));
                });
            });
        });
    </script>
@endpush

