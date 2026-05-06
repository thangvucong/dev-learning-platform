<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-base font-semibold text-white mb-3">Mô tả lớp học</h3>
        <p class="text-sm text-slate-300 leading-relaxed">{{ $class['description'] }}</p>
    </div>

    <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-5 space-y-3">
        <h3 class="text-base font-semibold text-white">Thống kê nhanh</h3>
        <p class="text-sm text-slate-300">Thành viên: <span class="text-white font-semibold">{{ (int) $overview['total_members'] }}</span></p>
        <p class="text-sm text-slate-300">Buổi đã học: <span class="text-white font-semibold">{{ (int) $overview['completed_sessions'] }}</span></p>
        <p class="text-sm text-slate-300">Buổi còn lại: <span class="text-white font-semibold">{{ (int) $overview['remaining_sessions'] }}</span></p>
        <p class="text-sm text-slate-300">Study streak: <span class="text-white font-semibold">{{ (int) $overview['study_streak_days'] }} ngày</span></p>
    </div>
</div>

