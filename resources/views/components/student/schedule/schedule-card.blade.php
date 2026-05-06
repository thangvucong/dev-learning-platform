@php
    $status = $session['status'] ?? 'upcoming';
    $statusMap = [
        'upcoming' => ['label' => $session['relative'] ?? 'Sắp diễn ra', 'class' => 'bg-blue-500/10 text-blue-300 border-blue-500/30'],
        'live' => ['label' => 'Đang LIVE', 'class' => 'bg-emerald-500/20 text-emerald-200 border-emerald-400/40'],
        'completed' => ['label' => 'Đã hoàn thành', 'class' => 'bg-slate-500/10 text-slate-300 border-slate-500/30'],
        'missed' => ['label' => 'Đã lỡ buổi', 'class' => 'bg-amber-500/10 text-amber-300 border-amber-500/30'],
    ];
    $statusInfo = $statusMap[$status] ?? $statusMap['upcoming'];
@endphp

<button type="button" data-session-item='@json($session)'
    class="schedule-session-item w-full text-left rounded-2xl border border-slate-700 bg-slate-900/50 p-4 hover:border-slate-500 transition-all group">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-white group-hover:text-emerald-300 transition-colors">{{ $session['class_name'] }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ $session['teacher'] }}</p>
            <p class="mt-2 text-xs text-slate-300"><i class="fa-regular fa-clock mr-1"></i>{{ $session['time'] }}</p>
            <p class="mt-1 text-xs text-slate-400"><i class="fa-solid {{ ($session['meeting_type'] ?? 'zoom') === 'offline' ? 'fa-location-dot' : 'fa-video' }} mr-1"></i>{{ $session['meeting_info'] }}</p>
        </div>
        <span class="px-2 py-1 rounded-full border text-[10px] uppercase font-semibold {{ $statusInfo['class'] }}">
            {{ $statusInfo['label'] }}
        </span>
    </div>

    <div class="mt-3 flex items-center gap-2">
        <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-emerald-500/30 text-emerald-300">
            <i class="fa-solid fa-right-to-bracket text-xs"></i>
        </span>
        <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-600 text-slate-300">
            <i class="fa-solid fa-eye text-xs"></i>
        </span>
    </div>
</button>

