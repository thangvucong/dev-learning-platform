@extends('layouts.app')

@section('title', 'Chỉnh sửa bài viết')

@push('styles')
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
@endpush

@section('content')
    <main class="flex-1 lg:ml-24">
        <div class="mx-auto w-full max-w-[1100px] p-4 lg:p-8">
            <form method="POST" action="{{ route('posts.update', $post->id) }}" enctype="multipart/form-data"
                class="rounded-2xl border border-gray-200 bg-white shadow-sm"
                data-post-composer data-upload-url="{{ route('posts.editor.image') }}"
                x-data="{
                    thumbUrl: '{{ $post->thumbnail ? \Illuminate\Support\Facades\Storage::url($post->thumbnail) : '' }}',
                    coverUrl: '{{ $post->image ? \Illuminate\Support\Facades\Storage::url($post->image) : '' }}',
                    setPreview(e, key) {
                        const file = e.target.files && e.target.files[0];
                        if (!file) return;
                        const url = URL.createObjectURL(file);
                        if (key === 'thumb') this.thumbUrl = url;
                        if (key === 'cover') this.coverUrl = url;
                    }
                }">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-100 p-4 lg:p-6">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-posts.status-badge :status="$post->status" />
                            @if ($post->status === \App\Models\Post::STATUS_REJECTED && !empty($post->reject_reason))
                                <span class="text-xs font-semibold text-red-600">Có phản hồi từ kiểm duyệt</span>
                            @endif
                        </div>
                        <a href="{{ route('my-posts.index') }}" class="text-sm font-semibold text-gray-600 hover:underline">
                            Quay lại
                        </a>
                    </div>

                    <input type="text" name="title" value="{{ old('title', $post->title) }}"
                        placeholder="Tiêu đề bài viết…"
                        class="w-full border-0 p-0 text-[28px] font-black leading-tight text-gray-900 placeholder-[#a9b3bb] focus:outline-none focus:ring-0" />
                    <x-forms.input-error :messages="$errors->get('title')" />
                </div>

                <div class="grid grid-cols-1 gap-6 p-4 lg:grid-cols-12 lg:p-6">
                    <div class="lg:col-span-8">
                        @if ($post->status === \App\Models\Post::STATUS_REJECTED && !empty($post->reject_reason))
                            <div class="mb-4 rounded-2xl border border-red-500/20 bg-red-500/5 p-4">
                                <p class="text-sm font-black text-red-600">Lý do từ chối</p>
                                <p class="mt-1 text-sm text-red-700 leading-relaxed">{{ $post->reject_reason }}</p>
                            </div>
                        @endif

                        <div class="rounded-xl border border-gray-200 bg-white">
                            <div class="p-3 text-xs font-semibold text-gray-600">
                                Nội dung (Markdown)
                            </div>
                            <div class="border-t border-gray-100">
                                <div data-editor class="min-h-[520px]"></div>
                            </div>
                        </div>

                        <textarea name="content" class="hidden">{{ old('content', $post->content) }}</textarea>
                        <x-forms.input-error :messages="$errors->get('content')" />
                    </div>

                    <aside class="lg:col-span-4 space-y-6">
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-sm font-bold text-gray-900">Thumbnail</p>
                                <span class="text-xs text-gray-500">Tối đa 2MB</span>
                            </div>

                            <label class="group block cursor-pointer">
                                <input type="file" name="thumbnail" accept="image/*" class="hidden"
                                    @change="setPreview($event, 'thumb')" />
                                <div
                                    class="relative overflow-hidden rounded-xl border border-dashed border-gray-300 bg-[#f9f9f9] transition group-hover:border-gray-400">
                                    <template x-if="thumbUrl">
                                        <img :src="thumbUrl" alt="Thumbnail preview"
                                            class="h-[160px] w-full object-cover" />
                                    </template>
                                    <template x-if="!thumbUrl">
                                        <div class="flex h-[160px] flex-col items-center justify-center gap-2 p-4">
                                            <p class="text-sm font-semibold text-gray-900">Tải ảnh lên</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, WEBP</p>
                                        </div>
                                    </template>
                                </div>
                            </label>

                            <x-forms.input-error :messages="$errors->get('thumbnail')" />
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-sm font-bold text-gray-900">Ảnh cover</p>
                                <span class="text-xs text-gray-500">Tối đa 4MB</span>
                            </div>

                            <label class="group block cursor-pointer">
                                <input type="file" name="image" accept="image/*" class="hidden"
                                    @change="setPreview($event, 'cover')" />
                                <div
                                    class="relative overflow-hidden rounded-xl border border-dashed border-gray-300 bg-[#f9f9f9] transition group-hover:border-gray-400">
                                    <template x-if="coverUrl">
                                        <img :src="coverUrl" alt="Cover preview" class="h-[200px] w-full object-cover" />
                                    </template>
                                    <template x-if="!coverUrl">
                                        <div class="flex h-[200px] flex-col items-center justify-center gap-2 p-4">
                                            <p class="text-sm font-semibold text-gray-900">Chọn ảnh cover</p>
                                            <p class="text-xs text-gray-500">Tỷ lệ ngang đẹp hơn</p>
                                        </div>
                                    </template>
                                </div>
                            </label>

                            <x-forms.input-error :messages="$errors->get('image')" />
                        </div>
                    </aside>
                </div>

                <div class="sticky bottom-4 flex justify-end px-4 pb-4 lg:px-6 lg:pb-6">
                    <div
                        class="flex items-center gap-2 rounded-2xl border border-gray-200 bg-white/90 p-2 shadow-[0_10px_30px_rgba(0,0,0,0.10)] backdrop-blur">
                        <button type="submit" name="action" value="draft"
                            class="inline-flex items-center justify-center rounded-xl bg-[#f5f5f5] px-4 py-2 text-sm font-semibold text-[#242424] transition hover:bg-[#ebebeb] focus:outline-none focus:ring-2 focus:ring-[#f05123]/20">
                            Lưu bản nháp
                        </button>
                        <button type="submit" name="action" value="pending"
                            class="inline-flex items-center justify-center rounded-xl bg-[#f05123] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#d8481f] focus:outline-none focus:ring-2 focus:ring-[#f05123]/20">
                            Gửi duyệt
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
@endpush

