@php
    $session = $session ?? null;
@endphp

<div id="schedule-session-detail" class="rounded-2xl border border-slate-700 bg-[#111827] p-5 h-full">
    @if ($session)
        <p class="text-xs uppercase tracking-wider text-slate-400">Session detail</p>
        <h3 class="mt-2 text-lg font-semibold text-white" data-detail="class_name">{{ $session['class_name'] }}</h3>
        <p class="mt-1 text-sm text-slate-400" data-detail="course">{{ $session['course'] }}</p>

        <div class="mt-4 space-y-2 text-sm text-slate-300">
            <p><i class="fa-solid fa-user mr-2"></i><span data-detail="teacher">{{ $session['teacher'] }}</span></p>
            <p><i class="fa-regular fa-clock mr-2"></i><span data-detail="start_at">{{ $session['start_at'] }}</span></p>
            <p><i class="fa-solid fa-location-dot mr-2"></i><span data-detail="meeting_info">{{ $session['meeting_info'] }}</span></p>
        </div>

        <div class="mt-4 rounded-xl border border-slate-700 bg-slate-900/50 p-4">
            <p class="text-xs uppercase tracking-wider text-slate-400 mb-2">Mô tả buổi học</p>
            <p class="text-sm text-slate-300 leading-relaxed" data-detail="description">{{ $session['description'] }}</p>
        </div>

        <p class="mt-4">
            <span id="schedule-detail-status" class="inline-flex items-center px-2 py-1 rounded-full border text-[10px] uppercase font-semibold"></span>
        </p>

        <button type="button" id="schedule-detail-join-btn"
            class="mt-5 w-full h-11 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition-colors">
            <i class="fa-solid fa-right-to-bracket mr-2"></i>Quick Join
        </button>
    @else
        <div class="h-full flex items-center justify-center text-center text-slate-500">
            <div>
                <i class="fa-regular fa-calendar text-2xl mb-3"></i>
                <p>Chọn một session để xem chi tiết.</p>
            </div>
        </div>
    @endif
</div>

