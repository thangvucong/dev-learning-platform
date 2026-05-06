<div class="rounded-xl border border-slate-700 bg-slate-900/50 p-4">
    <p class="text-xs uppercase tracking-wide text-slate-400">{{ $label }}</p>
    <p class="mt-2 text-2xl font-bold text-white">{{ $value }}</p>
    @if (!empty($hint))
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>

