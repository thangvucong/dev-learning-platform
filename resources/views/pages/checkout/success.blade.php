@extends('layouts.app')

@php
    $firstItem = $order->items->first();
    $course = $firstItem ? $firstItem->course : null;
    $itemCount = $order->items->count();
    $hasDiscount = (int) $order->discount_amount > 0;
    $formatCurrency = static function ($amount) {
        return number_format((int) $amount, 0, ',', '.') . 'đ';
    };
    $paymentMethodLabels = [
        \App\Models\Order::PAYMENT_ONEPAY_DOMESTIC => 'OnePay - Thẻ nội địa',
        \App\Models\Order::PAYMENT_ONEPAY_INTERNATIONAL => 'OnePay - Thẻ quốc tế',
        \App\Models\Order::PAYMENT_SEPAY_QR => 'SePay QR',
    ];
    $courseThumbnailFallbacks = [
        'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=480&q=80',
        'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=480&q=80',
        'https://images.unsplash.com/photo-1605379399642-870262d3d051?auto=format&fit=crop&w=480&q=80',
        'https://images.unsplash.com/photo-1559028012-481c04fa702d?auto=format&fit=crop&w=480&q=80',
    ];
@endphp

@section('title', 'Thanh toán thành công')

@section('content')
    <main class="ml-0 sm:ml-[96px] flex-1 min-h-[calc(100vh-66px)] bg-[#f7f9fa]">
        <div class="w-full max-w-[1120px] mx-auto px-4 sm:px-6 lg:px-10 py-8 sm:py-12">
            <section
                class="overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-sm">
                <div class="border-b border-[#e5e7eb] px-5 py-6 sm:px-8 sm:py-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-4">
                            <span
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#e8f5e9] text-[#16a34a]"
                                aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.32a1 1 0 0 1-1.42.002L3.29 9.246a1 1 0 1 1 1.42-1.41l4.04 4.067 6.54-6.607a1 1 0 0 1 1.414-.006Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="mb-2 text-[13px] font-bold uppercase text-[#16a34a]">
                                    Thanh toán thành công
                                </p>
                                <h1 class="text-[28px] font-black leading-tight text-[#2d2f31] sm:text-[34px]">
                                    Đơn hàng của bạn đã hoàn tất
                                </h1>
                                <p class="mt-3 max-w-[640px] text-[15px] leading-relaxed text-[#6a6f73]">
                                    @if ($course)
                                        Khóa học
                                        <span class="font-bold text-[#2d2f31]">{{ $course->title }}</span>
                                        đã được kích hoạt trên tài khoản của bạn.
                                    @else
                                        Các khóa học trong đơn hàng đã được kích hoạt trên tài khoản của bạn.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div
                            class="shrink-0 rounded-xl border border-[#d1d7dc] bg-[#f7f9fa] px-4 py-3 text-left sm:text-right">
                            <p class="text-[13px] text-[#6a6f73]">Mã đơn hàng</p>
                            <p class="mt-1 text-[16px] font-bold text-[#2d2f31]">#{{ $order->id }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-0 lg:grid-cols-[1fr_360px]">
                    <div class="px-5 py-6 sm:px-8 sm:py-8">
                        <h2 class="mb-5 text-[20px] font-bold text-[#2d2f31]">Khóa học đã mua</h2>

                        <div class="space-y-4">
                            @foreach ($order->items as $item)
                                @php
                                    $itemCourse = $item->course;
                                    $itemHasDiscount = (int) $item->discount_amount > 0;
                                    $fallbackThumbnail = $courseThumbnailFallbacks[$loop->index % count($courseThumbnailFallbacks)];
                                    $thumbnailUrl = media_url(optional($itemCourse)->thumbnail_url, $fallbackThumbnail);
                                @endphp
                                <article
                                    class="flex flex-col gap-4 rounded-xl border border-[#e5e7eb] bg-white p-4 sm:flex-row sm:items-start">
                                    <img
                                        src="{{ $thumbnailUrl }}"
                                        alt="{{ optional($itemCourse)->title ?: 'Khóa học' }}"
                                        class="h-24 w-full rounded-lg border border-[#d1d7dc] object-cover sm:h-20 sm:w-28">

                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-[16px] font-bold leading-snug text-[#2d2f31]">
                                            {{ optional($itemCourse)->title ?: 'Khóa học đã mua' }}
                                        </h3>
                                        <p class="mt-2 text-[14px] text-[#6a6f73]">
                                            Trạng thái:
                                            <span class="font-semibold text-[#16a34a]">Đã kích hoạt</span>
                                        </p>
                                        @if ($itemHasDiscount)
                                            <span
                                                class="mt-3 inline-flex items-center rounded-full bg-[#fff1e8] px-2.5 py-1 text-[12px] font-bold text-[#f05123]">
                                                Đã giảm {{ $formatCurrency($item->discount_amount) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="shrink-0 text-left sm:text-right">
                                        @if ($itemHasDiscount)
                                            <div class="text-[13px] text-[#6a6f73]"
                                                style="text-decoration: line-through; text-decoration-thickness: 1px;">
                                                {{ $formatCurrency($item->original_price) }}
                                            </div>
                                        @endif
                                        <div class="text-[18px] font-black text-[#2d2f31]">
                                            {{ $formatCurrency($item->final_price) }}
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                            @if ($course)
                                <a href="{{ route('courses.show', $course->slug) }}"
                                    class="inline-flex h-12 items-center justify-center rounded-[6px] bg-[#1473e6] px-6 text-[15px] font-bold text-white transition-colors hover:bg-[#105cba]">
                                    Vào khóa học
                                </a>
                            @endif
                            <a href="{{ route('home') }}"
                                class="inline-flex h-12 items-center justify-center rounded-[6px] border border-[#d1d7dc] bg-white px-6 text-[15px] font-bold text-[#2d2f31] transition-colors hover:bg-[#f7f9fa]">
                                Về trang chủ
                            </a>
                        </div>
                    </div>

                    <aside class="border-t border-[#e5e7eb] bg-white px-5 py-6 sm:px-8 lg:border-l lg:border-t-0">
                        <h2 class="mb-5 text-[20px] font-bold text-[#2d2f31]">Tóm tắt thanh toán</h2>

                        <div class="space-y-4 text-[15px]">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-[#6a6f73]">Số khóa học</span>
                                <span class="font-semibold text-[#2d2f31]">{{ $itemCount }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-[#6a6f73]">Giá gốc</span>
                                <span class="text-[#2d2f31]">{{ $formatCurrency($order->subtotal_amount) }}</span>
                            </div>
                            @if ($hasDiscount)
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-[#6a6f73]">Giảm giá</span>
                                    <span class="font-semibold text-[#16a34a]">-{{ $formatCurrency($order->discount_amount) }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-[#6a6f73]">Phương thức</span>
                                <span class="text-right font-semibold text-[#2d2f31]">
                                    {{ $paymentMethodLabels[$order->payment_method] ?? 'Thanh toán online' }}
                                </span>
                            </div>
                            @if ($order->paid_at)
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-[#6a6f73]">Thời gian</span>
                                    <span class="text-right text-[#2d2f31]">{{ $order->paid_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>

                        <hr class="my-5 border-[#d1d7dc]">

                        <div class="flex items-start justify-between gap-4">
                            <span class="text-[16px] font-bold text-[#2d2f31]">Tổng đã thanh toán</span>
                            <span class="text-right text-[26px] font-black leading-none text-[#f05123]">
                                {{ $formatCurrency($order->total_amount) }}
                            </span>
                        </div>
                    </aside>
                </div>
            </section>
        </div>
    </main>
@endsection
