@extends('components.admin.adminDashboard')

@section('title', 'Dashboard giảng viên')

@section('content')
    @php
        $statusMap = [
            'upcoming' => ['label' => 'Sắp diễn ra', 'class' => 'bg-blue-500/10 text-blue-300 border-blue-500/30'],
            'live' => ['label' => 'Đang dạy', 'class' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'],
            'completed' => ['label' => 'Đã kết thúc', 'class' => 'bg-slate-500/10 text-slate-300 border-slate-500/30'],
            'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-red-500/10 text-red-300 border-red-500/30'],
            'ongoing' => ['label' => 'Đang học', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30'],
        ];
        $toneMap = [
            'emerald' => 'border-emerald-500/30 text-emerald-300 bg-emerald-500/10',
            'blue' => 'border-blue-500/30 text-blue-300 bg-blue-500/10',
            'violet' => 'border-violet-500/30 text-violet-300 bg-violet-500/10',
            'amber' => 'border-amber-500/30 text-amber-300 bg-amber-500/10',
            'red' => 'border-red-500/30 text-red-300 bg-red-500/10',
        ];
        $next = $next_session;
    @endphp

    <div class="space-y-5">
        <section class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider text-emerald-300">Buổi tiếp theo</p>
                    @if ($next)
                        <div class="mt-2 min-w-0">
                            <h2 class="text-lg font-semibold text-white">{{ $next['title'] }}</h2>
                            <p class="mt-1 text-sm text-slate-300">{{ $next['class_name'] }}</p>
                            <p class="mt-2 text-sm text-emerald-200">
                                <i class="fa-regular fa-clock mr-1"></i>
                                {{ optional($next['start_at'])->format('d/m/Y') }} · {{ $next['time'] }}
                            </p>
                        </div>
                    @else
                        <p class="mt-3 text-sm text-slate-300">Chưa có buổi dạy sắp tới.</p>
                    @endif
                </div>
                @if ($next)
                    @if (!empty($next['join_url']))
                        <a href="{{ $next['join_url'] }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-500 px-4 text-sm font-semibold text-white hover:bg-emerald-600">
                            Vào lớp
                        </a>
                    @else
                        <span
                            class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-600 px-4 text-sm font-semibold text-slate-400">
                            Chưa có link
                        </span>
                    @endif
                @endif
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-12 gap-4">
            <div class="xl:col-span-8 rounded-2xl border border-slate-700 bg-[#111827] p-5">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Lịch hôm nay</h2>
                        <p class="text-sm text-slate-400">{{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($today_sessions as $session)
                        @php
                            $status = $statusMap[$session['status']] ?? $statusMap['upcoming'];
                        @endphp
                        <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-semibold text-white">{{ $session['title'] }}</h3>
                                        <span
                                            class="rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-400">{{ $session['class_name'] }}</p>
                                    <div class="mt-2 flex flex-wrap gap-3 text-xs text-slate-300">
                                        <span><i class="fa-regular fa-clock mr-1"></i>{{ $session['time'] }}</span>
                                        <span><i
                                                class="fa-solid fa-location-dot mr-1"></i>{{ $session['meeting_info'] }}</span>
                                        <span><i
                                                class="fa-solid fa-clipboard-check mr-1"></i>{{ $session['attendance_label'] }}
                                            điểm danh</span>
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    @if (!empty($session['join_url']))
                                        <a href="{{ $session['join_url'] }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex h-9 items-center rounded-lg bg-emerald-500 px-3 text-sm font-semibold text-white hover:bg-emerald-600">
                                            Vào lớp
                                        </a>
                                    @endif
                                    <button type="button" disabled
                                        class="inline-flex h-9 items-center rounded-lg border border-slate-600 px-3 text-sm font-semibold text-slate-400">
                                        Điểm danh
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-xl border border-dashed border-slate-600 bg-slate-900/40 p-8 text-center text-slate-400">
                            Hôm nay chưa có buổi dạy.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="xl:col-span-4 rounded-2xl border border-slate-700 bg-[#111827] p-5">
                <h2 class="text-lg font-semibold text-white">Cần xử lý</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($action_items as $item)
                        @php
                            $tone = $toneMap[$item['tone']] ?? $toneMap['emerald'];
                        @endphp
                        <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                            <div class="flex items-start gap-3">
                                <span
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border {{ $tone }}">
                                    <i class="{{ $item['icon'] }}"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $item['title'] }}</p>
                                    <p class="mt-1 text-sm text-slate-400">{{ $item['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Lớp đang phụ trách</h2>
                    <p class="text-sm text-slate-400">Ưu tiên các lớp đang hoặc sắp diễn ra.</p>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="text-xs uppercase tracking-wider text-slate-500">
                        <tr class="border-b border-slate-700">
                            <th class="pb-3 font-semibold">Lớp</th>
                            <th class="pb-3 font-semibold">Học viên</th>
                            <th class="pb-3 font-semibold">Buổi học</th>
                            <th class="pb-3 font-semibold">Tiến độ</th>
                            <th class="pb-3 font-semibold">Buổi tới</th>
                            <th class="pb-3 font-semibold">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($classes as $classItem)
                            @php
                                $classStatus = $statusMap[$classItem['status']] ?? $statusMap['ongoing'];
                            @endphp
                            <tr>
                                <td class="py-4">
                                    <p class="text-sm font-semibold text-white">{{ $classItem['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $classItem['course_name'] }}</p>
                                </td>
                                <td class="py-4 text-sm text-slate-300">{{ $classItem['students_count'] }}</td>
                                <td class="py-4 text-sm text-slate-300">{{ $classItem['sessions_count'] }}</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-24 rounded-full bg-slate-700 overflow-hidden">
                                            <div class="h-full bg-emerald-400"
                                                style="width: {{ (int) $classItem['progress'] }}%"></div>
                                        </div>
                                        <span class="text-xs text-slate-300">{{ (int) $classItem['progress'] }}%</span>
                                    </div>
                                </td>
                                <td class="py-4 text-sm text-slate-300">{{ $classItem['next_session'] }}</td>
                                <td class="py-4">
                                    <span
                                        class="rounded-full border px-2 py-1 text-xs font-semibold {{ $classStatus['class'] }}">
                                        {{ $classStatus['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Bạn chưa được phân công lớp học
                                    nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
