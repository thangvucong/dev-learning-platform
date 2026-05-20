@extends('layouts.app')

@section('title', 'Bài viết nổi bật')

@php
    $topics = [
        'Front-end / Mobile apps',
        'Back-end / Devops',
        'AI / LLM',
        'Tester / Testing',
        'UI / UX / Design',
        'Others',
    ];

    $posts = $posts ?? collect();
@endphp

@section('content')
    <main class="flex-1 lg:ml-24">
        <div class="mx-auto w-full max-w-[1420px] px-5 py-8 sm:px-8 lg:px-10 xl:px-12">
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-[minmax(0,1fr)_320px] xl:gap-16">
                <section class="min-w-0">
                    <div class="mb-9">
                        <h1 class="text-[28px] font-black leading-tight text-[#292929] sm:text-[32px]">
                            Bài viết nổi bật
                        </h1>
                    </div>

                    <div class="space-y-5">
                        @foreach ($posts as $post)
                            @php
                                $authorName = optional($post->user)->name ?: 'Tác giả';
                                $authorAvatar = media_url(
                                    optional($post->user)->avatar_url,
                                    'https://ui-avatars.com/api/?name=' . urlencode($authorName),
                                );
                                $thumbnail = media_url(
                                    $post->thumbnail ?: $post->image,
                                    asset('images/default-post.png'),
                                );
                                $viewCount = (int) ($post->views_count ?? 0);
                            @endphp
                            <article
                                class="group rounded-2xl border border-[#e1e4e8] bg-white px-5 py-4 transition-colors hover:border-[#cfd4dc]">
                                <div class="mb-4 flex items-start justify-between gap-4">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <img src="{{ $authorAvatar }}" alt="{{ $authorName }}"
                                            class="h-7 w-7 shrink-0 rounded-full border border-[#e5e7eb] object-cover">
                                        <span class="truncate text-[13px] font-bold text-[#292929]">
                                            {{ $authorName }}
                                        </span>
                                        @if (auth()->user()->isAdmin())
                                            <span
                                                class="inline-flex h-3.5 w-3.5 shrink-0 items-center justify-center rounded-full bg-[#1d9bf0] text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.32a1 1 0 0 1-1.42.002L3.29 9.246a1 1 0 1 1 1.42-1.41l4.04 4.067 6.54-6.607a1 1 0 0 1 1.414-.006Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col gap-5 md:flex-row md:items-center">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('posts.show', $post->slug) }}" class="block">
                                            <h2
                                                class="text-[20px] font-black leading-snug text-[#292929] transition-colors group-hover:text-[#f05123]">
                                                {{ $post->title }}
                                            </h2>
                                        </a>
                                        <p class="mt-2 line-clamp-2 text-[15px] leading-relaxed text-[#52525b]">
                                            {{ $post->description ?: \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 150) }}
                                        </p>

                                        <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-2 text-[13px]">
                                            <span class="text-[#292929]">{{ time_ago($post->created_at) }}</span>
                                            <span class="text-[#d1d5db]">·</span>
                                            <span class="text-[#292929]">
                                                {{ number_format($viewCount, 0, ',', '.') }} lượt xem
                                            </span>
                                        </div>
                                    </div>

                                    <a href="{{ route('posts.show', $post->slug) }}"
                                        class="block h-[112px] w-full shrink-0 overflow-hidden rounded-xl bg-[#f1f2f4] md:w-[200px]">
                                        <img src="{{ $thumbnail }}" alt="{{ $post->title }}"
                                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    </a>
                                </div>
                            </article>
                        @endforeach

                        @if ($posts->isEmpty())
                            <div class="rounded-2xl border border-dashed border-[#d1d5db] bg-white px-6 py-12 text-center">
                                <p class="text-[16px] font-bold text-[#292929]">Chưa có bài viết nổi bật</p>
                                <p class="mt-2 text-[14px] text-[#6b7280]">Các bài viết đã xuất bản sẽ hiển thị tại đây.</p>
                            </div>
                        @endif
                    </div>
                </section>

                <aside class="hidden min-w-0 lg:block">
                    <div class="sticky top-[98px] space-y-8">
                        <section>
                            <h2 class="mb-5 text-[14px] font-bold uppercase tracking-wide text-[#6b7280]">
                                Xem các bài viết theo chủ đề
                            </h2>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($topics as $topic)
                                    <a href="#"
                                        class="rounded-full bg-[#f1f1f1] px-4 py-2 text-[14px] font-semibold text-[#3f3f46] transition-colors hover:bg-[#e5e7eb]">
                                        {{ $topic }}
                                    </a>
                                @endforeach
                            </div>
                        </section>

                        <a href="#"
                            class="block overflow-hidden rounded-[8px] bg-gradient-to-r from-[#05a3d6] to-[#dff6ff] shadow-sm">
                            <div class="flex min-h-[210px] items-center justify-between gap-3 p-5">
                                <div class="max-w-[170px] text-white">
                                    <p class="text-[18px] leading-tight">Khóa học</p>
                                    <h3 class="mt-1 text-[25px] font-black leading-none">HTML CSS Pro</h3>
                                    <ul class="mt-4 space-y-2 text-[13px] font-semibold">
                                        <li>✓ Chuyên sâu hơn khóa Free</li>
                                        <li>✓ Thực hành 8 dự án</li>
                                        <li>✓ Tặng dạng Flashcards</li>
                                    </ul>
                                    <span
                                        class="mt-4 inline-flex rounded-full bg-[#075da7] px-4 py-2 text-[13px] font-bold">
                                        Tìm hiểu thêm
                                    </span>
                                </div>
                                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=220&q=80"
                                    alt="HTML CSS Pro" class="h-[190px] w-[118px] rounded-xl object-cover object-top">
                            </div>
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </main>
@endsection
