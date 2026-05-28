@extends('components.admin.adminDashboard')

@section('title', 'Tài liệu')

@section('content')
    @php
        $selectedClassId = (int) ($filters['class_id'] ?? 0);
        $selectedSessionId = (int) ($filters['class_session_id'] ?? 0);
        $sessionUrlTemplate = route('teacher.materials.class-sessions', ['courseClass' => '__CLASS_ID__']);
    @endphp

    <div class="space-y-5">
        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Tài liệu</h1>
                    <p class="mt-1 text-sm text-slate-400">Upload và quản lý tài liệu theo lớp hoặc theo từng buổi học.</p>
                </div>
                <button type="button" id="material-upload-toggle"
                    class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-500 px-4 text-sm font-semibold text-white hover:bg-emerald-600">
                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i>Tải tài liệu
                </button>
            </div>
        </section>

        @if ($errors->any())
            <section class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">
                <p class="font-semibold">Không thể tải tài liệu.</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section id="material-upload-panel"
            class="{{ $errors->any() ? '' : 'hidden' }} rounded-2xl border border-slate-700 bg-[#111827] p-4">
            <form method="POST" action="{{ route('teacher.materials.store') }}" enctype="multipart/form-data"
                class="grid grid-cols-1 gap-4 lg:grid-cols-6">
                @csrf
                <div class="lg:col-span-3">
                    <label class="mb-2 block text-sm text-slate-300">Lớp học</label>
                    <select name="class_id" id="material-upload-class" data-session-url-template="{{ $sessionUrlTemplate }}"
                        class="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/60 px-4 text-slate-100 focus:border-emerald-500 focus:outline-none">
                        <option value="">Chọn lớp</option>
                        @foreach ($classes as $classItem)
                            <option value="{{ $classItem['id'] }}" @selected((int) old('class_id', $selectedClassId) === (int) $classItem['id'])>
                                {{ $classItem['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label class="mb-2 block text-sm text-slate-300">Buổi học</label>
                    <select name="class_session_id" id="material-upload-session"
                        data-selected="{{ old('class_session_id', $selectedSessionId) }}"
                        class="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/60 px-4 text-slate-100 focus:border-emerald-500 focus:outline-none">
                        <option value="">Tài liệu lớp</option>
                        @foreach ($sessions as $session)
                            <option value="{{ $session['id'] }}" @selected((int) old('class_session_id', $selectedSessionId) === (int) $session['id'])>
                                {{ $session['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label class="mb-2 block text-sm text-slate-300">Tiêu đề</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Để trống sẽ dùng tên file"
                        class="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/60 px-4 text-slate-100 focus:border-emerald-500 focus:outline-none">
                </div>
                <div class="lg:col-span-3">
                    <label class="mb-2 block text-sm text-slate-300">File</label>
                    <input type="file" name="file"
                        class="block h-11 w-full rounded-xl border border-slate-700 bg-slate-900/60 text-sm text-slate-300 file:mr-4 file:h-full file:border-0 file:bg-slate-700 file:px-4 file:text-slate-100 hover:file:bg-slate-600">
                </div>
                <div class="lg:col-span-6">
                    <label class="mb-2 block text-sm text-slate-300">Mô tả</label>
                    <textarea name="description" rows="3" placeholder="Ghi chú ngắn cho học viên"
                        class="w-full rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-emerald-500 focus:outline-none">{{ old('description') }}</textarea>
                </div>
                <div class="lg:col-span-6 flex items-center justify-end gap-2">
                    <button type="button" id="material-upload-cancel"
                        class="h-10 rounded-xl border border-slate-600 px-4 text-sm font-semibold text-slate-300 hover:bg-slate-700">
                        Đóng
                    </button>
                    <button type="submit"
                        class="h-10 rounded-xl bg-emerald-500 px-4 text-sm font-semibold text-white hover:bg-emerald-600">
                        Tải lên
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-4">
            <form method="GET" action="{{ route('teacher.materials.index') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-6">
                <div class="lg:col-span-3">
                    <label class="mb-2 block text-sm text-slate-300">Lọc theo lớp</label>
                    <select name="class_id" id="material-filter-class" data-session-url-template="{{ $sessionUrlTemplate }}"
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
                    <select name="class_session_id" id="material-filter-session"
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
                    <a href="{{ route('teacher.materials.index') }}"
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
                                <div class="flex shrink-0 items-center gap-2">
                                    <a href="{{ $material['download_url'] }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700 hover:text-white"
                                        title="Tải xuống">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <form method="POST" action="{{ $material['delete_url'] }}"
                                        onsubmit="return confirm('Ẩn tài liệu này khỏi học viên?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-500/40 text-red-300 hover:bg-red-500/10"
                                            title="Ẩn tài liệu">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
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
            const panel = document.getElementById('material-upload-panel');
            const toggle = document.getElementById('material-upload-toggle');
            const cancel = document.getElementById('material-upload-cancel');

            if (toggle && panel) {
                toggle.addEventListener('click', function () {
                    panel.classList.toggle('hidden');
                });
            }

            if (cancel && panel) {
                cancel.addEventListener('click', function () {
                    panel.classList.add('hidden');
                });
            }

            function bindSessions(classSelectId, sessionSelectId, emptyLabel) {
                const classSelect = document.getElementById(classSelectId);
                const sessionSelect = document.getElementById(sessionSelectId);
                if (!classSelect || !sessionSelect) {
                    return;
                }

                classSelect.addEventListener('change', function () {
                    sessionSelect.innerHTML = '<option value="">' + emptyLabel + '</option>';
                    if (!classSelect.value) {
                        return;
                    }

                    const template = classSelect.getAttribute('data-session-url-template');
                    fetch(template.replace('__CLASS_ID__', classSelect.value), {
                        headers: { 'Accept': 'application/json' },
                    })
                        .then(function (response) { return response.ok ? response.json() : { sessions: [] }; })
                        .then(function (payload) {
                            const selected = sessionSelect.getAttribute('data-selected') || '';
                            (payload.sessions || []).forEach(function (session) {
                                const option = document.createElement('option');
                                option.value = session.id;
                                option.textContent = session.label;
                                option.selected = String(session.id) === String(selected);
                                sessionSelect.appendChild(option);
                            });
                        });
                });

                if (classSelect.value && sessionSelect.options.length <= 1) {
                    classSelect.dispatchEvent(new Event('change'));
                }
            }

            bindSessions('material-filter-class', 'material-filter-session', 'Tất cả tài liệu');
            bindSessions('material-upload-class', 'material-upload-session', 'Tài liệu lớp');
        });
    </script>
@endpush
