@php
    $status = $status ?? 'upcoming';
    $statusMap = [
        'ongoing' => ['label' => 'Đang diễn ra', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30'],
        'upcoming' => ['label' => 'Sắp diễn ra', 'class' => 'bg-blue-500/10 text-blue-300 border-blue-500/30'],
        'completed' => ['label' => 'Đã kết thúc', 'class' => 'bg-slate-500/10 text-slate-300 border-slate-500/30'],
    ];
    $statusInfo = $statusMap[$status] ?? $statusMap['upcoming'];
@endphp

<div class="rounded-2xl border border-slate-700 bg-[#111827] p-4 hover:border-slate-500 transition-colors">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-white">{{ $className }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ $courseName }}</p>
            <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-300">
                <span><i class="fa-regular fa-clock mr-1"></i>{{ $startTime }}{{ !empty($endTime) ? ' - ' . $endTime : '' }}</span>
                <span><i class="fa-solid fa-user mr-1"></i>{{ $teacherName }}</span>
                <span><i class="fa-solid fa-location-dot mr-1"></i>{{ $location }}</span>
            </div>
        </div>
        <span class="px-2 py-1 rounded-full border text-[10px] uppercase font-semibold {{ $statusInfo['class'] }}">
            {{ $statusInfo['label'] }}
        </span>
    </div>
    <div class="mt-4">
        <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 text-xs font-semibold hover:bg-emerald-500/25 transition-colors">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            Vào học
        </button>
    </div>
</div>

