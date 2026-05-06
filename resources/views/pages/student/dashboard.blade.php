@extends('components.admin.adminDashboard')

@section('title', 'Dashboard học tập')

@section('content')
    <div class="space-y-8">
        <section
            class="rounded-3xl border border-slate-700 bg-gradient-to-r from-[#111827] via-[#0f172a] to-[#1e293b] p-6 md:p-8">
            <div class="flex flex-wrap items-start justify-between gap-5">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="h-14 w-14 rounded-2xl overflow-hidden bg-slate-800 border border-slate-700 shrink-0">
                        @if (!empty($welcome['avatar']))
                            <img src="{{ $welcome['avatar'] }}" alt="{{ $welcome['name'] }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-emerald-300 text-lg font-bold">
                                {{ strtoupper(substr((string) ($welcome['name'] ?? 'U'), 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">{{ $welcome['greeting'] ?? 'Xin chào' }}</p>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $welcome['name'] ?? 'Student' }}</h1>
                        <p class="text-sm text-slate-300 mt-1">Hôm nay bạn có {{ (int) ($welcome['today_classes'] ?? 0) }}
                            buổi học trong lịch.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach ($stats as $stat)
                @include('components.student.stat-card', [
                    'title' => $stat['title'],
                    'value' => $stat['value'],
                    'suffix' => $stat['suffix'],
                    'icon' => $stat['icon'],
                    'tone' => $stat['tone'],
                ])
            @endforeach
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-5 gap-6">
            <div class="xl:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Lịch học hôm nay</h2>
                    <span class="text-xs text-slate-400">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="space-y-3">
                    @forelse ($today_schedule as $schedule)
                        @include('components.student.schedule-card', [
                            'className' => $schedule['class_name'],
                            'courseName' => $schedule['course_name'],
                            'teacherName' => $schedule['teacher_name'],
                            'startTime' => $schedule['start_time'],
                            'endTime' => $schedule['end_time'],
                            'location' => $schedule['location'],
                            'status' => $schedule['status'],
                        ])
                    @empty
                        <div
                            class="rounded-2xl border border-dashed border-slate-600 bg-[#111827] p-8 text-center text-slate-400">
                            <i class="fa-regular fa-calendar-xmark text-2xl mb-3"></i>
                            <p>Hôm nay chưa có buổi học nào. Bạn có thể xem các lớp sắp tới bên dưới.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="xl:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Khóa học đang học</h2>
                </div>
                <div class="space-y-3">
                    @forelse ($learning_courses as $course)
                        @include('components.student.progress-card', [
                            'title' => $course['title'],
                            'teacher' => $course['teacher'],
                            'thumbnail' => $course['thumbnail'],
                            'status' => $course['status'],
                            'progress' => $course['progress'],
                        ])
                    @empty
                        <div
                            class="rounded-2xl border border-dashed border-slate-600 bg-[#111827] p-8 text-center text-slate-400">
                            <i class="fa-regular fa-folder-open text-2xl mb-3"></i>
                            <p>Bạn chưa có khóa học nào đang theo học.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Lớp học sắp tới</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($upcoming_classes as $classCard)
                    @include('components.student.class-card', [
                        'name' => $classCard['name'],
                        'course' => $classCard['course'],
                        'teacher' => $classCard['teacher'],
                        'nextSession' => $classCard['next_session'],
                        'thumbnail' => $classCard['thumbnail'],
                        'progress' => $classCard['progress'],
                    ])
                @empty
                    <div
                        class="md:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-slate-600 bg-[#111827] p-8 text-center text-slate-400">
                        <i class="fa-regular fa-clock text-2xl mb-3"></i>
                        <p>Hiện chưa có lớp học sắp tới.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
