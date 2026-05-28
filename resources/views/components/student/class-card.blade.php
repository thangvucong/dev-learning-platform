@php
    $id = $id ?? null;
    $status = $status ?? 'ongoing';
    $attendanceRate = isset($attendanceRate) ? (int) $attendanceRate : null;
    $joinUrl = $joinUrl ?? '';
    $detailUrl = $id ? route('user.classes.show', ['id' => $id]) : '#';
    $statusMap = [
        'ongoing' => ['label' => 'Đang học', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30'],
        'upcoming' => ['label' => 'Sắp bắt đầu', 'class' => 'bg-blue-500/10 text-blue-300 border-blue-500/30'],
        'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-slate-500/10 text-slate-300 border-slate-500/30'],
    ];
    $statusInfo = $statusMap[$status] ?? $statusMap['ongoing'];
@endphp

<div class="rounded-2xl border border-slate-700 bg-[#111827] overflow-hidden hover:border-slate-500 transition-colors">
    <div class="aspect-[16/8] bg-slate-800">
        @if (!empty($thumbnail))
            <img src="{{ $thumbnail }}" alt="{{ $name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-500 text-sm">
                <i class="fa-regular fa-image mr-2"></i>Chưa có ảnh lớp học
            </div>
        @endif
    </div>
    <div class="p-4">
        <div class="flex items-start justify-between gap-2">
            <p class="text-sm font-semibold text-white line-clamp-2">{{ $name }}</p>
            <span class="px-3 py-1 rounded-full border text-xs uppercase font-semibold {{ $statusInfo['class'] }}">
                {{ $statusInfo['label'] }}
            </span>
        </div>
        <p class="mt-1 text-xs text-slate-400">{{ $course }}</p>
        <p class="mt-1 text-xs text-slate-400"><i class="fa-solid fa-user mr-1"></i>Giảng viên {{ $teacher }}</p>
        <p class="mt-2 text-xs text-slate-300"><i class="fa-regular fa-clock mr-1"></i>Buổi tiếp theo: {{ $nextSession }}</p>

        <div class="mt-3">
            <div class="h-2 rounded-full bg-slate-700 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-400" style="width: {{ max(0, min(100, (int) $progress)) }}%"></div>
            </div>
            <p class="mt-1 text-[11px] text-slate-400">{{ (int) $progress }}% tiến độ</p>
        </div>

        <div class="mt-4 flex items-center gap-2">
            @if (!empty($joinUrl))
                <a href="{{ $joinUrl }}" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center h-9 px-3 rounded-lg border border-emerald-500/30 text-emerald-300 hover:bg-emerald-500/10 transition-colors text-sm font-semibold"
                    title="Vào học" aria-label="Vào học">
                    <i class="fa-solid fa-right-to-bracket text-xs mr-2"></i>Vào học
                </a>
            @endif
            <a href="{{ $detailUrl }}"
                class="inline-flex items-center h-9 px-3 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700 transition-colors text-sm font-semibold"
                title="Xem chi tiết" aria-label="Xem chi tiết">
                <i class="fa-solid fa-eye text-xs mr-2"></i>Xem chi tiết
            </a>
        </div>
    </div>
</div>

