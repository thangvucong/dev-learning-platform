@extends('components.admin.adminDashboard')

@section('title', 'Lớp học của tôi')

@section('content')
    @php
        $statusMap = [
            'upcoming' => ['label' => 'Sắp diễn ra', 'class' => 'bg-blue-500/10 text-blue-300 border-blue-500/30'],
            'ongoing' => ['label' => 'Đang dạy', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30'],
            'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-slate-500/10 text-slate-300 border-slate-500/30'],
            'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-red-500/10 text-red-300 border-red-500/30'],
        ];
    @endphp

    <div class="space-y-5">
        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white">Lớp học của tôi</h1>
                    <p class="mt-1 text-sm text-slate-400">Theo dõi lớp được phân công, học viên, lịch học và điểm danh.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    @include('components.student.stat-card', [
                        'title' => 'Tổng lớp',
                        'value' => $stats['total'],
                        'suffix' => '',
                        'icon' => 'fa-solid fa-chalkboard',
                        'tone' => 'blue',
                    ])
                    @include('components.student.stat-card', [
                        'title' => 'Đang dạy',
                        'value' => $stats['ongoing'],
                        'suffix' => '',
                        'icon' => 'fa-solid fa-person-chalkboard',
                        'tone' => 'emerald',
                    ])
                    @include('components.student.stat-card', [
                        'title' => 'Sắp diễn ra',
                        'value' => $stats['upcoming'],
                        'suffix' => '',
                        'icon' => 'fa-regular fa-clock',
                        'tone' => 'violet',
                    ])
                    @include('components.student.stat-card', [
                        'title' => 'Hoàn thành',
                        'value' => $stats['completed'],
                        'suffix' => '',
                        'icon' => 'fa-solid fa-circle-check',
                        'tone' => 'amber',
                    ])
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827] p-4">
            <form method="GET" action="{{ route('teacher.classes.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-4">
                    <label class="text-sm text-slate-300 mb-2 block">Tìm lớp</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                        placeholder="Tên lớp, mã lớp, khóa học..."
                        class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-slate-300 mb-2 block">Trạng thái</label>
                    <select name="status"
                        class="w-full h-11 rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 px-4 focus:outline-none focus:border-emerald-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="ongoing" @selected(($filters['status'] ?? '') === 'ongoing')>Đang dạy</option>
                        <option value="upcoming" @selected(($filters['status'] ?? '') === 'upcoming')>Sắp diễn ra</option>
                        <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Hoàn thành</option>
                        <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Đã hủy</option>
                    </select>
                </div>
                <div class="md:col-span-6 flex items-center gap-2">
                    <button type="submit"
                        class="h-10 px-4 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">
                        Áp dụng
                    </button>
                    <a href="{{ route('teacher.classes.index') }}"
                        class="h-10 px-4 rounded-xl border border-slate-600 text-slate-300 text-sm font-semibold hover:bg-slate-700 inline-flex items-center">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <thead class="bg-slate-900/60 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Lớp</th>
                            <th class="px-5 py-3">Mã</th>
                            <th class="px-5 py-3">Học viên</th>
                            <th class="px-5 py-3">Buổi học</th>
                            <th class="px-5 py-3">Tiến độ</th>
                            <th class="px-5 py-3">Điểm danh</th>
                            <th class="px-5 py-3">Buổi tới</th>
                            <th class="px-5 py-3">Trạng thái</th>
                            <th class="px-5 py-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($classes as $classItem)
                            @php
                                $status = $statusMap[$classItem['status']] ?? $statusMap['ongoing'];
                            @endphp
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $classItem['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $classItem['course_name'] }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-300">{{ $classItem['code'] }}</td>
                                <td class="px-5 py-4 text-sm text-slate-300">{{ $classItem['students_count'] }}</td>
                                <td class="px-5 py-4 text-sm text-slate-300">{{ $classItem['sessions_count'] }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-20 rounded-full bg-slate-700 overflow-hidden">
                                            <div class="h-full bg-emerald-400"
                                                style="width: {{ (int) $classItem['progress'] }}%"></div>
                                        </div>
                                        <span class="text-xs text-slate-300">{{ (int) $classItem['progress'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-300">{{ (int) $classItem['attendance_rate'] }}%
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-300">{{ $classItem['next_session'] }}</td>
                                <td class="px-5 py-4">
                                    <span
                                        class="rounded-full border px-2 py-1 text-xs font-semibold {{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('teacher.classes.show', ['courseClass' => $classItem['id']]) }}"
                                        class="inline-flex h-9 items-center rounded-lg bg-emerald-500 px-3 text-sm font-semibold text-white hover:bg-emerald-600">
                                        Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center text-slate-400">Không có lớp học phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
