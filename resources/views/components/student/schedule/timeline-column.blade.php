<div class="rounded-2xl border border-slate-700 bg-[#111827] p-3">
    <div class="mb-3 flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-widest {{ $day['is_today'] ? 'text-emerald-300' : 'text-slate-500' }}">{{ $day['label'] }}</p>
            <p class="text-sm font-semibold {{ $day['is_today'] ? 'text-emerald-200' : 'text-white' }}">{{ $day['date'] }}</p>
        </div>
        @if ($day['is_today'])
            <span class="text-[10px] uppercase px-2 py-1 rounded-full border border-emerald-500/40 text-emerald-300 bg-emerald-500/10">Today</span>
        @endif
    </div>

    <div class="space-y-3">
        @forelse ($day['sessions'] as $session)
            @include('components.student.schedule.schedule-card', ['session' => $session])
        @empty
            <div class="rounded-xl border border-dashed border-slate-700 p-4 text-center text-xs text-slate-500">
                Không có session
            </div>
        @endforelse
    </div>
</div>

