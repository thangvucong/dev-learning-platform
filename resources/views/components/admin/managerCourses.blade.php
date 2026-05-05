@extends('components.admin.adminDashboard')

@section('title', 'Quản lý khóa học')

@section('content')
    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold">Quản lý khóa học</h1>
            <p class="text-sm text-slate-400">Dữ liệu được cập nhật từ hệ thống</p>
        </div>
        <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-500/20">
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
                        <th class="px-6 py-4 font-semibold">Trình độ</th>
                        <th class="px-6 py-4 font-semibold text-center">Số lớp</th>
                        <th class="px-6 py-4 font-semibold">Giá</th>
                        <th class="px-6 py-4 font-semibold text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="course-table-body" class="divide-y divide-slate-700">
                    <tr><td colspan="7" class="px-6 py-10 text-center text-slate-500 italic">Đang đồng bộ dữ liệu...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="pagination-info" class="p-4 border-t border-slate-700 bg-slate-800/20 text-xs flex justify-between items-center">
            <!-- Pagination đổ vào đây -->
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const API_URL = '/admin/courses/api/list';

        window.loadCourses = function(url) {
            fetch(url)
                .then(response => response.json())
                .then(res => {
                    const tableBody = document.getElementById('course-table-body');
                    tableBody.innerHTML = '';
                    
                    if (res.data && res.data.length > 0) {
                        res.data.forEach(course => {
                            let levelColor = course.level === "Advanced" ? "text-orange-400" : (course.level === "Beginner" ? "text-emerald-400" : "text-blue-400");
                            const row = `
                                <tr class="hover:bg-slate-800/40 transition-colors group">
                                    <td class="px-6 py-4 text-sm text-slate-500">#${course.id}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-200">${course.instructor}</td>
                                    <td class="px-6 py-4 text-sm ${levelColor}">${course.level}</td>
                                    <td class="px-6 py-4 text-center text-sm text-slate-300">${course.class_count}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-white">${course.price}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 rounded text-[10px] font-bold uppercase border border-emerald-500/20">
                                            ${course.status}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="p-1.5 hover:bg-slate-700 rounded text-slate-400 hover:text-white">Sửa</button>
                                    </td>
                                </tr>`;
                            tableBody.insertAdjacentHTML('beforeend', row);
                        });
                        // Code render pagination bỏ qua để rút gọn...
                    }
                });
        };
        loadCourses(API_URL);
    });
</script>
@endpush