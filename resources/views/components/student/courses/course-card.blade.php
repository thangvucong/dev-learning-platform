@php
    $statusMap = [
        'ongoing' => ['label' => 'Đang học', 'class' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'],
        'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30'],
        'not_started' => ['label' => 'Chưa bắt đầu', 'class' => 'bg-slate-500/15 text-slate-300 border-slate-500/30'],
    ];
    $statusInfo = $statusMap[$status] ?? $statusMap['ongoing'];
@endphp

<article class="rounded-2xl border border-slate-700 bg-[#111827] overflow-hidden hover:border-slate-500 transition-colors">
    <div class="aspect-[16/8] bg-slate-800">
        @if (!empty($thumbnail))
            <img src="{{ $thumbnail }}" alt="{{ $title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-500 text-sm">
                <i class="fa-solid fa-code-branch mr-2"></i>Lộ trình học tập
            </div>
        @endif
    </div>
    <div class="p-4 space-y-3">
        <div class="flex items-start justify-between gap-3">
            <h3 class="text-white font-semibold line-clamp-2">{{ $title }}</h3>
            <span class="px-3 py-1 rounded-full border text-xs uppercase font-semibold {{ $statusInfo['class'] }}">
                {{ $statusInfo['label'] }}
            </span>
        </div>

        <p class="text-xs text-slate-400"><i class="fa-solid fa-user mr-1"></i>Giảng viên {{ $teacher }}</p>
        <p class="text-xs text-slate-300"><i class="fa-regular fa-clock mr-1"></i>Buổi tiếp theo: {{ $nextSession }}</p>

        <div class="h-2 rounded-full bg-slate-700 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-emerald-400 to-cyan-400" style="width: {{ max(0, min(100, (int) $progress)) }}%"></div>
        </div>
        <div class="flex items-center justify-between text-xs text-slate-400">
            <span>{{ (int) $progress }}% hoàn thành</span>
            <span>{{ (int) $completedSessions }}/{{ (int) $totalSessions }} buổi</span>
        </div>

        <div class="flex items-center gap-2 pt-1">
            <a href="{{ route('user.courses.show', ['id' => $id]) }}"
                class="h-9 px-3 rounded-lg border border-slate-600 text-slate-200 text-sm font-semibold hover:bg-slate-700 transition-colors inline-flex items-center">
                Xem khóa học
            </a>
        </div>
    </div>
</article>

