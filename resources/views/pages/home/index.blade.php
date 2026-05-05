@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <main class="flex-1 lg:ml-24">
        <div class="mx-auto w-full p-4 lg:p-8">
            <div class="mb-10 overflow-hidden rounded-3xl">
                <div class="relative">
                    <div class="rounded-3xl bg-gradient-to-r from-[#fe5f2a] to-[#ff9820] p-8 text-white lg:p-12">
                        <h2 class="mb-4 text-3xl font-bold lg:text-4xl">Lớp Fullstack qua Zoom</h2>
                        <p class="mb-6 max-w-lg text-base leading-relaxed opacity-90">Học online trực tiếp qua Zoom, phù hợp
                            nếu bạn muốn được review code, chấm bài trực tiếp bởi giảng viên và trợ giảng giàu kinh nghiệm.
                        </p><button
                            class="rounded-full border-2 border-white bg-transparent px-6 py-2 text-sm font-bold uppercase transition hover:bg-white hover:text-[#f05123]">NHẬN
                            LỘ TRÌNH FULLSTACK</button>
                    </div>
                    <div class="mt-3 flex justify-center gap-1.5"><span
                            class="h-1.5 rounded-full transition-all w-6 bg-[#f05123]"></span><span
                            class="h-1.5 rounded-full transition-all w-1.5 bg-gray-300"></span><span
                            class="h-1.5 rounded-full transition-all w-1.5 bg-gray-300"></span><span
                            class="h-1.5 rounded-full transition-all w-1.5 bg-gray-300"></span><span
                            class="h-1.5 rounded-full transition-all w-1.5 bg-gray-300"></span><span
                            class="h-1.5 rounded-full transition-all w-1.5 bg-gray-300"></span><span
                            class="h-1.5 rounded-full transition-all w-1.5 bg-gray-300"></span></div>
                </div>
            </div>
            <section class="mb-12">
                <div class="mb-6 flex items-center gap-3">
                    <h2 class="text-2xl font-black text-gray-900">Khóa học Pro</h2><span
                        class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-600">MỚI</span>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($courses as $course)
                        <div
                            class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                            <a href="{{ route('courses.show', $course['slug']) }}" class="block">
                                <div class="relative overflow-hidden">
                                    <img alt="{{ $course['title'] }}"
                                        class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        src="{{ !empty($course['thumbnail_url'])
                                            ? $course['thumbnail_url']
                                            : 'https://files.f8.edu.vn/f8-prod/courses/15/62f13d2424a47.png' }}" />
                                </div>
                            </a>
                            <div class="p-4">
                                <h3
                                    class="mb-2 text-[15px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                    <a href="{{ route('courses.show', $course['slug']) }}" class="block">
                                        {{ $course['title'] }}
                                    </a>
                                </h3>
                                <div class="mb-3 flex items-center gap-2">
                                    <span
                                        class="text-base font-bold text-[#f05123]">{{ format_price($course['price'] ?? 0, $course['currency_symbol'] ?? 'đ') }}
                                    </span>
                                </div>
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <img alt="{{ $course->instructor->name ?? 'Unknown Teacher' }}"
                                            class="h-6 w-6 rounded-full object-cover"
                                            src="{{ $course->instructor->avatar_url ?? 'https://files.f8.edu.vn/f8-prod/avatars/699286a5e7330.png' }}">
                                        <span
                                            class="truncate text-sm font-medium text-gray-600">{{ $course->instructor->name ?? 'Unknown Teacher' }}
                                        </span>
                                    </div>
                                    <span class="whitespace-nowrap text-xs text-gray-500">
                                        <span class="font-medium text-gray-700">Khai giảng:</span>
                                        {{ optional(optional($course->classes->first())->start_at)->format('d/m/Y') ?? 'Chưa có lịch mở lớp' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="mb-12">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-black text-gray-900">Bài viết nổi bật</h2><button
                        class="flex items-center gap-1 text-sm font-semibold text-[#f05123] hover:underline"><span>Xem tất
                            cả</span> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4" aria-hidden="true">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg></button>
                </div>
                <div class="mb-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($posts as $post)
                        <div
                            class="group h-full cursor-pointer rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden transition-all hover:shadow-md hover:-translate-y-0.5 flex flex-col">
                            <div class="overflow-hidden"><img alt="{{ $post['title'] }}"
                                    class="aspect-[4/3] w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    src="{{ $post['thumbnail'] ?? 'https://files.f8.edu.vn/f8-prod/blog_posts/65/6139fe28a9844.png' }}">
                            </div>
                            <div class="p-3 flex flex-col flex-1">
                                <h3
                                    class="mb-2 min-h-[2.25rem] text-[13px] font-bold text-gray-900 line-clamp-2 group-hover:text-[#f05123] leading-snug">
                                    {{ $post['title'] }}</h3>
                                <div class="mt-auto flex items-center gap-1.5">
                                    <img alt="{{ $post['user']['name'] ?? 'Unknown Author' }}"
                                        class="h-5 w-5 rounded-full object-cover flex-shrink-0"
                                        src="{{ $post['user']['avatar_url'] ?? 'https://www.gravatar.com/avatar/?d=mp' }}"><span
                                        class="text-[12px] text-gray-600 font-medium truncate">{{ $post['user']['name'] ?? 'Unknown Author' }}</span><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-3 w-3 text-blue-500 flex-shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>
                                    <span class="text-[11px] text-gray-400 ml-auto whitespace-nowrap">·
                                        {{ time_ago($post['published_at']) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>
    </main>
@endsection
