<div id="change-password-modal" data-modal class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/60" data-modal-close></div>
    <div class="relative z-[101] h-full w-full flex items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-2xl border border-slate-700 bg-[#111827] shadow-2xl">
            <div class="px-5 py-4 border-b border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Đổi mật khẩu</h3>
                    <p class="text-sm text-slate-400">Cập nhật mật khẩu để bảo mật tài khoản học tập.</p>
                </div>
                <button type="button" data-modal-close class="h-9 w-9 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('user.profile.password.update') }}" class="p-5 space-y-4" data-form-loading>
                @csrf
                @method('PATCH')
                <div>
                    <label class="text-sm text-slate-300 mb-2 block">Mật khẩu hiện tại</label>
                    <div class="relative">
                        <input type="password" name="current_password"
                            class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 pr-11 focus:outline-none focus:border-emerald-500"
                            data-password-input>
                        <button type="button" class="absolute top-1/2 -translate-y-1/2 right-3 text-slate-400 hover:text-white" data-password-toggle>
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="text-sm text-slate-300 mb-2 block">Mật khẩu mới</label>
                    <div class="relative">
                        <input type="password" name="password"
                            class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 pr-11 focus:outline-none focus:border-emerald-500"
                            data-password-input>
                        <button type="button" class="absolute top-1/2 -translate-y-1/2 right-3 text-slate-400 hover:text-white" data-password-toggle>
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="text-sm text-slate-300 mb-2 block">Xác nhận mật khẩu mới</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation"
                            class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 pr-11 focus:outline-none focus:border-emerald-500"
                            data-password-input>
                        <button type="button" class="absolute top-1/2 -translate-y-1/2 right-3 text-slate-400 hover:text-white" data-password-toggle>
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" data-modal-close class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700">
                        Hủy
                    </button>
                    <button type="submit" class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600" data-loading-text="Đang cập nhật...">
                        Cập nhật mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
