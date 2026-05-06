@php
    $level = $level ?? 0;
    $baseLinkClass =
        'flex items-center gap-3 px-3 py-2 rounded-lg transition-all text-sm ' .
        ($item['is_active']
            ? 'bg-emerald-500/10 text-emerald-500 font-medium'
            : 'text-slate-400 hover:bg-slate-800 hover:text-white');
@endphp

@if (!empty($item['has_children']))
    <details class="group" @if (!empty($item['is_open'])) open @endif>
        <summary class="list-none cursor-pointer {{ $baseLinkClass }}">
            <i class="{{ $item['icon'] }} w-4 text-center"></i>
            <span class="flex-1">{{ $item['title'] }}</span>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500 transition-transform group-open:rotate-90"></i>
        </summary>

        <div class="mt-1 ml-4 pl-2 border-l border-slate-700 space-y-1">
            @foreach ($item['children'] as $child)
                @include('components.layouts.admin.partials.sidebar-item', [
                    'item' => $child,
                    'level' => $level + 1,
                ])
            @endforeach
        </div>
    </details>
@else
    <a href="{{ $item['url'] }}"
        class="{{ $baseLinkClass }} {{ !$item['has_route'] ? 'opacity-60 cursor-not-allowed' : '' }}"
        @if (!$item['has_route']) aria-disabled="true" @endif>
        <i class="{{ $item['icon'] }} w-4 text-center"></i>
        <span class="flex-1">{{ $item['title'] }}</span>
        @if (!$item['has_route'])
            <span class="text-[10px] uppercase text-slate-500">Soon</span>
        @endif
    </a>
@endif

