<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="xl:col-span-2 rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-lg font-semibold text-white mb-4">Huy hiệu điểm danh</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($achievements['badges'] as $badge)
                @include('components.student.profile.achievement-card', [
                    'title' => $badge['name'],
                    'description' => $badge['description'],
                ])
            @endforeach
        </div>
    </div>

    <div class="xl:col-span-1 rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-lg font-semibold text-white">Cột mốc học tập</h3>
        <div class="mt-3 space-y-2">
            @foreach ($achievements['milestones'] as $milestone)
                <div class="rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-200">
                    <i class="fa-solid fa-trophy text-amber-400 mr-2"></i>{{ $milestone }}
                </div>
            @endforeach
        </div>

        <div class="rounded-xl border border-slate-700 bg-slate-950/40 p-3 mt-4">
            <p class="text-sm text-slate-400">Khóa học hoàn thành</p>
            <p class="text-2xl text-white font-bold mt-1">{{ (int) $achievements['completed_courses'] }}</p>
            <p class="text-sm text-slate-500 mt-2">Chứng chỉ sẽ hiển thị tại đây ở giai đoạn tiếp theo.</p>
        </div>
    </div>
</div>

