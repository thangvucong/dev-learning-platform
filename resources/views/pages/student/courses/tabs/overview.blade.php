<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="xl:col-span-2 rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-lg font-semibold text-white mb-3">Mô tả khóa học</h3>
        <p class="text-sm text-slate-300">{{ $overview['description'] }}</p>

        <h4 class="text-sm font-semibold text-white mt-6 mb-3">Kỹ năng đạt được</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            @foreach ($overview['skills'] as $skill)
                <div class="rounded-lg border border-slate-700 bg-slate-950/30 p-3 text-sm text-slate-200">
                    <i class="fa-solid fa-check text-emerald-400 mr-2"></i>{{ $skill }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="xl:col-span-1 rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-lg font-semibold text-white">Thông tin giảng viên</h3>
        <p class="mt-3 text-sm text-slate-300">{{ $course['teacher'] }}</p>
        <p class="text-xs text-slate-500">{{ $course['teacher_email'] ?: 'Đang cập nhật' }}</p>

        <div class="mt-5 space-y-2">
            @foreach ($overview['statistics'] as $stat)
                <div class="rounded-lg border border-slate-700 bg-slate-950/30 p-3 flex items-center justify-between">
                    <span class="text-xs text-slate-400">{{ $stat['label'] }}</span>
                    <span class="text-sm text-white font-semibold">{{ $stat['value'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
