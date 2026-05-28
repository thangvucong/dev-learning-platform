@extends('components.admin.adminDashboard')

@section('title', $course['title'] . ' - Khóa học')

@push('styles')
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
    <style>
        /* Toast UI Viewer dark theme tuning (student dashboard layout) */
        [data-markdown-viewer] .toastui-editor-contents {
            color: rgb(203 213 225);
        }

        [data-markdown-viewer] .toastui-editor-contents h1,
        [data-markdown-viewer] .toastui-editor-contents h2,
        [data-markdown-viewer] .toastui-editor-contents h3,
        [data-markdown-viewer] .toastui-editor-contents h4 {
            color: #fff;
        }

        [data-markdown-viewer] .toastui-editor-contents a {
            color: rgb(110 231 183);
        }

        [data-markdown-viewer] .toastui-editor-contents code {
            background: rgba(2, 6, 23, 0.55);
            border: 1px solid rgba(51, 65, 85, 0.7);
            border-radius: 6px;
            padding: 0.1rem 0.35rem;
        }

        [data-markdown-viewer] .toastui-editor-contents pre {
            background: rgba(2, 6, 23, 0.65);
            border: 1px solid rgba(51, 65, 85, 0.8);
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <section class="rounded-3xl border border-slate-700 bg-gradient-to-r from-[#111827] via-[#0f172a] to-[#1e293b] overflow-hidden">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-0">
                <div class="xl:col-span-2 p-6 md:p-8">
                    <p class="text-xs uppercase tracking-wider text-slate-400">Learning Journey</p>
                    <h1 class="mt-2 text-2xl md:text-3xl font-bold text-white">{{ $course['title'] }}</h1>
                    <div class="mt-4">
                        <x-markdown.viewer :value="(string) ($course['description'] ?? '')" theme="dark" />
                    </div>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-300">
                        <p><i class="fa-solid fa-user mr-2"></i>{{ $course['teacher'] }}</p>
                        <p><i class="fa-regular fa-clock mr-2"></i>Buổi tiếp theo: {{ $course['next_session'] }}</p>
                        <p><i class="fa-solid fa-list-check mr-2"></i>{{ $course['modules_completed'] }}/{{ $course['modules_total'] }} modules</p>
                        <p><i class="fa-solid fa-flag-checkered mr-2"></i>Dự kiến hoàn thành {{ $course['estimated_completion'] }}</p>
                    </div>
                </div>

                <div class="xl:col-span-1 p-6 md:p-8 border-t xl:border-t-0 xl:border-l border-slate-700 bg-slate-900/40">
                    <p class="text-xs uppercase tracking-wider text-slate-400">Tiến độ tổng</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ (int) $course['progress'] }}%</p>
                    <div class="mt-3 h-2 rounded-full bg-slate-700 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-cyan-400" style="width: {{ (int) $course['progress'] }}%"></div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <div class="rounded-lg border border-slate-700 bg-slate-900/50 p-2">
                            <p class="text-slate-400">Attendance</p>
                            <p class="text-white font-semibold">{{ (int) $course['attendance_rate'] }}%</p>
                        </div>
                        <div class="rounded-lg border border-slate-700 bg-slate-900/50 p-2">
                            <p class="text-slate-400">Sessions</p>
                            <p class="text-white font-semibold">{{ (int) $course['sessions_completed'] }}/{{ (int) $course['sessions_total'] }}</p>
                        </div>
                    </div>
                    <button type="button" class="mt-5 w-full h-11 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition-colors">
                        <i class="fa-solid fa-play mr-2"></i>Continue Learning
                    </button>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827]">
            <div class="px-4 sm:px-6 pt-4">
                <div class="overflow-x-auto">
                    <div id="course-tabs-nav" class="inline-flex min-w-max rounded-xl bg-slate-900/60 border border-slate-700 p-1 gap-1">
                        @foreach ([
                            'overview' => 'Tổng quan',
                            'roadmap' => 'Lộ trình học',
                            'classes' => 'Lớp học',
                            'materials' => 'Tài liệu',
                            'progress' => 'Tiến độ',
                        ] as $tabKey => $tabLabel)
                            <button type="button"
                                class="course-tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $loop->first ? 'bg-emerald-500 text-white' : 'text-slate-300 hover:bg-slate-700' }}"
                                data-tab-target="{{ $tabKey }}">
                                {{ $tabLabel }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <div class="course-tab-panel" data-tab-panel="overview">
                    @include('pages.student.courses.tabs.overview', ['course' => $course, 'overview' => $overview])
                </div>
                <div class="course-tab-panel hidden" data-tab-panel="roadmap">
                    @include('pages.student.courses.tabs.roadmap', ['roadmap' => $roadmap])
                </div>
                <div class="course-tab-panel hidden" data-tab-panel="classes">
                    @include('pages.student.courses.tabs.classes', ['classes' => $classes])
                </div>
                <div class="course-tab-panel hidden" data-tab-panel="materials">
                    @include('pages.student.courses.tabs.materials', ['materials' => $materials])
                </div>
                <div class="course-tab-panel hidden" data-tab-panel="progress">
                    @include('pages.student.courses.tabs.progress', ['course' => $course, 'progress' => $progress])
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
    <script>
        window.addEventListener('load', function () {
            var roots = Array.prototype.slice.call(document.querySelectorAll('[data-markdown-viewer]'));
            roots.forEach(function (root) {
                var id = root.getAttribute('data-viewer-id');
                var mountEl = id ? document.getElementById(id) : null;
                var textarea = root.querySelector('[data-viewer-textarea]');
                if (!mountEl || !textarea || !window.toastui || !window.toastui.Editor) return;

                window.toastui.Editor.factory({
                    el: mountEl,
                    viewer: true,
                    initialValue: textarea.value || '',
                    usageStatistics: false
                });
            });
        }, { once: true });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tabsNav = document.getElementById('course-tabs-nav');
            if (!tabsNav) {
                return;
            }

            var buttons = Array.prototype.slice.call(document.querySelectorAll('.course-tab-btn'));
            var panels = Array.prototype.slice.call(document.querySelectorAll('.course-tab-panel'));

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

