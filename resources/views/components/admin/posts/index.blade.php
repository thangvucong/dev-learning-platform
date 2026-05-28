@extends('components.admin.adminDashboard')

@section('title', 'Quản lý bài viết')

@section('content')
    <header class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Quản lý bài viết</h1>
            <p class="text-sm text-slate-400">Moderation dashboard: duyệt, từ chối và theo dõi hiệu suất bài viết.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('posts.create') }}"
                class="rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-600 transition-all">
                + Viết bài mới
            </a>
        </div>
    </header>

    <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-slate-700 bg-[#1e293b] p-4">
            <p class="text-xs uppercase tracking-widest text-slate-400">Tổng bài viết</p>
            <p class="mt-2 text-3xl font-black text-white">{{ (int) data_get($stats, 'total', 0) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-700 bg-[#1e293b] p-4">
            <p class="text-xs uppercase tracking-widest text-slate-400">Cần admin duyệt</p>
            <p class="mt-2 text-3xl font-black text-amber-300">{{ (int) data_get($stats, \App\Models\Post::STATUS_PENDING_HUMAN_REVIEW, 0) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-700 bg-[#1e293b] p-4">
            <p class="text-xs uppercase tracking-widest text-slate-400">Đã xuất bản</p>
            <p class="mt-2 text-3xl font-black text-emerald-300">{{ (int) data_get($stats, \App\Models\Post::STATUS_PUBLISHED, 0) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-700 bg-[#1e293b] p-4">
            <p class="text-xs uppercase tracking-widest text-slate-400">Bị từ chối</p>
            <p class="mt-2 text-3xl font-black text-red-300">{{ (int) data_get($stats, \App\Models\Post::STATUS_REJECTED, 0) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-700 bg-[#1e293b] p-4">
            <p class="text-xs uppercase tracking-widest text-slate-400">Tổng lượt xem</p>
            <p class="mt-2 text-3xl font-black text-indigo-200">{{ number_format((int) data_get($stats, 'total_views', 0), 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1 scrollbar-hide">
            @foreach ($tabs as $key => $label)
                @php
                    $count = (int) data_get($stats, $key, 0);
                    $isActive = $status === $key;
                @endphp
                <a href="{{ route('admin.posts.index', array_filter(['status' => $key, 'q' => $q, 'sort' => $sort])) }}"
                    class="shrink-0 inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition
                        {{ $isActive ? 'border-amber-500/40 bg-amber-500/10 text-amber-200' : 'border-slate-700 bg-[#1e293b] text-slate-200 hover:bg-slate-800/50' }}">
                    <span>{{ $label }}</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $isActive ? 'bg-amber-500 text-[#0f172a]' : 'bg-slate-800 text-slate-200' }}">
                        {{ $count }}
                    </span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative w-full sm:w-[360px]">
                <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input name="q" value="{{ $q }}" placeholder="Tìm tiêu đề hoặc tác giả…"
                    class="w-full rounded-xl border border-slate-700 bg-[#1e293b] py-2.5 pl-10 pr-3 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-0">
            </div>
            <select name="sort"
                class="w-full sm:w-auto rounded-xl border border-slate-700 bg-[#1e293b] py-2.5 pl-3 pr-10 text-sm font-semibold text-slate-200 focus:border-emerald-500 focus:ring-0">
                <option value="newest" @selected($sort === 'newest')>Mới nhất</option>
                <option value="oldest" @selected($sort === 'oldest')>Cũ nhất</option>
            </select>
            <button type="submit"
                class="rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-200 hover:bg-slate-700 transition-all">
                Lọc
            </button>
        </form>
    </div>

    <div class="rounded-2xl border border-slate-700 bg-[#1e293b] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/50 text-slate-400 text-[10px] uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Bài viết</th>
                        <th class="px-6 py-4 font-semibold">Tác giả</th>
                        <th class="px-6 py-4 font-semibold text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold">Ngày tạo</th>
                        <th class="px-6 py-4 font-semibold text-center">Views</th>
                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @if ($posts->count() === 0)
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-800/70 border border-slate-700 mb-3">
                                    <i class="fa-regular fa-face-smile text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold">Không có bài viết phù hợp</p>
                                <p class="text-xs text-slate-500 mt-1">Hãy thử đổi tab hoặc từ khóa tìm kiếm.</p>
                            </td>
                        </tr>
                    @else
                        @foreach ($posts as $post)
                            <tr class="group hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-3">
                                        @php
                                            $thumb = $post['thumbnail'] ? \Illuminate\Support\Facades\Storage::url($post['thumbnail']) : null;
                                        @endphp
                                        <div class="w-16 h-10 rounded-lg overflow-hidden bg-slate-800 border border-slate-700 shrink-0">
                                            @if ($thumb)
                                                <img src="{{ $thumb }}" alt="{{ $post['title'] }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.posts.show', $post['id']) }}"
                                                class="text-sm font-bold text-white group-hover:text-emerald-300 transition-colors line-clamp-1">
                                                {{ $post['title'] }}
                                            </a>
                                            <p class="mt-0.5 text-xs text-slate-400 line-clamp-1">{{ $post['description'] }}</p>
                                            @if ($post['status'] === \App\Models\Post::STATUS_REJECTED && !empty($post['reject_reason']))
                                                <p class="mt-1 text-[11px] text-red-300 line-clamp-1">
                                                    <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $post['reject_reason'] }}
                                                </p>
                                            @endif
                                            @if (!empty($post['ai_decision']) || !empty($post['ai_escalation_reason']))
                                                <div class="mt-2 flex flex-wrap items-center gap-1.5 text-[11px]">
                                                    @if (!empty($post['ai_severity']))
                                                        <span class="rounded-full border border-slate-600 bg-slate-900/40 px-2 py-0.5 font-semibold text-slate-300">
                                                            AI: {{ strtoupper($post['ai_severity']) }}
                                                        </span>
                                                    @endif
                                                    @if ($post['ai_confidence'] !== null)
                                                        <span class="rounded-full border border-sky-500/30 bg-sky-500/10 px-2 py-0.5 font-semibold text-sky-200">
                                                            {{ number_format(((float) $post['ai_confidence']) * 100, 0) }}%
                                                        </span>
                                                    @endif
                                                    @foreach (array_slice((array) ($post['ai_flags'] ?? []), 0, 2) as $flag)
                                                        <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-0.5 font-semibold text-amber-200">
                                                            {{ data_get($flag, 'category', 'flag') }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                @if (!empty($post['ai_escalation_reason']))
                                                    <p class="mt-1 text-[11px] text-amber-200 line-clamp-1">
                                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $post['ai_escalation_reason'] }}
                                                    </p>
                                                @elseif (!empty($post['ai_summary']))
                                                    <p class="mt-1 text-[11px] text-slate-400 line-clamp-1">{{ $post['ai_summary'] }}</p>
                                                @endif
                                            @endif
                                            <p class="mt-1 text-[11px] text-slate-500">
                                                {{ (int) $post['reading_time'] }} phút đọc
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-200 line-clamp-1">{{ $post['author']['name'] }}</div>
                                    <div class="text-xs text-slate-500 line-clamp-1">{{ $post['author']['email'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <x-posts.status-badge :status="$post['status']" />
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-300">
                                    {{ optional($post['created_at'])->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-slate-200">
                                    {{ number_format((int) $post['views_count'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-1 opacity-100">
                                        <a href="{{ route('admin.posts.show', $post['id']) }}"
                                            class="p-2 rounded-lg hover:bg-slate-700/60 text-slate-300 hover:text-white transition-colors"
                                            title="Preview">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>

                                        @if (in_array($post['status'], [\App\Models\Post::STATUS_PENDING, \App\Models\Post::STATUS_PENDING_HUMAN_REVIEW], true))
                                            <form method="POST" action="{{ route('admin.posts.approve', $post['id']) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 rounded-lg hover:bg-emerald-500/15 text-emerald-300 hover:text-emerald-200 transition-colors"
                                                    title="Duyệt">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </button>
                                            </form>

                                            <button type="button"
                                                class="p-2 rounded-lg hover:bg-red-500/10 text-red-300 hover:text-red-200 transition-colors"
                                                title="Từ chối"
                                                data-reject-open
                                                data-reject-id="{{ $post['id'] }}"
                                                data-reject-title="{{ e($post['title']) }}">
                                                <i class="fa-solid fa-circle-xmark"></i>
                                            </button>
                                        @endif

                                        @if ($post['status'] === \App\Models\Post::STATUS_PUBLISHED)
                                            <form method="POST" action="{{ route('admin.posts.unpublish', $post['id']) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 rounded-lg hover:bg-amber-500/10 text-amber-300 hover:text-amber-200 transition-colors"
                                                    title="Ẩn bài">
                                                    <i class="fa-solid fa-eye-slash"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.posts.destroy', $post['id']) }}" class="inline"
                                            onsubmit="return confirm('Xóa bài viết này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-lg hover:bg-slate-700/60 text-slate-400 hover:text-white transition-colors"
                                                title="Xóa">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        @if ($posts->hasPages())
            <div class="p-4 border-t border-slate-700 bg-slate-800/20">
                {{ $posts->links('components.pagination.minimal') }}
            </div>
        @endif
    </div>

    <!-- Reject modal -->
    <div id="reject-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" data-reject-close></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-[#1e293b] w-full max-w-xl rounded-2xl border border-slate-700 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
                    <div>
                        <h3 class="text-xl font-bold text-white">Từ chối bài viết</h3>
                        <p id="reject-modal-title" class="text-xs text-slate-400 mt-1 line-clamp-1"></p>
                    </div>
                    <button type="button" data-reject-close class="p-2 text-slate-400 hover:text-white">✕</button>
                </div>
                <form id="reject-form" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs text-slate-400 mb-2">Lý do từ chối *</label>
                        <textarea name="reject_reason" rows="5" required
                            class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-red-500"
                            placeholder="Nêu rõ lý do để tác giả chỉnh sửa và gửi lại...">{{ old('reject_reason') }}</textarea>
                        @error('reject_reason')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" data-reject-close class="px-4 py-2 rounded-lg border border-slate-600 text-slate-300">Hủy</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600">Từ chối</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const modal = document.getElementById('reject-modal');
            const titleEl = document.getElementById('reject-modal-title');
            const form = document.getElementById('reject-form');
            if (!modal || !form) return;

            function openModal(postId, postTitle) {
                form.action = `/admin/posts/${postId}/reject`;
                if (titleEl) titleEl.textContent = postTitle || '';
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            document.addEventListener('click', function (e) {
                const openBtn = e.target.closest('[data-reject-open]');
                if (openBtn) {
                    openModal(openBtn.getAttribute('data-reject-id'), openBtn.getAttribute('data-reject-title'));
                    return;
                }

                if (e.target.closest('[data-reject-close]')) {
                    closeModal();
                }
            });
        })();
    </script>
@endpush
