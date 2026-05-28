<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="xl:col-span-2 rounded-2xl border border-slate-700 bg-slate-900/40 p-5 space-y-4">
        <h3 class="text-lg font-semibold text-white">Thông tin cá nhân</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl border border-slate-700 bg-slate-950/40 p-3">
                <p class="text-sm text-slate-400">Họ và tên</p>
                <p class="text-slate-100 mt-1">{{ $profile['name'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-700 bg-slate-950/40 p-3">
                <p class="text-sm text-slate-400">Email</p>
                <p class="text-slate-100 mt-1">{{ $profile['email'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-700 bg-slate-950/40 p-3">
                <p class="text-sm text-slate-400">Số điện thoại</p>
                <p class="text-slate-100 mt-1">{{ $profile['phone'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-700 bg-slate-950/40 p-3">
                <p class="text-sm text-slate-400">Ngày sinh</p>
                <p class="text-slate-100 mt-1">{{ $profile['dob'] }}</p>
            </div>
        </div>
        <div class="rounded-xl border border-slate-700 bg-slate-950/40 p-3">
            <p class="text-sm text-slate-400">Giới thiệu</p>
            <p class="text-slate-200 mt-1">{{ $profile['bio'] }}</p>
        </div>
    </div>

    <div class="xl:col-span-1 rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-lg font-semibold text-white">Liên kết mạng xã hội</h3>
        <div class="mt-4 space-y-2">
            @foreach ($profile['socials'] as $social)
                <a href="{{ $social['url'] }}" class="rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-300 hover:bg-slate-800 transition-colors block">
                    {{ $social['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>

