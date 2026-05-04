@extends('layouts.app')

@php
    $firstItem = $order->items->first();
    $course = $firstItem ? $firstItem->course : null;
@endphp

@section('title', 'Thanh toán thành công')

@section('content')
    <main class="ml-0 sm:ml-[96px] flex-1 flex justify-center items-start min-h-[calc(100vh-66px)]">
        <div class="w-full max-w-[640px] px-4 sm:px-6 py-12">
            <div
                class="rounded border border-[#c8e6c9] bg-[#e8f5e9] px-5 py-4 text-[15px] text-[#1e4620] mb-8"
                role="status">
                Cảm ơn bạn. Đơn hàng đã được thanh toán thành công.
            </div>
            <h1 class="text-[26px] font-bold text-[#2d2f31] mb-4">Hoàn tất</h1>
            <p class="text-[15px] text-[#6a6f73] mb-6 leading-relaxed">
                @if ($course)
                    Bạn đã được ghi danh vào khóa học
                    <span class="font-semibold text-[#2d2f31]">{{ $course->title }}</span>.
                @else
                    Khóa học đã được kích hoạt trên tài khoản của bạn.
                @endif
            </p>
            @if ($course)
                <a href="{{ route('courses.show', $course->slug) }}"
                    class="inline-flex h-12 items-center justify-center rounded-[2px] bg-[#5624d0] px-6 text-[15px] font-bold text-white hover:bg-[#401b9c] transition-colors">
                    Vào khóa học
                </a>
            @endif
        </div>
    </main>
@endsection
