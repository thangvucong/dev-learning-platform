@php
    $session = $session ?? null;
@endphp

<div id="schedule-session-detail" class="rounded-2xl border border-slate-700 bg-[#111827] p-5 h-full">
    <div id="schedule-detail-empty"
        class="{{ $session ? 'hidden' : '' }} h-full flex items-center justify-center text-center text-slate-500">
        <div>
            <i class="fa-regular fa-calendar text-2xl mb-3"></i>
            <p>Chọn một session để xem chi tiết.</p>
        </div>
    </div>

    <div id="schedule-detail-content" class="{{ $session ? '' : 'hidden' }}">
        <p class="text-sm uppercase tracking-wide text-slate-400">Chi tiết buổi học</p>
        <h3 class="mt-2 text-lg font-semibold text-white" data-detail="class_name">{{ data_get($session, 'class_name', '') }}</h3>
        <p class="mt-1 text-sm text-slate-400" data-detail="course">{{ data_get($session, 'course', '') }}</p>

        <div class="mt-4 grid grid-cols-2 rounded-xl border border-slate-700 bg-slate-900/50 p-1 text-sm font-semibold">
            <button type="button" data-session-tab="detail"
                class="schedule-session-tab rounded-lg px-3 py-2 text-white bg-emerald-500">Chi tiết</button>
            <button type="button" data-session-tab="assignments"
                class="schedule-session-tab rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700">Bài tập</button>
        </div>

        <div data-session-panel="detail">
            <div class="mt-4 space-y-2 text-sm text-slate-300">
                <p><i class="fa-solid fa-user mr-2"></i><span data-detail="teacher">{{ data_get($session, 'teacher', '') }}</span></p>
                <p><i class="fa-regular fa-clock mr-2"></i><span data-detail="start_at">{{ data_get($session, 'start_at', '') }}</span></p>
                <p><i class="fa-solid fa-location-dot mr-2"></i><span data-detail="meeting_info">{{ data_get($session, 'meeting_info', '') }}</span></p>
            </div>

            <div class="mt-4 rounded-xl border border-slate-700 bg-slate-900/50 p-4">
                <p class="text-sm uppercase tracking-wide text-slate-400 mb-2">Mô tả buổi học</p>
                <p class="text-sm text-slate-300 leading-relaxed" data-detail="description">{{ data_get($session, 'description', '') }}</p>
            </div>

            <p class="mt-4">
                <span id="schedule-detail-status"
                    class="inline-flex items-center px-3 py-1 rounded-full border text-xs uppercase font-semibold"></span>
            </p>

            <a id="schedule-detail-join-btn" href="#" target="_blank" rel="noopener noreferrer"
                class="hidden mt-5 w-full h-11 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition-colors items-center justify-center">
                <i class="fa-solid fa-right-to-bracket mr-2"></i>Vào học
            </a>
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

            <div data-assignment-message class="mt-4 hidden rounded-xl border border-slate-700 bg-slate-900/50 p-3 text-sm text-slate-300"></div>
            <div data-assignment-list class="attendance-list mt-4 max-h-[520px] space-y-3 overflow-y-auto"></div>
        </div>
    </div>
</div>
