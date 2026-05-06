@php
    $tone = $tone ?? 'emerald';
    $toneMap = [
        'emerald' => 'from-emerald-500/20 to-emerald-400/5 text-emerald-300 border-emerald-400/20',
        'blue' => 'from-blue-500/20 to-blue-400/5 text-blue-300 border-blue-400/20',
        'violet' => 'from-violet-500/20 to-violet-400/5 text-violet-300 border-violet-400/20',
        'amber' => 'from-amber-500/20 to-amber-400/5 text-amber-300 border-amber-400/20',
    ];
    $toneClass = $toneMap[$tone] ?? $toneMap['emerald'];
@endphp

<div class="rounded-2xl border bg-gradient-to-br {{ $toneClass }} p-5 shadow-sm hover:shadow-lg transition-all">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-400">{{ $title }}</p>
            <p class="mt-3 text-3xl font-bold text-white">{{ $value }}<span class="text-base ml-1 text-slate-300">{{ $suffix ?? '' }}</span></p>
        </div>
        <div class="h-10 w-10 rounded-xl bg-slate-900/40 border border-slate-600 flex items-center justify-center text-base">
            <i class="{{ $icon }}"></i>
        </div>
    </div>
</div>

