@extends('components.admin.adminDashboard')

@section('title', 'Teacher Dashboard')

@section('content')
    <div class="space-y-8">
        <section class="rounded-3xl border border-slate-700 bg-gradient-to-r from-[#111827] via-[#0f172a] to-[#1e293b] p-6 md:p-8">
            <div class="flex flex-wrap items-start justify-between gap-5">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="h-14 w-14 rounded-2xl overflow-hidden bg-slate-800 border border-slate-700 shrink-0">
                        @if (!empty($welcome['avatar']))
                            <img src="{{ $welcome['avatar'] }}" alt="{{ $welcome['name'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-emerald-300 text-lg font-bold bg-slate-700">
                                {{ strtoupper(substr((string) ($welcome['name'] ?? 'T'), 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">{{ $welcome['greeting'] ?? 'Xin chào' }}</p>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $welcome['name'] ?? 'Teacher' }}</h1>
                        <p class="text-sm text-slate-300 mt-1">Hôm nay bạn có {{ $welcome['today_classes'] }} buổi dạy trong lịch.</p>
                    </div>
                </div>
                <button class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-plus mr-2"></i> Tạo bài giảng mới
                </button>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach ($stats as $stat)
                @include('components.student.stat-card', $stat)
            @endforeach
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-5 gap-6">
            <div class="xl:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Lịch dạy hôm nay</h2>
                    <span class="text-xs text-slate-400">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="space-y-3" id="today-schedule">
                    <div class="rounded-2xl border border-dashed border-slate-600 bg-[#111827] p-8 text-center text-slate-400">
                        <p>Đang tải lịch dạy...</p>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Lớp học của tôi</h2>
                    <button id="btn-load-classes" class="text-xs text-emerald-400 hover:underline bg-transparent border-none cursor-pointer">
                        Danh sách lớp học
                    </button>
                </div>
                <div class="space-y-3" id="my-classes-list">
                    <div class="p-4 text-slate-500 text-sm italic">Nhấn "Danh sách lớp học" để xem dữ liệu...</div>
                </div>
            </div>
        </section>
    </div>

    <div id="student-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-[#1e293b] border border-slate-700 rounded-3xl w-full max-w-2xl max-h-[80vh] overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
                <h3 id="modal-class-name" class="text-xl font-bold text-white">Chi tiết lớp học</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div id="modal-student-list" class="p-6 overflow-y-auto max-h-[60vh] space-y-4">
                </div>
        </div>
    </div>

    <script>
        let classData = [];

        document.getElementById('btn-load-classes').addEventListener('click', function(e) {
            e.preventDefault();
            const container = document.getElementById('my-classes-list');
            container.innerHTML = '<div class="p-4 text-emerald-400 animate-pulse">Đang tải dữ liệu ...</div>';

            fetch("{{ route('teacher.api.classes') }}")
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        classData = res.data; // Lưu dữ liệu để dùng cho modal
                        renderClasses(res.data);
                    }
                })
                .catch(err => {
                    container.innerHTML = '<p class="text-red-400">Lỗi kết nối API.</p>';
                });
        });

        function renderClasses(data) {
            const container = document.getElementById('my-classes-list');
            if (data.length === 0) {
                container.innerHTML = '<p class="p-4 text-slate-500">Trống.</p>';
                return;
            }

            let html = '';
            data.forEach((item, index) => {
                html += `
                    <div onclick="showStudents(${index})" class="group p-4 rounded-2xl bg-[#1e293b]/50 border border-slate-700 hover:border-emerald-500/50 transition-all mb-3 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <h4 class="text-white font-medium group-hover:text-emerald-400 transition-colors truncate">
                                    ${item.name}
                                </h4>
                                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider font-semibold">
                                    ${item.course_name}
                                </p>
                                <div class="flex items-center mt-2 text-xs text-slate-500">
                                    <i class="fa-solid fa-users mr-1.5"></i>
                                    <span>${item.students.length} sinh viên</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <span class="text-[10px] px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase font-bold">
                                    ${item.status}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        function showStudents(index) {
            const item = classData[index];
            document.getElementById('modal-class-name').innerText = item.name;
            const studentList = document.getElementById('modal-student-list');
            
            if (item.students.length === 0) {
                studentList.innerHTML = '<p class="text-slate-500 text-center py-10">Lớp này chưa có sinh viên đăng ký.</p>';
            } else {
                let html = '';
                item.students.forEach(st => {
                    html += `
                        <div class="flex items-center gap-4 p-3 rounded-xl bg-slate-800/30 border border-slate-700">
                            <img src="${st.avatar || 'https://ui-avatars.com/api/?name='+st.name}" class="h-10 w-10 rounded-full border border-slate-600">
                            <div>
                                <p class="text-white font-medium text-sm">${st.name}</p>
                                <p class="text-xs text-slate-500">${st.email}</p>
                            </div>
                        </div>
                    `;
                });
                studentList.innerHTML = html;
            }
            document.getElementById('student-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('student-modal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('student-modal');
            if (event.target == modal) closeModal();
        }
    </script>
@endsection