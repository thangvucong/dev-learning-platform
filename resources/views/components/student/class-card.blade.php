<div class="rounded-2xl border border-slate-700 bg-[#111827] overflow-hidden hover:border-slate-500 transition-colors">
    <div class="aspect-[16/8] bg-slate-800">
        @if (!empty($thumbnail))
            <img src="{{ $thumbnail }}" alt="{{ $name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-500 text-sm">
                <i class="fa-regular fa-image mr-2"></i>No thumbnail
            </div>
        @endif
    </div>
    <div class="p-4">
        <p class="text-sm font-semibold text-white line-clamp-2">{{ $name }}</p>
        <p class="mt-1 text-xs text-slate-400">{{ $course }}</p>
        <p class="mt-1 text-xs text-slate-400"><i class="fa-solid fa-user mr-1"></i>{{ $teacher }}</p>
        <p class="mt-2 text-xs text-slate-300"><i class="fa-regular fa-clock mr-1"></i>Buổi tiếp theo: {{ $nextSession }}</p>

        <div class="mt-3">
            <div class="h-2 rounded-full bg-slate-700 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-400" style="width: {{ max(0, min(100, (int) $progress)) }}%"></div>
            </div>
            <p class="mt-1 text-[11px] text-slate-400">{{ (int) $progress }}% tiến độ</p>
        </div>
    </div>
</div>

