@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <main class="flex-1 lg:ml-24">
        <div class="mx-auto w-full p-4 lg:p-8">
            <x-home.hero-slider :items="$bannerCourses" />
            <section class="mb-12">
                <div class="mb-6 flex items-center gap-3">
                    <h2 class="text-2xl font-black text-gray-900">Khóa học nổi bật</h2>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($courses as $course)
                        <div
                            class="group h-full cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1 flex flex-col">
                            <a href="{{ route('courses.show', $course['slug']) }}" class="block">
                                <div class="relative overflow-hidden">
                                    <img alt="{{ $course['title'] }}"
                                        class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        src="{{ !empty($course['thumbnail_url'])
                                            ? $course['thumbnail_url']
                                            : 'https://files.f8.edu.vn/f8-prod/courses/15/62f13d2424a47.png' }}" />
                                </div>
                            </a>
                            <div class="p-4 flex flex-col flex-1">
                                <h3
                                    class="mb-2 h-[2.8rem] text-[15px] leading-snug font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                    <a href="{{ route('courses.show', $course['slug']) }}" class="block">
                                        {{ $course['title'] }}
                                    </a>
                                </h3>
                                <div class="mt-auto mb-2 flex items-baseline gap-2">
                                    @if (!empty($course['has_discount']))
                                        <span class="text-sm text-gray-500"
                                            style="text-decoration: line-through; text-decoration-thickness: 1px;">
                                            {{ format_price($course['original_price'] ?? 0) }}
                                        </span>
                                    @endif
                                    <span class="text-base font-bold text-[#f05123]">
                                        {{ format_price($course['sale_price'] ?? ($course['original_price'] ?? 0)) }}
                                    </span>
                                </div>
                                @php
                                    $ratingAvg = (float) ($course['rating_avg'] ?? 0);
                                    $ratingCount = (int) ($course['rating_count'] ?? 0);
                                    $starOutlinePath =
                                        'M288.1-32c9 0 17.3 5.1 21.4 13.1L383 125.3 542.9 150.7c8.9 1.4 16.3 7.7 19.1 16.3s.5 18-5.8 24.4L441.7 305.9 467 465.8c1.4 8.9-2.3 17.9-9.6 23.2s-17 6.1-25 2L288.1 417.6 143.8 491c-8 4.1-17.7 3.3-25-2s-11-14.2-9.6-23.2L134.4 305.9 20 191.4c-6.4-6.4-8.6-15.8-5.8-24.4s10.1-14.9 19.1-16.3l159.9-25.4 73.6-144.2c4.1-8 12.4-13.1 21.4-13.1zm0 76.8L230.3 158c-3.5 6.8-10 11.6-17.6 12.8l-125.5 20 89.8 89.9c5.4 5.4 7.9 13.1 6.7 20.7l-19.8 125.5 113.3-57.6c6.8-3.5 14.9-3.5 21.8 0l113.3 57.6-19.8-125.5c-1.2-7.6 1.3-15.3 6.7-20.7l89.8-89.9-125.5-20c-7.6-1.2-14.1-6-17.6-12.8L288.1 44.8z';
                                    $starFillPath =
                                        'M309.5-18.9c-4.1-8-12.4-13.1-21.4-13.1s-17.3 5.1-21.4 13.1L193.1 125.3 33.2 150.7c-8.9 1.4-16.3 7.7-19.1 16.3s-.5 18 5.8 24.4l114.4 114.5-25.2 159.9c-1.4 8.9 2.3 17.9 9.6 23.2s16.9 6.1 25 2L288.1 417.6 432.4 491c8 4.1 17.7 3.3 25-2s11-14.2 9.6-23.2L441.7 305.9 556.1 191.4c6.4-6.4 8.6-15.8 5.8-24.4s-10.1-14.9-19.1-16.3L383 125.3 309.5-18.9z';
                                @endphp
                                <div class="mb-2 flex items-center gap-2 text-sm"
                                    title="Đánh giá chất lượng: {{ number_format($ratingAvg, 1) }}/5">
                                    <span class="inline-flex items-center gap-[2px]"
                                        aria-label="Đánh giá {{ number_format($ratingAvg, 1) }}/5 sao">
                                        @for ($star = 1; $star <= 5; $star++)
                                            @php
                                                $fillPercent = max(0, min(100, ($ratingAvg - ($star - 1)) * 100));
                                            @endphp
                                            <span class="relative inline-flex h-4 w-4">
                                                <svg class="h-4 w-4" role="img" viewBox="0 0 576 512"
                                                    aria-hidden="true">
                                                    <path fill="#d1d5db" d="{{ $starOutlinePath }}"></path>
                                                </svg>
                                                <span class="absolute inset-0 block overflow-hidden"
                                                    style="width: {{ $fillPercent }}%; color: #f6c343;">
                                                    <svg class="h-4 w-4" style="min-width: 1rem;" role="img"
                                                        viewBox="0 0 576 512" aria-hidden="true">
                                                        <path fill="currentColor" d="{{ $starFillPath }}"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        @endfor
                                    </span>
                                    <span class="font-medium text-gray-700">
                                        {{ number_format($ratingAvg, 1) }}
                                    </span>
                                    <span class="text-gray-500">
                                        ({{ number_format($ratingCount, 0, ',', '.') }})
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-3 min-h-[1.75rem]">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <img alt="{{ $course['instructor_name'] ?? 'Giảng viên' }}"
                                            class="h-6 w-6 rounded-full object-cover"
                                            src="{{ $course['instructor_avatar_url'] ?? 'https://files.f8.edu.vn/f8-prod/avatars/699286a5e7330.png' }}">
                                        <span
                                            class="truncate text-sm font-medium text-gray-600">{{ $course['instructor_name'] ?? 'Giảng viên' }}
                                        </span>
                                    </div>
                                    <span class="whitespace-nowrap text-xs text-gray-500">
                                        <span class="font-medium text-gray-700">Khai giảng:</span>
                                        {{ optional($course['next_class_start_at'] ?? null)->format('d/m/Y') ?? 'Chưa có lịch mở lớp' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="mb-12">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-black text-gray-900">Bài viết nổi bật</h2>
                    <a href="{{ route('posts.index') }}"
                        class="flex items-center gap-1 text-sm font-semibold text-[#f05123] hover:underline">
                        <span>Xem tất cả</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4" aria-hidden="true">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($posts as $post)
                        <div
                            class="group h-full cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1 flex flex-col">
                            <a href="{{ route('posts.show', $post['slug']) }}" class="block">
                                <div class="relative overflow-hidden">
                                    <img alt="{{ $post['title'] }}"
                                        class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        src="{{ !empty($post['thumbnail']) ? Storage::url($post['thumbnail']) : asset('images/default-post.png') }}" />
                                </div>
                            </a>
                            <div class="p-4 flex flex-col flex-1">
                                <h3
                                    class="mb-2 min-h-[2.8rem] text-[15px] leading-snug font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                    <a href="{{ route('posts.show', $post['slug']) }}" class="block">
                                        {{ $post['title'] }}
                                    </a>
                                </h3>
                                <div class="mb-2 text-sm font-medium text-gray-500">
                                    {{ number_format((int) ($post['views_count'] ?? 0), 0, ',', '.') }} lượt xem
                                </div>
                                <div class="flex items-center justify-between gap-3 min-h-[1.75rem]">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <img alt="{{ $post['author_name'] ?? 'Tác giả' }}"
                                            class="h-6 w-6 rounded-full object-cover"
                                            src="{{ $post['author_avatar_url'] ?? 'https://files.f8.edu.vn/f8-prod/avatars/699286a5e7330.png' }}">
                                        <span
                                            class="truncate text-sm font-medium text-gray-600">{{ $post['author_name'] ?? 'Tác giả' }}
                                        </span>
                                    </div>
                                    <span class="whitespace-nowrap text-xs text-gray-500">
                                        <span class="font-medium text-gray-700">Ngày đăng:</span>
                                        {{ optional($post['created_at'] ?? null)->format('d/m/Y') ?? 'Chưa có ngày đăng' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>
    </main>
@endsection
