<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="xl:col-span-2 rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-lg font-semibold text-white">Bảo mật tài khoản</h3>
        <p class="text-sm text-slate-300 mt-2">Bạn có thể đổi mật khẩu và theo dõi hoạt động đăng nhập gần đây để bảo vệ learning workspace.</p>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <button type="button" class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
                Đổi mật khẩu
            </button>
            <button type="button" class="h-10 px-4 rounded-xl border border-slate-600 text-slate-200 text-sm font-semibold hover:bg-slate-700 transition-colors">
                Cài đặt tài khoản
            </button>
        </div>

        <div class="mt-5 space-y-2">
            @foreach ($security['recent_logins'] as $login)
                <div class="rounded-lg border border-slate-700 bg-slate-950/40 p-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <div>
                        <p class="text-sm text-slate-100">{{ $login['device'] }}</p>
                        <p class="text-xs text-slate-400 mt-1">IP {{ $login['ip'] }}</p>
                    </div>
                    <p class="text-xs text-slate-400">{{ $login['time'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="xl:col-span-1 rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-lg font-semibold text-white">Device / sessions</h3>
        <p class="text-sm text-slate-400 mt-3">{{ $security['sessions_placeholder'] }}</p>
    </div>
</div>

