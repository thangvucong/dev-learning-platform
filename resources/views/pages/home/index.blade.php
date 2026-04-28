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
                    @foreach ($homeData['courses'] as $course)
                        <div
                            class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                            <a href="{{ url('/courses/' . $course['slug']) }}" class="block">
                                <div class="relative overflow-hidden"><img alt="{{ $course['title'] }}"
                                        class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        src="{{ $course['thumbnail_url'] ?? 'https://files.f8.edu.vn/f8-prod/courses/15/62f13d2424a47.png' }}">
                                </div>
                            </a>
                            <div class="p-4">
                                <h3
                                    class="mb-2 text-[15px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                    <a href="{{ url('/courses/' . $course['slug']) }}" class="block">
                                        {{ $course['title'] }}
                                    </a>
                                </h3>
                                <div class="mb-3 flex items-center gap-2">
                                    @if (!empty($course['old_price']))
                                        <span
                                            class="text-sm text-gray-400 line-through">{{ format_price($course['old_price'], $course['currency_symbol'] ?? 'đ') }}</span>
                                    @endif
                                    <span
                                        class="text-base font-bold text-[#f05123]">{{ format_price($course['price'] ?? 0, $course['currency_symbol'] ?? 'đ') }}</span>
                                </div>
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <div class="flex min-w-0 items-center gap-2"><img
                                            alt="{{ $course['user']['name'] ?? 'Unknown Teacher' }}"
                                            class="h-6 w-6 rounded-full object-cover"
                                            src="{{ $course['user']['avatar_url'] ?? 'https://files.f8.edu.vn/f8-prod/avatars/699286a5e7330.png' }}"><span
                                            class="truncate text-sm font-medium text-gray-600">{{ $course['user']['name'] ?? 'Unknown Teacher' }}</span>
                                    </div>
                                    <span class="whitespace-nowrap text-xs text-gray-500">
                                        <span class="font-medium text-gray-700">Khai giảng:</span>
                                        {{ $course['next_opening_at'] ? \Carbon\Carbon::parse($course['next_opening_at'])->format('d/m/Y') : 'Chưa có lịch mở lớp' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            {{-- <section class="mb-12">
                <div class="mb-2 flex items-center gap-2"><img alt="student"
                        class="h-8 w-8 rounded-full object-cover border-2 border-white shadow-sm -ml-0"
                        src="https://files.f8.edu.vn/f8-prod/avatars/69ec83929a4d7.png"><img alt="student"
                        class="h-8 w-8 rounded-full object-cover border-2 border-white shadow-sm -ml-2"
                        src="https://files.f8.edu.vn/f8-prod/avatars/69ea59dbd0f0a.png"><img alt="student"
                        class="h-8 w-8 rounded-full object-cover border-2 border-white shadow-sm -ml-2"
                        src="https://files.f8.edu.vn/f8-prod/avatars/69ea14750b7fd.png">
                    <p class="ml-1 text-sm text-gray-600"><strong class="text-gray-900">+466.283</strong> học viên đã tham
                        gia</p>
                </div>
                <div class="mt-4 mb-6 flex items-end justify-between">
                    <h2 class="text-2xl font-black text-gray-900">Khóa học Free</h2><button
                        class="flex items-center gap-1 text-sm font-semibold text-[#f05123] hover:underline"><span>Xem lộ
                            trình</span> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4" aria-hidden="true">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="Kiến Thức Nhập Môn IT"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/7.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                Kiến Thức Nhập Môn IT</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>139.231</span><span>·</span><span>9 bài</span><span>·</span><span>3h12p</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="Lập trình C++ cơ bản, nâng cao"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/21/63e1bcbaed1dd.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                Lập trình C++ cơ bản, nâng cao</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>39.673</span><span>·</span><span>55 bài</span><span>·</span><span>10h18p</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="HTML CSS từ Zero đến Hero"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/2.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                HTML CSS từ Zero đến Hero</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>219.282</span><span>·</span><span>117 bài</span><span>·</span><span>29h5p</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="Responsive Với Grid System"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/3.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                Responsive Với Grid System</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>48.575</span><span>·</span><span>34 bài</span><span>·</span><span>6h31p</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="Lập Trình JavaScript Cơ Bản"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/1.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                Lập Trình JavaScript Cơ Bản</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>154.986</span><span>·</span><span>112
                                    bài</span><span>·</span><span>24h15p</span></div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="Lập Trình JavaScript Nâng Cao"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/12.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                Lập Trình JavaScript Nâng Cao</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>42.565</span><span>·</span><span>19 bài</span><span>·</span><span>8h41p</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="Xây Dựng Website với ReactJS"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/13/13.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                Xây Dựng Website với ReactJS</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>80.784</span><span>·</span><span>112 bài</span><span>·</span><span>27h32p</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="Node &amp; ExpressJS"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/6.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                Node &amp; ExpressJS</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>50.454</span><span>·</span><span>36 bài</span><span>·</span><span>12h8p</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="App &quot;Đừng Chạm Tay Lên Mặt&quot;"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/4/61a9e9e701506.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                App "Đừng Chạm Tay Lên Mặt"</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>11.440</span><span>·</span><span>13 bài</span><span>·</span><span>1h34p</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="relative overflow-hidden"><img alt="Làm việc với Terminal &amp; Ubuntu"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://files.f8.edu.vn/f8-prod/courses/14/624faac11d109.png"></div>
                        <div class="p-4">
                            <h3 class="mb-1 text-[14px] font-bold text-gray-900 group-hover:text-[#f05123] line-clamp-2">
                                Làm việc với Terminal &amp; Ubuntu</h3>
                            <p class="mb-2 text-sm font-semibold text-green-500">Miễn phí</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg><span>22.000</span><span>·</span><span>28 bài</span><span>·</span><span>4h59p</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section> --}}
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
                    @foreach ($homeData['posts'] as $post)
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
            {{-- <section class="mb-12">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-black text-gray-900">Videos nổi bật</h2><a
                        href="https://www.youtube.com/c/F8VNOfficial" target="_blank" rel="noreferrer"
                        class="flex items-center gap-1 text-sm font-semibold text-[#f05123] hover:underline"><span>Xem tất
                            cả</span> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4"
                            aria-hidden="true">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg></a>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                    <div
                        class="group cursor-pointer rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                        <div class="relative overflow-hidden"><img alt="Bạn sẽ làm được gì sau khóa học?"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://i.ytimg.com/vi/R6plN3FvzFY/maxresdefault.jpg">
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-play h-5 w-5 text-gray-900 ml-1" aria-hidden="true">
                                        <path
                                            d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z">
                                        </path>
                                    </svg>
                                </div>
                            </div><span
                                class="absolute bottom-2 right-2 rounded bg-black/80 px-1.5 py-0.5 text-[11px] font-bold text-white">03:15</span>
                        </div>
                        <div class="p-3">
                            <h3
                                class="mb-2 text-[13px] font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#f05123]">
                                Bạn sẽ làm được gì sau khóa học?</h3>
                            <div class="flex items-center gap-3 text-[12px] text-gray-500">
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg><span>1.141.328</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-thumbs-up h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path d="M7 10v12"></path>
                                        <path
                                            d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z">
                                        </path>
                                    </svg><span>6.702</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-message-circle h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path
                                            d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719">
                                        </path>
                                    </svg><span>149</span></div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                        <div class="relative overflow-hidden"><img
                                alt="Sinh viên IT đi thực tập tại doanh nghiệp cần biết những gì?"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://i.ytimg.com/vi/YH-E4Y3EaT4/maxresdefault.jpg">
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-play h-5 w-5 text-gray-900 ml-1" aria-hidden="true">
                                        <path
                                            d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z">
                                        </path>
                                    </svg>
                                </div>
                            </div><span
                                class="absolute bottom-2 right-2 rounded bg-black/80 px-1.5 py-0.5 text-[11px] font-bold text-white">34:51</span>
                        </div>
                        <div class="p-3">
                            <h3
                                class="mb-2 text-[13px] font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#f05123]">
                                Sinh viên IT đi thực tập tại doanh nghiệp cần biết những gì?</h3>
                            <div class="flex items-center gap-3 text-[12px] text-gray-500">
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg><span>265.299</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-thumbs-up h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path d="M7 10v12"></path>
                                        <path
                                            d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z">
                                        </path>
                                    </svg><span>6.447</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-message-circle h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path
                                            d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719">
                                        </path>
                                    </svg><span>233</span></div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                        <div class="relative overflow-hidden"><img alt="Phương pháp học lập trình của Admin F8?"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://i.ytimg.com/vi/DpvYHLUiZpc/maxresdefault.jpg">
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-play h-5 w-5 text-gray-900 ml-1" aria-hidden="true">
                                        <path
                                            d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z">
                                        </path>
                                    </svg>
                                </div>
                            </div><span
                                class="absolute bottom-2 right-2 rounded bg-black/80 px-1.5 py-0.5 text-[11px] font-bold text-white">24:06</span>
                        </div>
                        <div class="p-3">
                            <h3
                                class="mb-2 text-[13px] font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#f05123]">
                                Phương pháp học lập trình của Admin F8?</h3>
                            <div class="flex items-center gap-3 text-[12px] text-gray-500">
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg><span>132.000</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-thumbs-up h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path d="M7 10v12"></path>
                                        <path
                                            d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z">
                                        </path>
                                    </svg><span>6.187</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-message-circle h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path
                                            d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719">
                                        </path>
                                    </svg><span>336</span></div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                        <div class="relative overflow-hidden"><img
                                alt="&quot;Code Thiếu Nhi Battle&quot; Tranh Giành Trà Sữa Size L"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://i.ytimg.com/vi/sgq7BH6WxL8/maxresdefault.jpg">
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-play h-5 w-5 text-gray-900 ml-1" aria-hidden="true">
                                        <path
                                            d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z">
                                        </path>
                                    </svg>
                                </div>
                            </div><span
                                class="absolute bottom-2 right-2 rounded bg-black/80 px-1.5 py-0.5 text-[11px] font-bold text-white">25:10</span>
                        </div>
                        <div class="p-3">
                            <h3
                                class="mb-2 text-[13px] font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#f05123]">
                                "Code Thiếu Nhi Battle" Tranh Giành Trà Sữa Size L</h3>
                            <div class="flex items-center gap-3 text-[12px] text-gray-500">
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg><span>282.777</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-thumbs-up h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path d="M7 10v12"></path>
                                        <path
                                            d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z">
                                        </path>
                                    </svg><span>5.661</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-message-circle h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path
                                            d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719">
                                        </path>
                                    </svg><span>180</span></div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                        <div class="relative overflow-hidden"><img alt="ReactJS là gì? Tại sao nên học ReactJS?"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://i.ytimg.com/vi/x0fSBAgBrOQ/maxresdefault.jpg">
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-play h-5 w-5 text-gray-900 ml-1" aria-hidden="true">
                                        <path
                                            d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z">
                                        </path>
                                    </svg>
                                </div>
                            </div><span
                                class="absolute bottom-2 right-2 rounded bg-black/80 px-1.5 py-0.5 text-[11px] font-bold text-white">10:41</span>
                        </div>
                        <div class="p-3">
                            <h3
                                class="mb-2 text-[13px] font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#f05123]">
                                ReactJS là gì? Tại sao nên học ReactJS?</h3>
                            <div class="flex items-center gap-3 text-[12px] text-gray-500">
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg><span>526.308</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-thumbs-up h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path d="M7 10v12"></path>
                                        <path
                                            d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z">
                                        </path>
                                    </svg><span>3.944</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-message-circle h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path
                                            d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719">
                                        </path>
                                    </svg><span>348</span></div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                        <div class="relative overflow-hidden"><img alt="Các thẻ HTML thông dụng"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://i.ytimg.com/vi/AzmdwZ6e_aM/maxresdefault.jpg">
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-play h-5 w-5 text-gray-900 ml-1" aria-hidden="true">
                                        <path
                                            d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z">
                                        </path>
                                    </svg>
                                </div>
                            </div><span
                                class="absolute bottom-2 right-2 rounded bg-black/80 px-1.5 py-0.5 text-[11px] font-bold text-white">11:08</span>
                        </div>
                        <div class="p-3">
                            <h3
                                class="mb-2 text-[13px] font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#f05123]">
                                Các thẻ HTML thông dụng</h3>
                            <div class="flex items-center gap-3 text-[12px] text-gray-500">
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg><span>375.416</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-thumbs-up h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path d="M7 10v12"></path>
                                        <path
                                            d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z">
                                        </path>
                                    </svg><span>3.835</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-message-circle h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path
                                            d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719">
                                        </path>
                                    </svg><span>204</span></div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                        <div class="relative overflow-hidden"><img alt="Pass by value và Pass by reference là gì?"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://i.ytimg.com/vi/mt71kEv6A_4/maxresdefault.jpg">
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-play h-5 w-5 text-gray-900 ml-1" aria-hidden="true">
                                        <path
                                            d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z">
                                        </path>
                                    </svg>
                                </div>
                            </div><span
                                class="absolute bottom-2 right-2 rounded bg-black/80 px-1.5 py-0.5 text-[11px] font-bold text-white">12:22</span>
                        </div>
                        <div class="p-3">
                            <h3
                                class="mb-2 text-[13px] font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#f05123]">
                                Pass by value và Pass by reference là gì?</h3>
                            <div class="flex items-center gap-3 text-[12px] text-gray-500">
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg><span>6.692</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-thumbs-up h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path d="M7 10v12"></path>
                                        <path
                                            d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z">
                                        </path>
                                    </svg><span>174</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-message-circle h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path
                                            d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719">
                                        </path>
                                    </svg><span>30</span></div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="group cursor-pointer rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                        <div class="relative overflow-hidden"><img alt="Bảo mật mạng"
                                class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://i.ytimg.com/vi/zed6xXKuuTQ/maxresdefault.jpg">
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-play h-5 w-5 text-gray-900 ml-1" aria-hidden="true">
                                        <path
                                            d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z">
                                        </path>
                                    </svg>
                                </div>
                            </div><span
                                class="absolute bottom-2 right-2 rounded bg-black/80 px-1.5 py-0.5 text-[11px] font-bold text-white">00:06</span>
                        </div>
                        <div class="p-3">
                            <h3
                                class="mb-2 text-[13px] font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#f05123]">
                                Bảo mật mạng</h3>
                            <div class="flex items-center gap-3 text-[12px] text-gray-500">
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5" aria-hidden="true">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg><span>829</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-thumbs-up h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path d="M7 10v12"></path>
                                        <path
                                            d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z">
                                        </path>
                                    </svg><span>3</span></div>
                                <div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-message-circle h-3.5 w-3.5"
                                        aria-hidden="true">
                                        <path
                                            d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719">
                                        </path>
                                    </svg><span>0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> --}}
        </div>
    </main>
@endsection
