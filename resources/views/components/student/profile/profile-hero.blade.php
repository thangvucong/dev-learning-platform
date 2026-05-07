<section class="rounded-3xl border border-slate-700 bg-gradient-to-r from-[#111827] via-[#0f172a] to-[#1e293b] p-5">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
        <div class="flex items-start gap-4 min-w-0">
            <div class="h-16 w-16 rounded-2xl overflow-hidden bg-slate-800 border border-slate-700 shrink-0">
                @if (!empty($profile['avatar']))
                    <img src="{{ $profile['avatar'] }}" alt="{{ $profile['name'] }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-emerald-300 text-lg font-bold">
                        {{ strtoupper(substr((string) $profile['name'], 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-xs uppercase tracking-widest text-slate-400">Hồ sơ học tập</p>
                <h1 class="text-2xl font-bold text-white mt-1 truncate">{{ $profile['name'] }}</h1>
                <p class="text-sm text-slate-300 mt-1 truncate">{{ $profile['email'] }}</p>
                <p class="text-sm text-slate-400 mt-2 max-w-2xl">{{ $profile['bio'] }}</p>
                <p class="text-xs text-slate-500 mt-2">Tham gia từ {{ $profile['join_date'] }}</p>
            </div>
        </div>

        <div class="w-full lg:w-[360px] space-y-3">
            <div class="grid grid-cols-3 gap-2 text-xs">
                <div class="rounded-xl border border-slate-700 bg-slate-900/50 p-3 text-center">
                    <p class="text-slate-400">Chuỗi học</p>
                    <p class="text-white font-semibold mt-1">{{ (int) $profile['learning_streak'] }}d</p>
                </div>
                <div class="rounded-xl border border-slate-700 bg-slate-900/50 p-3 text-center">
                    <p class="text-slate-400">Điểm danh</p>
                    <p class="text-white font-semibold mt-1">{{ (int) $profile['attendance_rate'] }}%</p>
                </div>
                <div class="rounded-xl border border-slate-700 bg-slate-900/50 p-3 text-center">
                    <p class="text-slate-400">Tiến độ</p>
                    <p class="text-white font-semibold mt-1">{{ (int) $profile['overall_progress'] }}%</p>
                </div>
            </div>
            <div class="h-2 rounded-full bg-slate-700 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-400 to-cyan-400"
                    style="width: {{ (int) $profile['overall_progress'] }}%"></div>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-300">
                <span>Tổng quan học tập</span>
                <span>{{ (int) $profile['overall_progress'] }}% hoàn thành</span>
            </div>
        </div>
    </div>
</section>
