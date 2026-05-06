@php
    $viewMap = [
        'week' => 'timeGridWeek',
        'month' => 'dayGridMonth',
        'day' => 'timeGridDay',
        'list' => 'listWeek',
    ];
    $statusColorMap = [
        'upcoming' => '#3b82f6',
        'live' => '#10b981',
        'completed' => '#6b7280',
        'missed' => '#ef4444',
    ];

    $events = collect($sessions ?? [])->map(function ($session) use ($statusColorMap) {
        $status = (string) ($session['status'] ?? 'upcoming');
        $color = $statusColorMap[$status] ?? '#3b82f6';

        return [
            'id' => $session['id'],
            'title' => $session['class_name'],
            'start' => $session['start_iso'],
            'end' => $session['end_iso'],
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => $session,
        ];
    })->values();
@endphp

<section class="rounded-2xl border border-slate-700 bg-[#111827] p-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <button type="button" data-cal-nav="prev"
                class="h-10 px-3 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <button type="button" data-cal-nav="today"
                class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700 transition-colors">
                Today
            </button>
            <button type="button" data-cal-nav="next"
                class="h-10 px-3 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>
        <p id="schedule-date-range" class="text-sm font-semibold text-slate-300">{{ $header['week_range'] }}</p>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @foreach ([
            'timeGridDay' => 'Day',
            'timeGridWeek' => 'Week',
            'dayGridMonth' => 'Month',
            'listWeek' => 'List',
        ] as $viewKey => $viewLabel)
            <button type="button" data-cal-view="{{ $viewKey }}"
                class="schedule-view-btn h-9 px-3 rounded-xl border border-slate-600 text-slate-300 text-sm font-medium hover:bg-slate-700 transition-colors">
                {{ $viewLabel }}
            </button>
        @endforeach
    </div>

    <div id="student-calendar-root" class="mt-4"
        data-events='@json($events)'
        data-initial-view="{{ $viewMap[$header['view_mode']] ?? 'timeGridWeek' }}">
        <div id="student-calendar"></div>
    </div>
</section>

