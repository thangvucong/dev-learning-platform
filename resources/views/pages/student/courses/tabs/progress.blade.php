<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
        @include('components.student.courses.progress-widget', ['label' => 'Tiến độ tổng', 'value' => $course['progress'] . '%'])
        @include('components.student.courses.progress-widget', ['label' => 'Attendance', 'value' => $course['attendance_rate'] . '%'])
        @include('components.student.courses.progress-widget', ['label' => 'Study streak', 'value' => $progress['study_streak'] . ' ngày'])
        @include('components.student.courses.progress-widget', ['label' => 'Estimated completion', 'value' => $progress['estimated_completion']])
    </div>

    <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-lg font-semibold text-white">Progress timeline</h3>
        <div class="mt-4 space-y-2">
            @foreach ($progress['timeline'] as $point)
                <div class="rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 flex items-center justify-between">
                    <span class="text-sm text-slate-300">{{ $point['label'] }}</span>
                    <span class="text-sm font-semibold text-emerald-300">{{ $point['value'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

