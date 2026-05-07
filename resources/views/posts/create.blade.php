@extends('layouts.app')

@section('title', 'Viết bài')

@push('styles')
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
@endpush

@section('content')
    <main class="flex-1 lg:ml-24">
        <div class="mx-auto w-full max-w-[1100px] p-4 lg:p-8">
            <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data"
                class="rounded-2xl border border-gray-200 bg-white shadow-sm"
                data-post-composer data-upload-url="{{ route('posts.editor.image') }}"
                x-data="{
                    thumbUrl: '{{ old('thumbnail') ? '' : '' }}',
                    coverUrl: '{{ old('image') ? '' : '' }}',
                    setPreview(e, key) {
                        const file = e.target.files && e.target.files[0];
                        if (!file) return;
                        const url = URL.createObjectURL(file);
                        if (key === 'thumb') this.thumbUrl = url;
                        if (key === 'cover') this.coverUrl = url;
                    }
                }">
                @csrf

                <div class="border-b border-gray-100 p-4 lg:p-6">
                    <input type="text" name="title" value="{{ old('title') }}"
                        placeholder="Tiêu đề bài viết…"
                        class="w-full border-0 p-0 text-[28px] font-black leading-tight text-gray-900 placeholder-[#a9b3bb] focus:outline-none focus:ring-0" />
                    <x-forms.input-error :messages="$errors->get('title')" />
                </div>

                <div class="grid grid-cols-1 gap-6 p-4 lg:grid-cols-12 lg:p-6">
                    <div class="lg:col-span-8">
                        <div class="rounded-xl border border-gray-200 bg-white">
                            <div class="p-3 text-xs font-semibold text-gray-600">
                                Nội dung (Markdown)
                            </div>
                            <div class="border-t border-gray-100">
                                <div data-editor class="min-h-[520px]"></div>
                            </div>
                        </div>

                        <textarea name="content" class="hidden">{{ old('content') }}</textarea>
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
                                        <img :src="thumbUrl" alt="Thumbnail preview" class="h-[160px] w-full object-cover" />
                                    </template>
                                    <template x-if="!thumbUrl">
                                        <div class="flex h-[160px] flex-col items-center justify-center gap-2 p-4">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-black/5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="text-gray-700" aria-hidden="true">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                    <polyline points="17 8 12 3 7 8" />
                                                    <line x1="12" x2="12" y1="3" y2="15" />
                                                </svg>
                                            </div>
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
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-black/5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="text-gray-700" aria-hidden="true">
                                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                                    <circle cx="9" cy="9" r="2" />
                                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                                </svg>
                                            </div>
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

