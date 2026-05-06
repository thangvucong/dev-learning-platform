<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-5 lg:col-span-1">
        <p class="text-xs uppercase tracking-wider text-slate-400">Attendance %</p>
        <p class="text-4xl font-bold text-white mt-2">{{ (int) $attendance['rate'] }}%</p>
        <div class="mt-4 space-y-2 text-xs text-slate-300">
            <p>Present: <span class="text-emerald-300">{{ (int) $attendance['present'] }}</span></p>
            <p>Absent: <span class="text-red-300">{{ (int) $attendance['absent'] }}</span></p>
            <p>Late: <span class="text-amber-300">{{ (int) $attendance['late'] }}</span></p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-5 lg:col-span-2">
        <p class="text-xs uppercase tracking-wider text-slate-400 mb-3">Timeline attendance</p>
        <div class="space-y-2">
            @foreach ($attendance['timeline'] as $item)
                <div class="flex items-center justify-between rounded-xl border border-slate-700 bg-slate-800/40 px-3 py-2">
                    <p class="text-sm text-slate-200">{{ $item['date'] }}</p>
                    @include('components.student.attendance-badge', ['status' => $item['status']])
                </div>
            @endforeach
        </div>
    </div>
</div>

