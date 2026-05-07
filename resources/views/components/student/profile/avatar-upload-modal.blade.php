<div id="avatar-upload-modal" data-modal class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/60" data-modal-close></div>
    <div class="relative z-[101] h-full w-full flex items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-2xl border border-slate-700 bg-[#111827] shadow-2xl">
            <div class="px-5 py-4 border-b border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Đổi ảnh đại diện</h3>
                    <p class="text-sm text-slate-400">Upload ảnh mới (JPG, PNG, WEBP - tối đa 2MB).</p>
                </div>
                <button type="button" data-modal-close class="h-9 w-9 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('user.profile.avatar.update') }}" enctype="multipart/form-data"
                class="p-5 space-y-4" data-form-loading>
                @csrf
                <div class="flex items-center gap-4">
                    <div class="h-20 w-20 rounded-2xl overflow-hidden bg-slate-800 border border-slate-700">
                        <img src="{{ $profile['avatar'] ?: 'https://placehold.co/80x80/0f172a/e2e8f0?text=U' }}"
                            alt="Avatar preview" id="avatar-preview-image" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <label class="text-sm text-slate-300 mb-2 block">Chọn ảnh mới</label>
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" id="avatar-upload-input"
                            class="w-full text-sm text-slate-300 file:mr-3 file:px-3 file:h-10 file:rounded-xl file:border-0 file:bg-emerald-500 file:text-white file:font-semibold hover:file:bg-emerald-600">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" data-modal-close class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700">
                        Hủy
                    </button>
                    <button type="submit" class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600" data-loading-text="Đang upload...">
                        Cập nhật ảnh
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
