@php
    $normalized = max(0, min(100, (int) $progress));
@endphp

<div class="rounded-2xl border border-slate-700 bg-[#111827] p-4 hover:border-slate-500 transition-colors">
    <div class="flex items-start gap-3">
        <div class="h-12 w-12 rounded-lg overflow-hidden bg-slate-800 shrink-0">
            @if (!empty($thumbnail))
                <img src="{{ $thumbnail }}" alt="{{ $title }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs">
                    <i class="fa-regular fa-image"></i>
                </div>
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-white line-clamp-2">{{ $title }}</p>
            <p class="mt-1 text-xs text-slate-400"><i class="fa-solid fa-user mr-1"></i>{{ $teacher }}</p>
        </div>
    </div>

    <div class="mt-4">
        <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1">
            <span>{{ ucfirst($status) }}</span>
            <span>{{ $normalized }}%</span>
        </div>
        <div class="h-2 rounded-full bg-slate-700 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-indigo-400 to-violet-400" style="width: {{ $normalized }}%"></div>
        </div>
    </div>
</div>

