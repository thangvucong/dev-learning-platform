@php
    $toneMap = [
        'emerald' => 'from-emerald-500/20 to-emerald-400/5',
        'blue' => 'from-blue-500/20 to-blue-400/5',
        'violet' => 'from-violet-500/20 to-violet-400/5',
        'amber' => 'from-amber-500/20 to-amber-400/5',
    ];
    $toneClass = $toneMap[$tone] ?? $toneMap['blue'];
@endphp

<div class="rounded-2xl border border-slate-700 bg-gradient-to-br {{ $toneClass }} p-4 hover:border-slate-500 transition-colors">
    <div class="flex items-start justify-between">
        <p class="text-xs uppercase tracking-wide text-slate-400">{{ $label }}</p>
        <i class="{{ $icon }} text-slate-300 text-sm"></i>
    </div>
    <p class="mt-3 text-2xl font-bold text-white">{{ $value }}</p>
</div>

