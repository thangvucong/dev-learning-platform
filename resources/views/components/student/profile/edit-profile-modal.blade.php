<div id="edit-profile-modal" data-modal class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/60" data-modal-close></div>
    <div class="relative z-[101] h-full w-full flex items-center justify-center p-4">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-700 bg-[#111827] shadow-2xl">
            <div class="px-5 py-4 border-b border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Chỉnh sửa hồ sơ</h3>
                    <p class="text-sm text-slate-400">Cập nhật thông tin học viên và liên kết cá nhân.</p>
                </div>
                <button type="button" data-modal-close class="h-9 w-9 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('user.profile.update') }}" class="p-5 space-y-4" data-form-loading>
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="md:col-span-2">
                        <label class="text-sm text-slate-300 mb-2 block">Họ và tên</label>
                        <input type="text" name="name" value="{{ old('name', $profile['name']) }}"
                            class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm text-slate-300 mb-2 block">Email</label>
                        <input type="email" value="{{ $profile['email'] }}" disabled
                            class="w-full h-11 rounded-xl bg-slate-900/40 border border-slate-700 text-slate-400 px-4 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="text-sm text-slate-300 mb-2 block">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $profile['phone'] !== 'Đang cập nhật' ? $profile['phone'] : '') }}"
                            class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="text-sm text-slate-300 mb-2 block">Ngày sinh</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                            class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm text-slate-300 mb-2 block">Giới thiệu</label>
                        <textarea name="bio" rows="3"
                            class="w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 py-3 focus:outline-none focus:border-emerald-500">{{ old('bio', $profile['bio']) }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm text-slate-300 mb-2 block">GitHub URL</label>
                        <input type="url" name="social_github" value="{{ old('social_github') }}"
                            class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="text-sm text-slate-300 mb-2 block">LinkedIn URL</label>
                        <input type="url" name="social_linkedin" value="{{ old('social_linkedin') }}"
                            class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm text-slate-300 mb-2 block">Portfolio URL</label>
                        <input type="url" name="social_portfolio" value="{{ old('social_portfolio') }}"
                            class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" data-modal-close class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700">
                        Hủy
                    </button>
                    <button type="submit" class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600" data-loading-text="Đang lưu...">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
