<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    @foreach ($materials as $material)
        <a href="{{ $material['url'] }}"
            class="rounded-2xl border border-slate-700 bg-slate-900/40 p-4 hover:border-slate-500 transition-colors">
            <p class="text-xs uppercase tracking-wider text-slate-400">{{ $material['type'] }}</p>
            <p class="mt-2 text-sm font-semibold text-white">{{ $material['name'] }}</p>
        </a>
    @endforeach
</div>

