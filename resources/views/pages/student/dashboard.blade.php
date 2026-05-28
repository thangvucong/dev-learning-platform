@extends('components.admin.adminDashboard')

@section('title', 'Dashboard học tập')

@section('content')
    <div class="space-y-8">
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
                            'joinUrl' => $schedule['join_url'] ?? '',
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
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-4 gap-4">
                @forelse ($upcoming_classes as $classCard)
                    @include('components.student.class-card', [
                        'id' => $classCard['id'],
                        'name' => $classCard['name'],
                        'course' => $classCard['course'],
                        'teacher' => $classCard['teacher'],
                        'nextSession' => $classCard['next_session'],
                        'thumbnail' => $classCard['thumbnail'],
                        'progress' => $classCard['progress'],
                        'status' => $classCard['status'] ?? 'upcoming',
                        'joinUrl' => $classCard['join_url'] ?? '',
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
