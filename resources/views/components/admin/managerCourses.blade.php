@extends('components.admin.adminDashboard')

@section('title', 'Quản lý khóa học')

@section('content')
    <!-- Gọi Modal chi tiết -->
    @include('components.admin.modalDetailCourse')

    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Quản lý khóa học</h1>
            <p class="text-sm text-slate-400">Dữ liệu được cập nhật từ hệ thống</p>
        </div>
        <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-500/20 transition-all">
            + Thêm khóa học
        </button>
    </header>

    <div class="bg-[#1e293b] rounded-2xl border border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/50 text-slate-400 text-[10px] uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Giảng viên</th>
                        <th class="px-6 py-4 font-semibold text-center">Số lớp</th>
                        <th class="px-6 py-4 font-semibold">Giá</th>
                        <th class="px-6 py-4 font-semibold text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="course-table-body" class="divide-y divide-slate-700">
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500 italic">Đang đồng bộ dữ liệu...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="pagination-info" class="p-4 border-t border-slate-700 bg-slate-800/20 text-xs flex justify-between items-center text-slate-400">
            <!-- Pagination đổ vào đây -->
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const API_URL = '/admin/courses/api/list';
            const tableBody = document.getElementById('course-table-body');
            const detailModal = document.getElementById('course-detail-modal');

            // 1. Hàm hiển thị Modal chi tiết (Đã sửa lỗi trùng lặp và khớp với DB)
            window.showCourseDetail = function(courseEncoded) {
                const course = JSON.parse(decodeURIComponent(courseEncoded));
                
                document.getElementById('detail-title').innerText = course.name || 'Chi tiết khóa học';
                document.getElementById('detail-instructor').innerText = "Giảng viên: " + course.instructor;
                
                const classesBody = document.getElementById('detail-classes-body');
                classesBody.innerHTML = '';

                if (course.classes && course.classes.length > 0) {
                    course.classes.forEach(c => {
                        // Badge màu sắc dựa trên status từ database
                        const statusColor = c.status === 'upcoming' ? 'text-blue-400' : 'text-emerald-400';
                        
                        classesBody.innerHTML += `
                            <tr class="text-xs text-slate-300 hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 font-mono text-emerald-500">${c.code || 'N/A'}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-200">${c.name}</div>
                                    <div class="text-[10px] ${statusColor} italic font-medium">Khai giảng: ${c.start_date || 'TBA'}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold text-slate-200">${c.current_students || 0}</span> 
                                    <span class="text-slate-500 mx-0.5">/</span> 
                                    <span class="text-slate-400">${c.capacity || 0}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-400">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        ${c.location || 'Chưa xác định'}
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    classesBody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 italic">Khóa học này chưa mở lớp nào.</td></tr>';
                }

                detailModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            // 2. Hàm đóng Modal
            window.closeDetailModal = function() {
                detailModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            };

            // 3. Hàm tải danh sách khóa học từ API
            window.loadCourses = function(url) {
                tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-slate-500 italic">Đang tải dữ liệu...</td></tr>';
                
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(res => {
                        tableBody.innerHTML = '';
                        if (res.data && res.data.length > 0) {
                            res.data.forEach(course => {
                                const courseData = encodeURIComponent(JSON.stringify(course));
                                
                                const row = `
                                <tr onclick="showCourseDetail('${courseData}')" class="hover:bg-slate-800/60 cursor-pointer transition-all group">
                                    <td class="px-6 py-4 text-sm text-slate-500 font-mono">#${course.id}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-200 group-hover:text-emerald-400 transition-colors">${course.instructor}</td>
                                    <td class="px-6 py-4 text-center text-sm text-slate-300">${course.class_count}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-white">${course.price}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 rounded text-[10px] font-bold uppercase border border-emerald-500/20">
                                            ${course.status}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="event.stopPropagation();" class="p-1.5 hover:bg-slate-700 rounded text-slate-400 hover:text-white transition-colors text-xs font-medium">
                                            Sửa
                                        </button>
                                    </td>
                                </tr>`;
                                tableBody.insertAdjacentHTML('beforeend', row);
                            });
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-slate-500">Không có dữ liệu khóa học.</td></tr>';
                        }
                    })
                    .catch(err => {
                        console.error("Lỗi:", err);
                        tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-red-400">Lỗi đồng bộ dữ liệu. Vui lòng thử lại.</td></tr>';
                    });
            };

            // Khởi chạy lấy dữ liệu
            loadCourses(API_URL);
        });
    </script>
@endpush