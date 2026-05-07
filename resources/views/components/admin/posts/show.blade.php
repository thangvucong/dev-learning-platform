@extends('components.admin.adminDashboard')

@section('title', 'Preview bài viết')

@push('styles')
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
    <style>
        /* Toast UI Viewer dark theme tuning (admin layout) */
        [data-admin-post-viewer] .toastui-editor-contents {
            color: rgb(226 232 240); /* slate-200 */
        }

        [data-admin-post-viewer] .toastui-editor-contents p,
        [data-admin-post-viewer] .toastui-editor-contents li {
            color: rgb(203 213 225); /* slate-300 */
        }

        [data-admin-post-viewer] .toastui-editor-contents h1,
        [data-admin-post-viewer] .toastui-editor-contents h2,
        [data-admin-post-viewer] .toastui-editor-contents h3,
        [data-admin-post-viewer] .toastui-editor-contents h4 {
            color: #fff;
        }

        [data-admin-post-viewer] .toastui-editor-contents a {
            color: rgb(110 231 183); /* emerald-300 */
        }

        [data-admin-post-viewer] .toastui-editor-contents a:hover {
            color: rgb(52 211 153); /* emerald-400 */
            text-decoration: underline;
        }

        [data-admin-post-viewer] .toastui-editor-contents blockquote {
            border-left-color: rgba(148, 163, 184, 0.4); /* slate-400 */
            color: rgb(203 213 225);
            background: rgba(15, 23, 42, 0.35);
        }

        [data-admin-post-viewer] .toastui-editor-contents code {
            color: rgb(226 232 240);
            background: rgba(2, 6, 23, 0.55);
            border: 1px solid rgba(51, 65, 85, 0.7);
            border-radius: 6px;
            padding: 0.1rem 0.35rem;
        }

        [data-admin-post-viewer] .toastui-editor-contents pre {
            background: rgba(2, 6, 23, 0.65);
            border: 1px solid rgba(51, 65, 85, 0.8);
        }

        [data-admin-post-viewer] .toastui-editor-contents pre code {
            background: transparent;
            border: 0;
            padding: 0;
        }

        [data-admin-post-viewer] .toastui-editor-contents hr {
            border-color: rgba(51, 65, 85, 0.8);
        }

        [data-admin-post-viewer] .toastui-editor-contents table th,
        [data-admin-post-viewer] .toastui-editor-contents table td {
            border-color: rgba(51, 65, 85, 0.8);
        }
    </style>
@endpush

@section('content')
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Preview bài viết</h1>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-300">
                <x-posts.status-badge :status="$post->status" />
                <span class="text-slate-500">•</span>
                <span class="font-semibold text-slate-200">{{ $post->user->name ?? 'Unknown' }}</span>
                <span class="text-slate-500">•</span>
                <span class="text-slate-400">{{ optional($post->created_at)->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.posts.index') }}"
                class="rounded-xl border border-slate-700 bg-[#1e293b] px-4 py-2.5 text-sm font-semibold text-slate-200 hover:bg-slate-800/60 transition-all">
                Quay lại
            </a>

            @if ($post->status === \App\Models\Post::STATUS_PENDING)
                <form method="POST" action="{{ route('admin.posts.approve', $post->id) }}">
                    @csrf
                    <button type="submit"
                        class="rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-900/20">
                        Duyệt bài
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="rounded-2xl border border-slate-700 bg-[#1e293b] overflow-hidden shadow-sm">
        @if (!empty($post->image))
            <div class="relative">
                <img class="h-[260px] w-full object-cover"
                    src="{{ \Illuminate\Support\Facades\Storage::url($post->image) }}"
                    alt="{{ $post->title }}">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/0 to-transparent"></div>
            </div>
        @endif

        <div class="p-6">
            <h2 class="text-3xl font-black text-white leading-tight">{{ $post->title }}</h2>
            @if (!empty($post->description))
                <p class="mt-3 text-slate-300 leading-relaxed">{{ $post->description }}</p>
            @endif

            @if ($post->status === \App\Models\Post::STATUS_REJECTED && !empty($post->reject_reason))
                <div class="mt-5 rounded-2xl border border-red-500/20 bg-red-500/10 p-4">
                    <p class="text-sm font-bold text-red-200">Lý do từ chối</p>
                    <p class="mt-1 text-sm text-red-100/90 leading-relaxed">{{ $post->reject_reason }}</p>
                </div>
            @endif

            <div class="mt-6 rounded-xl border border-slate-700 bg-slate-900/30">
                <div class="p-3 text-xs font-semibold text-slate-400 border-b border-slate-700">Nội dung (Markdown render)</div>
                <div class="p-4">
                    <div data-admin-post-viewer></div>
                    <textarea class="hidden" data-admin-post-markdown>{{ $post->content }}</textarea>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
    <script>
        window.addEventListener('load', function () {
            var viewerEl = document.querySelector('[data-admin-post-viewer]');
            var mdEl = document.querySelector('[data-admin-post-markdown]');
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

