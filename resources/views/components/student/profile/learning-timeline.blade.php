<div class="space-y-3">
    @foreach ($timeline as $point)
        <div class="rounded-xl border border-slate-700 bg-slate-900/40 px-4 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                <p class="text-sm text-slate-200">{{ $point['title'] }}</p>
            </div>
            <span class="text-xs text-slate-400">{{ $point['date'] }}</span>
        </div>
    @endforeach
</div>

