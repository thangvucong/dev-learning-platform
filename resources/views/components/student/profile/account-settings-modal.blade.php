<div id="account-settings-modal" data-modal class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/60" data-modal-close></div>
    <div class="relative z-[101] h-full w-full flex items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-2xl border border-slate-700 bg-[#111827] shadow-2xl">
            <div class="px-5 py-4 border-b border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Cài đặt tài khoản</h3>
                    <p class="text-sm text-slate-400">Thiết lập timezone và tùy chọn thông báo cơ bản.</p>
                </div>
                <button type="button" data-modal-close class="h-9 w-9 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('user.profile.settings.update') }}" class="p-5 space-y-4" data-form-loading>
                @csrf
                @method('PATCH')
                <div>
                    <label class="text-sm text-slate-300 mb-2 block">Timezone</label>
                    <select name="timezone"
                        class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                        @foreach (['Asia/Ho_Chi_Minh', 'Asia/Singapore', 'UTC'] as $timezone)
                            <option value="{{ $timezone }}" @selected(($settings['timezone'] ?? 'Asia/Ho_Chi_Minh') === $timezone)>{{ $timezone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="notifications_enabled" value="1" @checked($settings['notifications_enabled'] ?? true)
                            class="rounded border-slate-600 bg-slate-900/60 text-emerald-500 focus:ring-emerald-500">
                        Nhận thông báo nhắc lịch học
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="weekly_report" value="1" @checked($settings['weekly_report'] ?? false)
                            class="rounded border-slate-600 bg-slate-900/60 text-emerald-500 focus:ring-emerald-500">
                        Nhận báo cáo học tập hằng tuần (placeholder)
                    </label>
                </div>
                <div class="rounded-xl border border-slate-700 bg-slate-900/40 px-3 py-2 text-sm text-slate-400">
                    Các cài đặt nâng cao sẽ được mở rộng ở iteration tiếp theo.
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" data-modal-close class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700">
                        Hủy
                    </button>
                    <button type="submit" class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600" data-loading-text="Đang lưu...">
                        Lưu cài đặt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
