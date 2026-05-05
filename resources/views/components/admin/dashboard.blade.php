<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
       
        .sidebar-scroll { height: calc(100vh - 100px); overflow-y: auto; }
    </style>
</head>
<body class="bg-[#0f172a] text-white">

    <div class="flex min-h-screen">
        <!-- 1. SIDEBAR FIXED -->
        <aside class="w-64 bg-[#1e293b] border-r border-slate-700 fixed h-full z-50">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-10">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg"></div>
                    <span class="text-xl font-bold tracking-tight text-white">Manager Admin</span>
                </div>

                <nav class="space-y-4">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-4">Hệ thống</p>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 bg-emerald-500/10 text-emerald-500 rounded-lg font-medium">
                            Dashboard
                        </a>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-4">Quản lý</p>
                     <div class="space-y-1">
  
    <a href="#" class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
        Quản lý người dùng
    </a>

   
    <a href="{{ route('admin.courses.managerCourses') }}" class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
        Quản lý khóa học
    </a>

   
    <a href="{{ Route::has('admin.classes.managerClasses') ? route('admin.classes.managerClasses') : '#' }}" 
       class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
        Quản lý lớp học
    </a>
</div>
                    </div>
                </nav>
            </div>

            <!-- User Info Sidebar - Cố định ở đáy sidebar -->
            <div class="absolute bottom-0 left-0 right-0 p-4 bg-[#1e293b] border-t border-slate-700">
                <div class="bg-slate-800/50 rounded-xl p-3 flex items-center gap-3 border border-slate-700">
                    <div class="w-9 h-9 rounded-full bg-emerald-500 flex items-center justify-center font-bold text-white">Q</div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold truncate text-white">Nguyễn Tiến Quang</p>
                        <p class="text-[10px] text-slate-400 uppercase font-medium">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- 2. MAIN CONTENT - Cần margin-left để không bị sidebar đè -->
        <main class="flex-1 ml-64 p-8">
            <!-- Header -->
            <header class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-white">Tổng quan hệ thống</h1>
                <div class="text-sm text-slate-400">04 tháng 05, 2026</div>
            </header>

            <!-- Banner -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-500 rounded-3xl p-8 mb-8 shadow-lg shadow-emerald-500/10">
                <h2 class="text-3xl font-bold mb-2">Chào mừng trở lại! 👋</h2>
                <p class="text-emerald-50 mb-6 opacity-90">Hệ thống đang hoạt động ổn định. Chúc bạn một ngày tốt lành.</p>
                <button class="bg-white text-emerald-600 px-6 py-2.5 rounded-xl font-bold hover:bg-emerald-50 transition-colors">Xem báo cáo</button>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-700">
                    <p class="text-slate-400 text-sm mb-2">Tổng người dùng</p>
                    <h3 id="total-users" class="text-3xl font-bold">...</h3>
                </div>
                <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-700">
                    <p class="text-slate-400 text-sm mb-2">Tổng khóa học</p>
                    <h3 id="total-courses" class="text-3xl font-bold">...</h3>
                </div>
                <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-700">
                    <p class="text-slate-400 text-sm mb-2">Đang trực tuyến</p>
                    <h3 id="online-users" class="text-3xl font-bold text-emerald-500">...</h3>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-[#1e293b] rounded-2xl border border-slate-700 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800/20">
                    <h3 class="font-bold">Người dùng mới đăng ký</h3>
                  
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-800/50 text-slate-400 text-[10px] uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Người dùng</th>
                                <th class="px-6 py-4 font-semibold text-center">Vai trò</th>
                                <th class="px-6 py-4 font-semibold text-right">Ngày đăng ký</th>
                            </tr>
                        </thead>
                        <tbody id="user-table-body" class="divide-y divide-slate-700">
                            <!-- Dữ liệu từ JS sẽ đổ vào đây -->
                            <tr><td colspan="3" class="px-6 py-10 text-center text-slate-500">Đang đồng bộ dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
       document.addEventListener('DOMContentLoaded', function() {
   
    const API_URL = '/admin/courses/api/list';

    function loadCourses(url) {
        const tableBody = document.getElementById('course-table-body');
        const paginationInfo = document.getElementById('pagination-info');


        tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500 italic animate-pulse">Đang đồng bộ dữ liệu khóa học...</td></tr>';

        fetch(url)
            .then(response => response.json())
            .then(res => {
                
                if (res.data && res.data.length > 0) {
                    tableBody.innerHTML = ''; 
                    
                    res.data.forEach(course => {
                        // Logic xử lý màu sắc cho Level
                        let levelColor = "text-slate-300";
                        if (course.level === "Advanced") levelColor = "text-orange-400";
                        if (course.level === "Intermediate") levelColor = "text-blue-400";
                        if (course.level === "Beginner") levelColor = "text-emerald-400";

                        const row = `
                            <tr class="hover:bg-slate-800/40 transition-colors group">
                                <td class="px-6 py-4 text-sm font-medium text-slate-500">#${course.id}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-[11px] font-bold border border-emerald-500/20">
                                            ${course.instructor ? course.instructor.split(' ').map(n => n[0]).join('') : '??'}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-200">${course.name || 'Chưa đặt tên'}</p>
                                            <p class="text-[10px] text-slate-500">${course.instructor}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm ${levelColor} font-medium">${course.level}</td>
                                <td class="px-6 py-4 text-center text-sm text-slate-300">${course.class_count} lớp</td>
                                <td class="px-6 py-4 text-sm font-bold text-white">${course.price}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 rounded text-[10px] font-bold uppercase border border-emerald-500/20">
                                        ${course.status}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button class="p-2 hover:bg-red-500/10 rounded-lg text-slate-400 hover:text-red-500 transition-all">
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

                  
                    renderPagination(res);
                } else {
                    tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500">Không tìm thấy khóa học nào.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-10 text-center text-red-400 font-medium">Lỗi kết nối API!</td></tr>';
            });
    }

    function renderPagination(res) {
        const paginationInfo = document.getElementById('pagination-info');
        
       
        let buttonsHtml = '';
        res.links.forEach(link => {
            const isActive = link.active ? 'bg-emerald-500 text-white font-bold' : 'bg-slate-800 text-slate-400 hover:bg-slate-700';
            const isDisabled = link.url === null ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
            
            
            let label = link.label;
            if(label.includes('Previous')) label = 'Trước';
            if(label.includes('Next')) label = 'Sau';

            buttonsHtml += `
                <button 
                    ${link.url ? `onclick="window.loadCourses('${link.url}')"` : ''} 
                    class="px-3 py-1.5 rounded-lg text-[11px] transition-all border border-slate-700 ${isActive} ${isDisabled}">
                    ${label}
                </button>
            `;
        });

        paginationInfo.innerHTML = `
            <span class="text-slate-500">Hiển thị từ <b>${res.from || 0}</b> đến <b>${res.to || 0}</b> trong tổng số <b>${res.total}</b> khóa học</span>
            <div class="flex gap-1.5">
                ${buttonsHtml}
            </div>
        `;
    }
    window.loadCourses = loadCourses;
    loadCourses(API_URL);
});
    </script>
</body>
</html>