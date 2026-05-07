@extends('layouts.app')

@section('title', $post->title)

@push('styles')
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
@endpush

@section('content')
    <main class="flex-1 lg:ml-24">
        <div class="mx-auto w-full max-w-[1100px] p-4 lg:p-8">
            <article class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                @if (!empty($post->image))
                    <div class="relative">
                        <img class="h-[260px] w-full object-cover"
                            src="{{ \Illuminate\Support\Facades\Storage::url($post->image) }}"
                            alt="{{ $post->title }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/0 to-transparent"></div>
                    </div>
                @endif

                <div class="p-4 lg:p-8">
                    <div class="mb-6">
                        <h1 class="text-3xl font-black leading-tight text-gray-900">{{ $post->title }}</h1>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                            <span class="font-medium text-gray-700">{{ $post->user->name ?? 'Unknown' }}</span>
                            <span>•</span>
                            <span>{{ optional($post->created_at)->format('d/m/Y') }}</span>
                            @if (!$post->isPublished())
                                <span class="rounded bg-amber-500/10 px-2 py-0.5 text-xs font-bold text-amber-500">
                                    {{ $post->isPending() ? 'Chờ duyệt' : ($post->isRejected() ? 'Từ chối' : 'Nháp') }}
                                </span>
                            @endif
                        </div>
                        @if (!empty($post->description))
                            <p class="mt-4 text-base text-gray-700 leading-relaxed">{{ $post->description }}</p>
                        @endif
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white">
                        <div class="p-3 text-xs font-semibold text-gray-600">Nội dung</div>
                        <div class="border-t border-gray-100 p-4">
                            <div data-post-viewer class="prose max-w-none"></div>
                            <textarea class="hidden" data-post-markdown>{{ $post->content }}</textarea>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
    <script>
        window.addEventListener('load', function () {
            var viewerEl = document.querySelector('[data-post-viewer]');
            var mdEl = document.querySelector('[data-post-markdown]');
            if (!viewerEl || !mdEl || !window.toastui || !window.toastui.Editor) return;

            window.toastui.Editor.factory({
                el: viewerEl,
                viewer: true,
                initialValue: mdEl.value || '',
                usageStatistics: false
            });
        }, { once: true });
    </script>
@endpush

