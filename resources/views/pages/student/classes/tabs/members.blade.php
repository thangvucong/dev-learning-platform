<div class="space-y-4">
    <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-4">
        <p class="text-xs uppercase tracking-wider text-slate-400 mb-2">Teacher</p>
        <p class="text-sm font-semibold text-white">{{ $members['teacher']['name'] }}</p>
        <p class="text-xs text-slate-400">{{ $members['teacher']['email'] }}</p>
    </div>

    <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-4">
        <p class="text-xs uppercase tracking-wider text-slate-400 mb-3">Học viên</p>
        <div class="space-y-2">
            @foreach ($members['students'] as $student)
                <div class="flex items-center justify-between rounded-xl border border-slate-700 bg-slate-800/40 px-3 py-2">
                    <div>
                        <p class="text-sm text-white font-medium">{{ $student['name'] }}</p>
                        <p class="text-xs text-slate-400">{{ $student['email'] }}</p>
                    </div>
                    <i class="fa-regular fa-user text-slate-500"></i>
                </div>
            @endforeach
        </div>
    </div>
</div>

