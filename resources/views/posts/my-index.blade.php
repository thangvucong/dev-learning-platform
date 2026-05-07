@extends('layouts.app')

@section('title', 'Bài viết của tôi')

@section('content')
    <main class="flex-1 lg:ml-24">
        <div class="mx-auto w-full max-w-[1140px] p-4 lg:p-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900">Bài viết của tôi</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Quản lý bài viết, theo dõi trạng thái kiểm duyệt và tối ưu nội dung của bạn.
                    </p>
                </div>
                <a href="{{ route('posts.create') }}"
                    class="inline-flex items-center justify-center rounded-full bg-[#f05123] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#d8481f] focus:outline-none focus:ring-2 focus:ring-[#f05123]/20">
                    Viết bài mới
                </a>
            </div>

            <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="-mx-1 flex gap-2 overflow-x-auto pb-1 px-1 scrollbar-hide">
                    @foreach ($tabs as $key => $label)
                        @php
                            $count = (int) ($counts[$key] ?? 0);
                            $isActive = $status === $key;
                        @endphp
                        <a href="{{ route('my-posts.index', array_filter(['status' => $key, 'q' => $q, 'sort' => $sort])) }}"
                            class="shrink-0 inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition
                                {{ $isActive ? 'border-[#f05123] bg-[#f05123]/10 text-[#f05123]' : 'border-gray-200 bg-white text-[#242424] hover:bg-[#f5f5f5]' }}">
                            <span>{{ $label }}</span>
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-bold {{ $isActive ? 'bg-[#f05123] text-white' : 'bg-[#f5f5f5] text-gray-700' }}">
                                {{ $count }}
                            </span>
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('my-posts.index') }}"
                    class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="relative w-full sm:w-[360px]">
                        <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </div>
                        <input name="q" value="{{ $q }}" placeholder="Tìm theo tiêu đề…"
                            class="w-full rounded-full border border-gray-200 bg-white py-2 pl-10 pr-3 text-sm text-gray-900 placeholder-[#a9b3bb] shadow-sm focus:border-[#f05123] focus:ring-2 focus:ring-[#f05123]/20">
                    </div>
                    <select name="sort"
                        class="w-full sm:w-auto rounded-full border border-gray-200 bg-white py-2 pl-3 pr-10 text-sm font-semibold text-[#242424] shadow-sm focus:border-[#f05123] focus:ring-2 focus:ring-[#f05123]/20">
                        <option value="newest" @selected($sort === 'newest')>Mới nhất</option>
                        <option value="oldest" @selected($sort === 'oldest')>Cũ nhất</option>
                    </select>
                    <button type="submit"
                        class="rounded-full bg-[#f5f5f5] px-4 py-2 text-sm font-semibold text-[#242424] hover:bg-[#ebebeb]">
                        Lọc
                    </button>
                </form>
            </div>

            @if ($posts->count() === 0)
                @php
                    $emptyMap = [
                        \App\Models\Post::STATUS_PUBLISHED => [
                            'title' => 'Chưa có bài đã xuất bản',
                            'subtitle' => 'Khi bài được duyệt và xuất bản, nó sẽ xuất hiện ở đây.',
                        ],
                        \App\Models\Post::STATUS_DRAFT => [
                            'title' => 'Chưa có bản nháp',
                            'subtitle' => 'Bạn có thể bắt đầu viết và lưu nháp để hoàn thiện sau.',
                        ],
                        \App\Models\Post::STATUS_PENDING => [
                            'title' => 'Chưa có bài chờ duyệt',
                            'subtitle' => 'Gửi bài để xét duyệt, bạn sẽ thấy trạng thái ở đây.',
                        ],
                        \App\Models\Post::STATUS_REJECTED => [
                            'title' => 'Chưa có bài bị từ chối',
                            'subtitle' => 'Nếu bài bị từ chối, lý do sẽ hiển thị tại đây để bạn chỉnh sửa.',
                        ],
                    ];
                    $empty = $emptyMap[$status] ?? $emptyMap[\App\Models\Post::STATUS_PUBLISHED];
                @endphp
                <x-posts.empty-state :title="$empty['title']" :subtitle="$empty['subtitle']" />
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <div
                            class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <a href="{{ route('posts.show', $post['slug']) }}" class="block">
                                <div class="relative overflow-hidden">
                                    @php
                                        $thumb = $post['thumbnail']
                                            ? \Illuminate\Support\Facades\Storage::url($post['thumbnail'])
                                            : null;
                                    @endphp
                                    @if ($thumb)
                                        <img src="{{ $thumb }}" alt="{{ $post['title'] }}"
                                            class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    @else
                                        <div
                                            class="aspect-video w-full bg-gradient-to-br from-slate-500/10 to-slate-900/20">
                                        </div>
                                    @endif
                                    <div class="absolute left-3 top-3">
                                        <x-posts.status-badge :status="$post['status']" />
                                    </div>
                                </div>
                            </a>

                            <div class="p-4">
                                <h3 class="line-clamp-2 text-[15px] font-bold leading-snug text-gray-900">
                                    <a href="{{ route('posts.show', $post['slug']) }}" class="hover:text-[#f05123]">
                                        {{ $post['title'] }}
                                    </a>
                                </h3>
                                <p class="mt-2 line-clamp-2 text-sm text-gray-600">{{ $post['description'] }}</p>

                                @if ($post['status'] === \App\Models\Post::STATUS_REJECTED && !empty($post['reject_reason']))
                                    <div class="mt-3 rounded-xl border border-red-500/20 bg-red-500/5 p-3">
                                        <p class="text-xs font-bold text-red-500">Lý do từ chối</p>
                                        <p class="mt-1 text-xs text-red-700 line-clamp-3">{{ $post['reject_reason'] }}</p>
                                    </div>
                                @endif

                                <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500">
                                    <span class="font-medium text-gray-700">
                                        {{ optional($post['created_at'])->format('d/m/Y') }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <span>{{ $post['reading_time'] }} phút đọc</span>
                                        <span>•</span>
                                        <span>{{ number_format((int) $post['views_count'], 0, ',', '.') }} lượt xem</span>
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('posts.show', $post['slug']) }}"
                                            class="rounded-xl bg-[#f5f5f5] px-3 py-2 text-xs font-semibold text-[#242424] hover:bg-[#ebebeb]">
                                            Xem
                                        </a>

                                        @if ($post['status'] === \App\Models\Post::STATUS_DRAFT)
                                            <a href="{{ route('posts.edit', $post['id']) }}"
                                                class="rounded-xl bg-[#f5f5f5] px-3 py-2 text-xs font-semibold text-[#242424] hover:bg-[#ebebeb]">
                                                Tiếp tục viết
                                            </a>
                                        @endif

                                        @if ($post['status'] === \App\Models\Post::STATUS_REJECTED)
                                            <form method="POST" action="{{ route('my-posts.resubmit', $post['id']) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="rounded-xl bg-[#f05123] px-3 py-2 text-xs font-semibold text-white hover:bg-[#d8481f]">
                                                    Gửi duyệt lại
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('my-posts.destroy', $post['id']) }}"
                                        onsubmit="return confirm('Xóa bài viết này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-xl bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-500/15">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $posts->links('components.pagination.minimal') }}
                </div>
            @endif
        </div>
    </main>
@endsection
