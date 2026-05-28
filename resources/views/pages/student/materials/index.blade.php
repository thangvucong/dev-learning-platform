@extends('components.admin.adminDashboard')

@section('title', 'Tài liệu')

@section('content')
    @php
        $selectedClassId = (int) ($filters['class_id'] ?? 0);
        $selectedSessionId = (int) ($filters['class_session_id'] ?? 0);
        $sessionUrlTemplate = route('user.materials.class-sessions', ['courseClass' => '__CLASS_ID__']);
    @endphp

    <div class="space-y-5">
        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-5">
            <h1 class="text-2xl font-bold text-white">Tài liệu</h1>
            <p class="mt-1 text-sm text-slate-400">Xem và tải tài liệu từ các lớp học bạn đang tham gia.</p>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-4">
            <form method="GET" action="{{ route('user.materials.index') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-6">
                <div class="lg:col-span-3">
                    <label class="mb-2 block text-sm text-slate-300">Lọc theo lớp</label>
                    <select name="class_id" id="student-material-filter-class" data-session-url-template="{{ $sessionUrlTemplate }}"
                        class="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/60 px-4 text-slate-100 focus:border-emerald-500 focus:outline-none">
                        <option value="">Tất cả lớp</option>
                        @foreach ($classes as $classItem)
                            <option value="{{ $classItem['id'] }}" @selected($selectedClassId === (int) $classItem['id'])>
                                {{ $classItem['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label class="mb-2 block text-sm text-slate-300">Lọc theo buổi học</label>
                    <select name="class_session_id" id="student-material-filter-session"
                        class="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/60 px-4 text-slate-100 focus:border-emerald-500 focus:outline-none">
                        <option value="">Tất cả tài liệu</option>
                        @foreach ($sessions as $session)
                            <option value="{{ $session['id'] }}" @selected($selectedSessionId === (int) $session['id'])>
                                {{ $session['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-6 flex items-center gap-2">
                    <button type="submit"
                        class="h-10 rounded-xl bg-emerald-500 px-4 text-sm font-semibold text-white hover:bg-emerald-600">
                        Áp dụng
                    </button>
                    <a href="{{ route('user.materials.index') }}"
                        class="inline-flex h-10 items-center rounded-xl border border-slate-600 px-4 text-sm font-semibold text-slate-300 hover:bg-slate-700">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </section>

        <section class="grid grid-cols-1 gap-3 xl:grid-cols-2">
            @forelse ($materials as $material)
                <article class="rounded-2xl border border-slate-700 bg-[#111827] p-4">
                    <div class="flex gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-700 bg-slate-900/70 text-lg text-emerald-300">
                            <i class="{{ $material['icon'] }}"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <p class="truncate text-base font-semibold text-white">{{ $material['title'] }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-400">{{ $material['original_name'] }} · {{ $material['size_label'] }}</p>
                                </div>
                                <a href="{{ $material['download_url'] }}"
                                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700 hover:text-white"
                                    title="Tải xuống">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-300">
                                <span class="rounded-full border border-slate-600 px-2 py-1">{{ $material['class_name'] }}</span>
                                <span class="rounded-full border border-slate-600 px-2 py-1">{{ $material['session_label'] }}</span>
                                <span class="rounded-full border border-slate-600 px-2 py-1">{{ $material['published_at'] }}</span>
                            </div>
                            @if (!empty($material['description']))
                                <p class="mt-3 text-sm leading-6 text-slate-300">{{ $material['description'] }}</p>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="xl:col-span-2 rounded-2xl border border-dashed border-slate-600 bg-[#111827] p-10 text-center text-slate-400">
                    <i class="fa-regular fa-folder-open mb-3 text-3xl"></i>
                    <p>Chưa có tài liệu phù hợp.</p>
                </div>
            @endforelse
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const classSelect = document.getElementById('student-material-filter-class');
            const sessionSelect = document.getElementById('student-material-filter-session');
            if (!classSelect || !sessionSelect) {
                return;
            }

            classSelect.addEventListener('change', function () {
                sessionSelect.innerHTML = '<option value="">Tất cả tài liệu</option>';
                if (!classSelect.value) {
                    return;
                }

                const template = classSelect.getAttribute('data-session-url-template');
                fetch(template.replace('__CLASS_ID__', classSelect.value), {
                    headers: { 'Accept': 'application/json' },
                })
                    .then(function (response) { return response.ok ? response.json() : { sessions: [] }; })
                    .then(function (payload) {
                        (payload.sessions || []).forEach(function (session) {
                            const option = document.createElement('option');
                            option.value = session.id;
                            option.textContent = session.label;
                            sessionSelect.appendChild(option);
                        });
                    });
            });
        });
    </script>
@endpush
