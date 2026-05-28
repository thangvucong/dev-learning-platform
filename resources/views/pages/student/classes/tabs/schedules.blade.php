<div class="space-y-3">
    @foreach ($schedules as $session)
        <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h4 class="text-sm font-semibold text-white">{{ $session['title'] }}</h4>
                    <p class="text-xs text-slate-400 mt-1">
                        <i class="fa-regular fa-clock mr-1"></i>{{ $session['start_at'] }} - {{ $session['end_at'] }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1">
                        <i class="fa-solid fa-location-dot mr-1"></i>{{ $session['location'] }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @include('components.student.attendance-badge', ['status' => $session['status'] === 'completed' ? 'present' : ($session['status'] === 'ongoing' ? 'late' : 'absent')])
                    <button type="button" class="h-8 px-3 rounded-lg border border-emerald-500/30 text-emerald-300 text-xs font-semibold hover:bg-emerald-500/10">
                        <i class="fa-solid fa-right-to-bracket mr-1"></i>Join class
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

