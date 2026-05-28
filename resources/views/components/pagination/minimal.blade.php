@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination"
        class="flex items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
        <div class="flex flex-1 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="rounded-xl bg-[#f5f5f5] px-3 py-2 text-sm font-semibold text-gray-400">Trước</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="rounded-xl bg-[#f5f5f5] px-3 py-2 text-sm font-semibold text-[#242424] hover:bg-[#ebebeb]">Trước</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="rounded-xl bg-[#f5f5f5] px-3 py-2 text-sm font-semibold text-[#242424] hover:bg-[#ebebeb]">Sau</a>
            @else
                <span class="rounded-xl bg-[#f5f5f5] px-3 py-2 text-sm font-semibold text-gray-400">Sau</span>
            @endif
        </div>

        <div class="hidden flex-1 items-center justify-between sm:flex">
            <p class="text-sm text-gray-600">
                Trang <span class="font-semibold text-gray-900">{{ $paginator->currentPage() }}</span>/<span
                    class="font-semibold text-gray-900">{{ $paginator->lastPage() }}</span>
            </p>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex h-9 items-center justify-center rounded-xl px-3 text-sm font-semibold text-gray-400">
                        Trước
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="inline-flex h-9 items-center justify-center rounded-xl bg-[#f5f5f5] px-3 text-sm font-semibold text-[#242424] hover:bg-[#ebebeb]">
                        Trước
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-2 text-sm text-gray-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[#f05123] text-sm font-bold text-white">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[#f5f5f5] text-sm font-semibold text-[#242424] hover:bg-[#ebebeb]">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="inline-flex h-9 items-center justify-center rounded-xl bg-[#f5f5f5] px-3 text-sm font-semibold text-[#242424] hover:bg-[#ebebeb]">
                        Sau
                    </a>
                @else
                    <span class="inline-flex h-9 items-center justify-center rounded-xl px-3 text-sm font-semibold text-gray-400">
                        Sau
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif

