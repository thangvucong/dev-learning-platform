@php
    $session = $session ?? null;
@endphp

<div id="schedule-session-detail" class="rounded-2xl border border-slate-700 bg-[#111827] p-5 h-full">
    <div id="schedule-detail-empty"
        class="{{ $session ? 'hidden' : '' }} h-full flex items-center justify-center text-center text-slate-500">
        <div>
            <i class="fa-regular fa-calendar text-2xl mb-3"></i>
            <p>Chọn một buổi dạy để xem chi tiết.</p>
        </div>
    </div>

    <div id="schedule-detail-content" class="{{ $session ? '' : 'hidden' }}">
        <p class="text-sm uppercase tracking-wide text-slate-400">Chi tiết buổi dạy</p>
        <h3 class="mt-2 text-lg font-semibold text-white" data-detail="class_name">{{ data_get($session, 'class_name', '') }}</h3>
        <p class="mt-1 text-sm text-slate-400" data-detail="course">{{ data_get($session, 'course', '') }}</p>

        <div class="mt-4 grid grid-cols-3 rounded-xl border border-slate-700 bg-slate-900/50 p-1 text-sm font-semibold">
            <button type="button" data-session-tab="detail"
                class="schedule-session-tab rounded-lg px-3 py-2 text-white bg-emerald-500">Chi tiết</button>
            <button type="button" data-session-tab="attendance"
                class="schedule-session-tab rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700">Điểm danh</button>
            <button type="button" data-session-tab="assignments"
                class="schedule-session-tab rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700">Bài tập</button>
        </div>

        <div data-session-panel="detail">
            <div class="mt-4 grid grid-cols-1 gap-2 text-sm text-slate-300">
                <p><i class="fa-solid fa-clipboard-check mr-2"></i><span data-detail="teacher">{{ data_get($session, 'teacher', '') }}</span></p>
                <p><i class="fa-regular fa-clock mr-2"></i><span data-detail="start_at">{{ data_get($session, 'start_at', '') }}</span></p>
                <p><i class="fa-solid fa-location-dot mr-2"></i><span data-detail="meeting_info">{{ data_get($session, 'meeting_info', '') }}</span></p>
            </div>

            <div class="mt-4 rounded-xl border border-slate-700 bg-slate-900/50 p-4">
                <p class="text-sm uppercase tracking-wide text-slate-400 mb-2">Ghi chú</p>
                <p class="text-sm text-slate-300 leading-relaxed" data-detail="description">{{ data_get($session, 'description', '') }}</p>
            </div>

            <p class="mt-4">
                <span id="schedule-detail-status"
                    class="inline-flex items-center px-3 py-1 rounded-full border text-xs uppercase font-semibold"></span>
            </p>

            <div class="mt-5 grid grid-cols-1 gap-2">
                <a id="schedule-detail-join-btn" href="#" target="_blank" rel="noopener noreferrer"
                    class="hidden h-11 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition-colors items-center justify-center">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i>Vào lớp
                </a>
            </div>
        </div>

        <div id="schedule-attendance-panel" class="hidden mt-4" data-session-panel="attendance" data-session-id="{{ data_get($session, 'session_id') }}">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-white">Điểm danh học viên</p>
                    <p class="mt-1 text-xs text-slate-400" data-attendance-summary>Chọn buổi học để tải danh sách.</p>
                </div>
                <button type="button" data-attendance-refresh
                    class="h-9 w-9 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700"
                    title="Làm mới điểm danh" aria-label="Làm mới điểm danh">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <button type="button" data-bulk-attendance="present"
                    class="h-9 rounded-lg border border-emerald-500/40 bg-emerald-500/10 text-emerald-300 font-semibold hover:bg-emerald-500/20">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-400 mr-1"></span>Có mặt
                </button>
                <button type="button" data-bulk-attendance="absent"
                    class="h-9 rounded-lg border border-red-500/40 bg-red-500/10 text-red-300 font-semibold hover:bg-red-500/20">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-red-400 mr-1"></span>Vắng
                </button>
            </div>

            <div class="mt-4">
                <label for="schedule-attendance-search" class="sr-only">Tìm học viên</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                    <input id="schedule-attendance-search" type="search" placeholder="Tìm học viên..."
                        class="w-full h-10 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 pl-9 pr-3 text-sm focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-2 text-xs" data-attendance-filters>
                <button type="button" data-attendance-filter="all" class="attendance-filter is-active">Tất cả</button>
                <button type="button" data-attendance-filter="unmarked" class="attendance-filter">Chưa điểm danh</button>
                <button type="button" data-attendance-filter="present" class="attendance-filter">Có mặt</button>
                <button type="button" data-attendance-filter="late" class="attendance-filter">Trễ</button>
                <button type="button" data-attendance-filter="absent" class="attendance-filter">Vắng</button>
                <button type="button" data-attendance-filter="excused" class="attendance-filter">Có phép</button>
            </div>

            <div data-attendance-message class="mt-4 hidden rounded-xl border border-slate-700 bg-slate-900/50 p-3 text-sm text-slate-300"></div>
            <div data-attendance-list class="attendance-list mt-4 max-h-[430px] space-y-2 overflow-y-auto"></div>
        </div>

        <div id="schedule-assignments-panel" class="hidden mt-4" data-session-panel="assignments" data-session-id="{{ data_get($session, 'session_id') }}">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-white">Bài tập buổi học</p>
                    <p class="mt-1 text-xs text-slate-400" data-assignment-summary>Chọn buổi học để tải bài tập.</p>
                </div>
                <button type="button" data-assignment-refresh
                    class="h-9 w-9 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700"
                    title="Làm mới bài tập" aria-label="Làm mới bài tập">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>

            <form data-assignment-form method="POST" action="#" class="mt-4 rounded-xl border border-slate-700 bg-slate-900/45 p-4 space-y-3">
                <div>
                    <label for="assignment-title" class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Tiêu đề</label>
                    <input id="assignment-title" name="title" type="text" maxlength="255" required
                        class="w-full h-10 rounded-xl bg-slate-950/60 border border-slate-700 text-slate-100 px-3 text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label for="assignment-content" class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Nội dung</label>
                    <textarea id="assignment-content" name="content" rows="3"
                        class="w-full rounded-xl bg-slate-950/60 border border-slate-700 text-slate-100 px-3 py-2 text-sm focus:outline-none focus:border-emerald-500"></textarea>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="assignment-submission-type" class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Kiểu nộp</label>
                        <select id="assignment-submission-type" name="submission_type"
                            class="w-full h-10 rounded-xl bg-slate-950/60 border border-slate-700 text-slate-100 px-3 text-sm focus:outline-none focus:border-emerald-500">
                            <option value="both">Text hoặc file</option>
                            <option value="text">Chỉ text</option>
                            <option value="file">Chỉ file</option>
                        </select>
                    </div>
                    <div>
                        <label for="assignment-due-at" class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Hạn nộp</label>
                        <input id="assignment-due-at" name="due_at" type="datetime-local"
                            class="w-full h-10 rounded-xl bg-slate-950/60 border border-slate-700 text-slate-100 px-3 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                </div>
                <div>
                    <label for="assignment-attachment" class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">File đính kèm</label>
                    <input id="assignment-attachment" name="attachment" type="file"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-700 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-100 hover:file:bg-slate-600">
                </div>
                <div class="flex items-center justify-between gap-2">
                    <select name="status"
                        class="h-10 rounded-xl bg-slate-950/60 border border-slate-700 text-slate-100 px-3 text-sm focus:outline-none focus:border-emerald-500">
                        <option value="published">Giao ngay</option>
                        <option value="draft">Lưu nháp</option>
                    </select>
                    <button type="button" data-assignment-submit
                        class="h-10 rounded-xl bg-emerald-500 px-4 text-sm font-semibold text-white hover:bg-emerald-600">
                        Giao bài
                    </button>
                </div>
            </form>

            <div data-assignment-message class="mt-4 hidden rounded-xl border border-slate-700 bg-slate-900/50 p-3 text-sm text-slate-300"></div>
            <div data-assignment-list class="attendance-list mt-4 max-h-[360px] space-y-2 overflow-y-auto"></div>
        </div>
    </div>
</div>
