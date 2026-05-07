@props(['title' => 'Chưa có bài viết', 'subtitle' => 'Hãy bắt đầu viết bài đầu tiên của bạn.', 'ctaLabel' => 'Viết bài mới', 'ctaUrl' => route('posts.create')])

<div class="rounded-2xl border border-gray-200 bg-white p-10 text-center shadow-sm">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#f5f5f5] text-[#242424]">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
        </svg>
    </div>
    <h3 class="mt-4 text-lg font-black text-gray-900">{{ $title }}</h3>
    <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
    <a href="{{ $ctaUrl }}"
        class="mt-6 inline-flex items-center justify-center rounded-full bg-[#f05123] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#d8481f] focus:outline-none focus:ring-2 focus:ring-[#f05123]/20">
        {{ $ctaLabel }}
    </a>
</div>

