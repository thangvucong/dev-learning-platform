@php
    $items = [
        [
            'label' => 'Trang chủ',
            'route' => 'home',
            'href' => route('home'),
            'icon' => 'fa-solid fa-house',
            'active' => request()->routeIs('home'),
        ],
        [
            'label' => 'Bài viết',
            'route' => 'posts.index',
            'href' => route('posts.index'),
            'icon' => 'fa-regular fa-newspaper',
            'active' => request()->routeIs('posts.*') || request()->routeIs('my-posts.*'),
        ],
    ];
@endphp

<div class="w-[96px] fixed left-0 top-[66px] bottom-0 flex flex-col items-center py-6 gap-4 z-40 bg-white">
    @foreach ($items as $item)
        <a href="{{ $item['href'] }}"
            class="flex h-[72px] w-[72px] flex-col items-center justify-center gap-2 rounded-2xl text-[#444] transition-colors hover:bg-[#f1f2f4] hover:text-[#242424] {{ $item['active'] ? 'bg-[#e8ebee] text-[#242424]' : '' }}">
            <i class="{{ $item['icon'] }} text-[17px]" aria-hidden="true"></i>
            <span class="text-[11px] font-semibold leading-none tracking-normal">{{ $item['label'] }}</span>
        </a>
    @endforeach
</div>
