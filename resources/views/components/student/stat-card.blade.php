@php
    $tone = $tone ?? 'emerald';
    $toneMap = [
        'emerald' => 'bg-[#111827] text-emerald-300 border-emerald-500/30',
        'blue' => 'bg-[#111827] text-blue-300 border-blue-500/30',
        'violet' => 'bg-[#111827] text-violet-300 border-violet-500/30',
        'amber' => 'bg-[#111827] text-amber-300 border-amber-500/30',
    ];
    $toneClass = $toneMap[$tone] ?? $toneMap['emerald'];
@endphp

<div class="rounded-2xl border {{ $toneClass }} p-5 shadow-sm hover:shadow-lg transition-all">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm uppercase tracking-wide text-slate-400">{{ $title }}</p>
            <p class="mt-3 text-3xl font-bold text-white">
                <span @if (!empty($valueId)) id="{{ $valueId }}" @endif>{{ $value }}</span>
                <span class="text-base ml-1 text-slate-300">{{ $suffix ?? '' }}</span>
            </p>
        </div>
        <div class="h-10 w-10 rounded-xl bg-slate-900/40 border border-slate-600 flex items-center justify-center text-base">
            <i class="{{ $icon }}"></i>
        </div>
    </div>
</div>

