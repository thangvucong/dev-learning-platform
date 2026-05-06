<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="xl:col-span-2 space-y-4">
        <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
            <h3 class="text-lg font-semibold text-white mb-4">Progress timeline</h3>
            @include('components.student.profile.learning-timeline', ['timeline' => $learning['timeline']])
        </div>

        <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
            <h3 class="text-lg font-semibold text-white mb-3">Recently completed modules</h3>
            <div class="space-y-2">
                @foreach ($learning['recent_modules'] as $module)
                    <div class="rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-200">
                        <i class="fa-solid fa-check text-emerald-400 mr-2"></i>{{ $module }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="xl:col-span-1 space-y-4">
        <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
            <h3 class="text-lg font-semibold text-white mb-3">Khóa học đang học</h3>
            <div class="space-y-2">
                @forelse ($learning['courses'] as $courseItem)
                    <div class="rounded-lg border border-slate-700 bg-slate-950/40 p-3">
                        <p class="text-sm text-slate-100 font-semibold">{{ $courseItem['title'] }}</p>
                        <p class="text-xs text-slate-400 mt-1">Mentor {{ $courseItem['mentor'] }}</p>
                        <div class="mt-2 h-1.5 rounded-full bg-slate-700 overflow-hidden">
                            <div class="h-full bg-emerald-400" style="width: {{ (int) $courseItem['progress'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Bạn chưa có khóa học đang học.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
            <h3 class="text-lg font-semibold text-white mb-3">Upcoming classes</h3>
            <div class="space-y-2">
                @forelse ($learning['upcoming_classes'] as $classItem)
                    <div class="rounded-lg border border-slate-700 bg-slate-950/40 p-3">
                        <p class="text-sm text-slate-100">{{ $classItem['name'] }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $classItem['start_at'] }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Chưa có lớp học sắp tới.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

