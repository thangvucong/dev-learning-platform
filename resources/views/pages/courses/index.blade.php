@extends('layouts.app')

@section('title', 'Courses')

@push('styles')
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
    <style>
        /* Toast UI Viewer light theme tuning (public course detail) */
        [data-markdown-viewer] .toastui-editor-contents {
            color: #242424;
        }

        [data-markdown-viewer] .toastui-editor-contents p,
        [data-markdown-viewer] .toastui-editor-contents li {
            color: #444;
            line-height: 1.7;
            font-size: 15px;
        }

        [data-markdown-viewer] .toastui-editor-contents h1,
        [data-markdown-viewer] .toastui-editor-contents h2,
        [data-markdown-viewer] .toastui-editor-contents h3,
        [data-markdown-viewer] .toastui-editor-contents h4 {
            color: #242424;
            font-weight: 800;
        }

        [data-markdown-viewer] .toastui-editor-contents a {
            color: #f05123;
            text-decoration: none;
        }

        [data-markdown-viewer] .toastui-editor-contents a:hover {
            text-decoration: underline;
        }

        [data-markdown-viewer] .toastui-editor-contents blockquote {
            border-left-color: rgba(240, 81, 35, 0.45);
            background: rgba(240, 81, 35, 0.06);
            color: #444;
        }

        [data-markdown-viewer] .toastui-editor-contents code {
            background: rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            padding: 0.1rem 0.35rem;
        }

        [data-markdown-viewer] .toastui-editor-contents pre {
            background: #0b1020;
            border-radius: 10px;
            padding: 14px;
            overflow: auto;
        }

        [data-markdown-viewer] .toastui-editor-contents pre code {
            background: transparent;
            border: 0;
            padding: 0;
            color: #e5e7eb;
        }

        [data-markdown-viewer] .toastui-editor-contents img {
            border-radius: 12px;
        }
    </style>
@endpush

@section('content')
    <div class="ml-[96px] flex-1 pr-8 pl-[10px]">
        <div class="mx-auto px-11 mt-6 pb-16 grid grid-cols-1 xl:grid-cols-12 gap-8 xl:gap-10 items-start">
            <div class="min-w-0 xl:col-span-8">
                <h1 class="text-[32px] font-bold text-[#242424] leading-[1.4] mb-4">
                    {{ $course['title'] }}
                </h1>
                <div class="mb-4 flex items-center gap-3 rounded-lg border border-[#ebebeb] bg-white px-4 py-3">
                    <img alt="{{ $instructor['name'] ?? 'Unknown Teacher' }}" class="h-11 w-11 rounded-full object-cover"
                        src="{{ $instructor['avatar_url'] ?? 'https://files.f8.edu.vn/f8-prod/avatars/699286a5e7330.png' }}">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#666]">Giảng viên</p>
                        <p class="truncate text-[15px] font-semibold text-[#242424]">
                            {{ $instructor['name'] ?? 'Chưa cập nhật' }}
                        </p>
                        <p class="truncate text-sm text-[#666]">
                            {{ $instructor['email'] ?? 'Chưa có email hiển thị' }}
                        </p>
                    </div>
                </div>
                <p class="text-[15px] text-[#444] leading-[1.6] mb-4 max-w-[700px]">
                    {{ $course['description'] }}
                </p>
                <div class="mb-8 flex flex-wrap items-center gap-3 text-sm">
                    @php
                        $ratingAvg = (float) ($course['rating_avg'] ?? 0);
                        $ratingCount = (int) ($course['rating_count'] ?? 0);
                        $starOutlinePath =
                            'M288.1-32c9 0 17.3 5.1 21.4 13.1L383 125.3 542.9 150.7c8.9 1.4 16.3 7.7 19.1 16.3s.5 18-5.8 24.4L441.7 305.9 467 465.8c1.4 8.9-2.3 17.9-9.6 23.2s-17 6.1-25 2L288.1 417.6 143.8 491c-8 4.1-17.7 3.3-25-2s-11-14.2-9.6-23.2L134.4 305.9 20 191.4c-6.4-6.4-8.6-15.8-5.8-24.4s10.1-14.9 19.1-16.3l159.9-25.4 73.6-144.2c4.1-8 12.4-13.1 21.4-13.1zm0 76.8L230.3 158c-3.5 6.8-10 11.6-17.6 12.8l-125.5 20 89.8 89.9c5.4 5.4 7.9 13.1 6.7 20.7l-19.8 125.5 113.3-57.6c6.8-3.5 14.9-3.5 21.8 0l113.3 57.6-19.8-125.5c-1.2-7.6 1.3-15.3 6.7-20.7l89.8-89.9-125.5-20c-7.6-1.2-14.1-6-17.6-12.8L288.1 44.8z';
                        $starFillPath =
                            'M309.5-18.9c-4.1-8-12.4-13.1-21.4-13.1s-17.3 5.1-21.4 13.1L193.1 125.3 33.2 150.7c-8.9 1.4-16.3 7.7-19.1 16.3s-.5 18 5.8 24.4l114.4 114.5-25.2 159.9c-1.4 8.9 2.3 17.9 9.6 23.2s16.9 6.1 25 2L288.1 417.6 432.4 491c8 4.1 17.7 3.3 25-2s11-14.2 9.6-23.2L441.7 305.9 556.1 191.4c6.4-6.4 8.6-15.8 5.8-24.4s-10.1-14.9-19.1-16.3L383 125.3 309.5-18.9z';
                    @endphp
                    <span class="inline-flex items-center gap-[2px]"
                        aria-label="Đánh giá {{ number_format($ratingAvg, 1) }}/5 sao">
                        @for ($star = 1; $star <= 5; $star++)
                            @php
                                $fillPercent = max(0, min(100, ($ratingAvg - ($star - 1)) * 100));
                            @endphp
                            <span class="relative inline-flex h-4 w-4">
                                <svg class="h-4 w-4" role="img" viewBox="0 0 576 512" aria-hidden="true">
                                    <path fill="#d1d5db" d="{{ $starOutlinePath }}"></path>
                                </svg>
                                <span class="absolute inset-0 block overflow-hidden"
                                    style="width: {{ $fillPercent }}%; color: #f6c343;">
                                    <svg class="h-4 w-4" style="min-width: 1rem;" role="img" viewBox="0 0 576 512"
                                        aria-hidden="true">
                                        <path fill="currentColor" d="{{ $starFillPath }}"></path>
                                    </svg>
                                </span>
                            </span>
                        @endfor
                    </span>
                    <span class="font-semibold text-[#242424]">{{ number_format($ratingAvg, 1) }}</span>
                    <span class="text-[#666]">({{ number_format($ratingCount, 0, ',', '.') }} lượt
                        đánh giá)</span>
                </div>

                @if ($courseDetailData['benefits']->isNotEmpty())
                    <section class="mb-10">
                        <h2 class="text-[20px] font-bold text-[#242424] mb-5">Bạn sẽ học được gì?</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2" style="column-gap: 32px; row-gap: 8px;">
                            @foreach ($courseDetailData['benefits'] as $benefit)
                                <div class="flex items-center gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-check text-[#f05123] shrink-0" aria-hidden="true">
                                        <path d="M20 6 9 17l-5-5"></path>
                                    </svg><span
                                        class="text-[15px] text-[#444] leading-[1.5]">{{ $benefit['content'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <div class="mb-6 flex justify-between items-end">
                    <div>
                        <h2 class="text-[20px] font-bold text-[#242424] mb-2">Nội dung khóa học</h2>
                        <div class="flex items-center text-sm text-[#444]">
                            <span
                                class="font-semibold">{{ $courseDetailData['summary']['chapters_count'] }}</span>&nbsp;buổi
                            &nbsp;
                            <span class="font-semibold">{{ $courseDetailData['summary']['lessons_count'] }}</span>&nbsp;bài
                            học
                        </div>
                    </div><button id="toggle-all-tracks" class="text-[#f05123] font-semibold text-sm hover:underline">Mở
                        rộng tất cả</button>
                </div>

                <div class="flex flex-col gap-[10px]">
                    @foreach ($courseDetailData['tracks'] as $index => $track)
                        @php
                            $isOpen = $loop->first;
                        @endphp
                        <div class="border border-[#ebebeb] rounded-lg bg-[#f5f5f5] overflow-hidden">
                            <button
                                class="w-full flex items-center justify-between p-3 lg:p-4 hover:bg-[#ebebeb] transition-colors course-track-toggle"
                                type="button" data-target="track-panel-{{ $track['id'] }}"
                                aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 flex justify-center text-[#f05123]" style="transform: none;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide {{ $isOpen ? 'lucide-minus' : 'lucide-plus' }} track-toggle-icon"
                                            aria-hidden="true">
                                            <path d="M5 12h14"></path>
                                            @if (!$isOpen)
                                                <path d="M12 5v14"></path>
                                            @endif
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-[15px] text-[#333]">
                                        {{ $index + 1 }}. {{ $track['title'] }}
                                    </h3>
                                </div><span class="text-sm text-[#444]">{{ $track['children']->count() }} bài học</span>
                            </button>
                            @if ($track['children']->count())
                                <div id="track-panel-{{ $track['id'] }}"
                                    class="bg-white border-t border-[#ebebeb] {{ $isOpen ? '' : 'hidden' }}">
                                    @foreach ($track['children'] as $child)
                                        <div
                                            class="flex items-center justify-between p-3 lg:py-[14px] lg:px-8 border-b border-[#f5f5f5] hover:bg-[#f5f5f5] cursor-pointer last:border-0 transition-colors">
                                            <div class="flex items-center gap-3"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-circle-play text-[#f05123] shrink-0"
                                                    aria-hidden="true">
                                                    <path
                                                        d="M9 9.003a1 1 0 0 1 1.517-.859l4.997 2.997a1 1 0 0 1 0 1.718l-4.997 2.997A1 1 0 0 1 9 14.996z">
                                                    </path>
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                </svg><span class="text-sm text-[#333] leading-relaxed">
                                                    {{ $index + 1 }}.{{ $loop->iteration }} {{ $child['title'] }}
                                                </span></div>
                                            <span class="text-sm text-[#444] min-w-[45px] text-right">--:--</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if ($courseDetailData['requirements']->isNotEmpty())
                    <section class="mt-10">
                        <h2 class="text-[20px] font-bold text-[#242424] mb-5">Yêu cầu</h2>
                        <ul class="flex flex-col" style="gap: 8px;">
                            @foreach ($courseDetailData['requirements'] as $requirement)
                                <li class="flex items-center gap-3"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-check text-[#f05123] shrink-0"
                                        aria-hidden="true">
                                        <path d="M20 6 9 17l-5-5"></path>
                                    </svg><span
                                        class="text-[15px] text-[#444] leading-[1.5]">{{ $requirement['content'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
                @if ($courseDetailData['targets']->isNotEmpty())
                    <section class="mt-10">
                        <h2 class="text-[20px] font-bold text-[#242424] mb-5">Đối tượng phù hợp</h2>
                        <ul class="flex flex-col" style="gap: 8px;">
                            @foreach ($courseDetailData['targets'] as $target)
                                <li class="flex items-center gap-3"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-check text-[#f05123] shrink-0"
                                        aria-hidden="true">
                                        <path d="M20 6 9 17l-5-5"></path>
                                    </svg><span
                                        class="text-[15px] text-[#444] leading-[1.5]">{{ $target['content'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </div>
            <div class="w-full xl:col-span-4">
                <div class="xl:sticky xl:top-[98px] flex flex-col items-center">
                    @php
                        $courseTitle = $course['title'] ?? 'Giới thiệu khóa học';
                        $thumbnailUrl =
                            $course['thumbnail_url'] ?? 'https://files.f8.edu.vn/f8-prod/courses/15/62f13d2424a47.png';
                        $introVideoUrl = $course['intro_video_url'] ?? null;
                        $youtubeVideoId = null;

                        if (
                            !empty($introVideoUrl) &&
                            preg_match(
                                '~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{11})~',
                                $introVideoUrl,
                                $matches,
                            )
                        ) {
                            $youtubeVideoId = $matches[1];
                        }
                    @endphp
                    <div
                        class="w-full relative rounded-xl overflow-hidden mb-5 group shadow-[0_4px_10px_rgba(0,0,0,0.1)] course-preview-card">
                        <div class="aspect-video relative flex items-center justify-center bg-[#111]">
                            <img src="{{ $thumbnailUrl }}" alt="{{ $courseTitle }}"
                                class="absolute inset-0 h-full w-full object-cover course-preview-thumbnail">
                            <div
                                class="absolute inset-0 bg-black/20 flex flex-col items-center justify-center group-hover:bg-black/40 transition-colors course-preview-overlay">
                                <button type="button"
                                    class="w-16 h-16 rounded-full bg-white/90 flex items-center justify-center mb-4 cursor-pointer scale-100 group-hover:scale-110 transition-transform course-preview-play-button"
                                    aria-label="Phát video giới thiệu">
                                    <svg data-prefix="fas" data-icon="circle-play"
                                        class="svg-inline--fa fa-circle-play CourseDetail-module__icon___smpaJ"
                                        role="img" viewBox="0 0 512 512" aria-hidden="true">
                                        <path fill="currentColor"
                                            d="M0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zM188.3 147.1c-7.6 4.2-12.3 12.3-12.3 20.9l0 176c0 8.7 4.7 16.7 12.3 20.9s16.8 4.1 24.3-.5l144-88c7.1-4.4 11.5-12.1 11.5-20.5s-4.4-16.1-11.5-20.5l-144-88c-7.4-4.5-16.7-4.7-24.3-.5z">
                                        </path>
                                    </svg>
                                </button><span class="text-white font-semibold text-base">Xem giới thiệu khóa
                                    học</span>
                            </div>
                            <div class="absolute inset-0 hidden bg-black course-preview-player">
                                @if ($youtubeVideoId)
                                    <iframe id="course-preview-youtube" class="h-full w-full"
                                        data-src="https://www.youtube.com/embed/{{ $youtubeVideoId }}?autoplay=1&rel=0&modestbranding=1"
                                        src="" title="{{ $courseTitle }}" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                                @elseif ($introVideoUrl)
                                    <video id="course-preview-video" class="h-full w-full" controls playsinline
                                        preload="metadata" src="{{ $introVideoUrl }}"
                                        poster="{{ $thumbnailUrl }}"></video>
                                @endif
                            </div>
                        </div>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 600; line-height: 1.4;">Chi phí khóa học</span>
                    <div class="mb-4 flex flex-col items-center gap-1">
                        @if (!empty($course['has_discount']))
                            <span class="text-base text-[#666] line-through"
                                style="text-decoration: line-through; text-decoration-thickness: 1px;">
                                {{ format_price($course['original_price'] ?? 0, 'đ') }}
                            </span>
                        @endif
                        <h2 class="text-[36px] font-semibold text-[#f05123] leading-tight">
                            {{ format_price($course['sale_price'] ?? ($course['original_price'] ?? 0), 'đ') }}
                        </h2>
                    </div><a href="{{ route('checkout', ['course_id' => $course['id']]) }}"
                        class="w-[200px] bg-[#1473e6] hover:bg-[#105cba] text-white text-center font-semibold py-[10px] px-4 rounded-full mb-6 transition-colors shadow-[0_4px_10px_rgba(20,115,230,0.3)]">ĐĂNG
                        KÝ HỌC</a>
                    <ul class="flex flex-col gap-3 px-2">
                        <li class="flex items-center gap-3 text-[#444] text-[15px]"><svg
                                xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-battery-medium" aria-hidden="true">
                                <path d="M10 14v-4"></path>
                                <path d="M22 14v-4"></path>
                                <path d="M6 14v-4"></path>
                                <rect x="2" y="6" width="16" height="12" rx="2"></rect>
                            </svg><span>{{ data_get($course, 'level.name', 'Trình độ cơ bản') }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-[#444] text-[15px]"><svg
                                xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-film" aria-hidden="true">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M7 3v18"></path>
                                <path d="M3 7.5h4"></path>
                                <path d="M3 12h18"></path>
                                <path d="M3 16.5h4"></path>
                                <path d="M17 3v18"></path>
                                <path d="M17 7.5h4"></path>
                                <path d="M17 16.5h4"></path>
                            </svg><span>Tổng số <strong>{{ $course['lessons_count'] }}</strong> bài
                                học</span></li>
                        <li class="flex items-center gap-3 text-[#444] text-[15px]"><svg
                                xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-clock" aria-hidden="true">
                                <path d="M12 6v6l4 2"></path>
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg><span>Khai giảng
                                <strong>
                                    {{ optional(optional($classes->first())->start_at)->format('d/m/Y') ?? 'Chưa có lịch' }}
                                </strong>
                            </span></li>
                        <li class="flex items-center gap-3 text-[#444] text-[15px]"><svg
                                xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-smartphone" aria-hidden="true">
                                <rect width="14" height="20" x="5" y="2" rx="2" ry="2">
                                </rect>
                                <path d="M12 18h.01"></path>
                            </svg><span>Học mọi lúc, mọi nơi</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggles = document.querySelectorAll('.course-track-toggle');
                const toggleAllButton = document.getElementById('toggle-all-tracks');
                const previewPlayButton = document.querySelector('.course-preview-play-button');
                const previewOverlay = document.querySelector('.course-preview-overlay');
                const previewThumbnail = document.querySelector('.course-preview-thumbnail');
                const previewPlayer = document.querySelector('.course-preview-player');
                const previewYoutube = document.getElementById('course-preview-youtube');
                const previewVideo = document.getElementById('course-preview-video');

                const setTrackExpandedState = function(toggle, expanded) {
                    const targetId = toggle.getAttribute('data-target');
                    const panel = document.getElementById(targetId);

                    if (!panel) {
                        return;
                    }

                    toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                    panel.classList.toggle('hidden', !expanded);

                    const icon = toggle.querySelector('.track-toggle-icon');

                    if (!icon) {
                        return;
                    }

                    icon.classList.toggle('lucide-plus', !expanded);
                    icon.classList.toggle('lucide-minus', expanded);

                    const plusPath = icon.querySelector('path:nth-child(2)');

                    if (!expanded && !plusPath) {
                        const newPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        newPath.setAttribute('d', 'M12 5v14');
                        icon.appendChild(newPath);
                    }

                    if (expanded && plusPath) {
                        plusPath.remove();
                    }
                };

                const updateToggleAllLabel = function() {
                    if (!toggleAllButton) {
                        return;
                    }

                    const allExpanded = Array.from(toggles).every(function(toggle) {
                        return toggle.getAttribute('aria-expanded') === 'true';
                    });

                    toggleAllButton.textContent = allExpanded ? 'Thu gọn tất cả' : 'Mở rộng tất cả';
                };

                toggles.forEach(function(toggle) {
                    toggle.addEventListener('click', function() {
                        const isExpanded = this.getAttribute('aria-expanded') === 'true';
                        setTrackExpandedState(this, !isExpanded);
                        updateToggleAllLabel();
                    });
                });

                if (toggleAllButton) {
                    toggleAllButton.addEventListener('click', function() {
                        const shouldExpandAll = this.textContent.trim() === 'Mở rộng tất cả';

                        toggles.forEach(function(toggle) {
                            setTrackExpandedState(toggle, shouldExpandAll);
                        });

                        updateToggleAllLabel();
                    });
                }

                const playPreviewInline = function() {
                    if (!previewPlayer) {
                        return;
                    }

                    previewPlayer.classList.remove('hidden');

                    if (previewOverlay) {
                        previewOverlay.classList.add('hidden');
                    }

                    if (previewThumbnail) {
                        previewThumbnail.classList.add('hidden');
                    }

                    if (previewYoutube && !previewYoutube.getAttribute('src')) {
                        previewYoutube.setAttribute('src', previewYoutube.getAttribute('data-src') || '');
                    }

                    if (previewVideo) {
                        previewVideo.play().catch(function() {
                            // Ignore autoplay rejection; user can press native play.
                        });
                    }
                };

                if (previewPlayButton) {
                    previewPlayButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        playPreviewInline();
                    });
                }

                updateToggleAllLabel();
            });
        </script>
    @endpush
@endsection
