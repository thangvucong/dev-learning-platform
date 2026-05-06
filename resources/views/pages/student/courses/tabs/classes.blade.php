<div class="space-y-3">
    @foreach ($classes as $classItem)
        <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-white">{{ $classItem['name'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Mentor {{ $classItem['mentor'] }}</p>
                <p class="text-xs text-slate-400 mt-1"><i class="fa-regular fa-clock mr-1"></i>{{ $classItem['schedule'] }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">{{ $classItem['location'] }}</span>
                <button type="button" class="h-9 px-3 rounded-lg bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
                    Join class
                </button>
            </div>
        </div>
    @endforeach
</div>

