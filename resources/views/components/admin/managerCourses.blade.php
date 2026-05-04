<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khóa học | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0f172a] text-white">

    <div class="flex min-h-screen">
        <!-- SIDEBAR (Giữ nguyên giao diện) -->
        <aside class="w-64 bg-[#1e293b] border-r border-slate-700 fixed h-full z-50">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-10">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg"></div>
                    <span class="text-xl font-bold tracking-tight text-white">Manager Admin</span>
                </div>
                <nav class="space-y-4">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-4">Hệ thống</p>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg">Dashboard</a>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-4">Quản lý</p>
                        <div class="space-y-1">
                            <a href="#" class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg">Quản lý người dùng</a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 bg-emerald-500/10 text-emerald-500 rounded-lg font-medium">Quản lý khóa học</a>
                        </div>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 ml-64 p-8">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold">Quản lý khóa học</h1>
                    <p class="text-sm text-slate-400">Dữ liệu được cập nhật từ hệ thống</p>
                </div>
                <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-500/20">
                    + Thêm khóa học
                </button>
            </header>

            <!-- Table -->
            <div class="bg-[#1e293b] rounded-2xl border border-slate-700 overflow-hidden">
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
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500 italic">Đang tải dữ liệu khóa học...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Info -->
                <div id="pagination-info" class="p-4 border-t border-slate-700 bg-slate-800/20 text-xs text-slate-500 flex justify-between items-center">
                    <!-- Sẽ đổ thông tin trang vào đây -->
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
           
            const API_URL = 'http://localhost:8088/admin/courses/api/list';

            fetch(API_URL)
                .then(response => response.json())
                .then(res => {
                    const tableBody = document.getElementById('course-table-body');
                    const paginationInfo = document.getElementById('pagination-info');
             
                    tableBody.innerHTML = '';

                    if (res.data && res.data.length > 0) {
                        res.data.forEach(course => {
                     
                            let levelClass = "text-slate-300";
                            if(course.level === "Advanced") levelClass = "text-orange-400";
                            if(course.level === "Beginner") levelClass = "text-emerald-400";

                            const row = `
                                <tr class="hover:bg-slate-800/40 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-500">#${course.id}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-[10px] font-bold">
                                                ${course.instructor.split(' ').map(n => n[0]).join('')}
                                            </div>
                                            <span class="text-sm font-semibold text-slate-200">${course.instructor}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm ${levelClass}">${course.level}</td>
                                    <td class="px-6 py-4 text-center text-sm text-slate-300">${course.class_count}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-white">${course.price}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 rounded text-[10px] font-bold uppercase border border-emerald-500/20">
                                            ${course.status}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button class="p-1.5 hover:bg-slate-700 rounded text-slate-400 hover:text-white transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="p-1.5 hover:bg-red-500/10 rounded text-slate-400 hover:text-red-500 transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tableBody.insertAdjacentHTML('beforeend', row);
                        });

                    
                        paginationInfo.innerHTML = `
                            <span>Hiển thị ${res.from} đến ${res.to} trong tổng số ${res.total} khóa học</span>
                            <div class="flex gap-2">
                                <button class="px-2 py-1 bg-slate-700 rounded ${!res.prev_page_url ? 'opacity-50 cursor-not-allowed' : ''}">Trước</button>
                                <button class="px-2 py-1 bg-emerald-500 rounded font-bold">${res.current_page}</button>
                                <button class="px-2 py-1 bg-slate-700 rounded ${!res.next_page_url ? 'opacity-50 cursor-not-allowed' : ''}">Sau</button>
                            </div>
                        `;
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500">Không có dữ liệu khóa học nào.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    document.getElementById('course-table-body').innerHTML = 
                        '<tr><td colspan="7" class="px-6 py-10 text-center text-red-400 font-medium">Không thể kết nối đến máy chủ API!</td></tr>';
                });
        });
    </script>
</body>
</html>