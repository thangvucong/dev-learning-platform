@php
    $stateMap = [
        'completed' => ['icon' => 'fa-solid fa-circle-check', 'label' => 'Hoàn thành', 'class' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'],
        'current' => ['icon' => 'fa-solid fa-hourglass-half', 'label' => 'Đang học', 'class' => 'border-blue-500/40 bg-blue-500/10 text-blue-200'],
        'locked' => ['icon' => 'fa-solid fa-lock', 'label' => 'Khóa', 'class' => 'border-slate-600 bg-slate-800/40 text-slate-300'],
    ];
    $stateInfo = $stateMap[$state] ?? $stateMap['locked'];
@endphp

<div class="rounded-xl border p-4 {{ $stateInfo['class'] }}">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-semibold">{{ $title }}</p>
            <p class="text-xs mt-1 opacity-90">{{ $subtitle }}</p>
        </div>
        <span class="text-xs font-semibold"><i class="{{ $stateInfo['icon'] }} mr-1"></i>{{ $stateInfo['label'] }}</span>
    </div>
    <p class="text-[11px] mt-3 opacity-80">{{ (int) $sessions }} buổi học</p>
</div>

