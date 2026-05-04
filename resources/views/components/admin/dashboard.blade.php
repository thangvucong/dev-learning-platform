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
        /* Đảm bảo sidebar không bị đè */
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
                            <a href="#" class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                                Quản lý khóa học
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
           
            fetch('/api/admin/dashboard-stats') 
                .then(response => response.json())
                .then(res => {
                    if(res.success) {
                        const data = res.data;
                        
                        document.getElementById('total-users').innerText = data.total_users.toLocaleString();
                        document.getElementById('total-courses').innerText = data.total_courses.toLocaleString();
                        document.getElementById('online-users').innerText = data.online_users.toLocaleString();

                        const tableBody = document.getElementById('user-table-body');
                        tableBody.innerHTML = data.recent_users.map(user => `
                            <tr class="hover:bg-slate-800/40 transition-colors group">
                                <td class="px-6 py-4 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-sm font-bold border border-emerald-500/20">
                                        ${user.name.charAt(0).toUpperCase()}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-200">${user.name}</p>
                                        <p class="text-xs text-slate-500 truncate">${user.email}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-0.5 bg-slate-700/50 text-slate-300 rounded text-[10px] font-bold uppercase tracking-tight border border-slate-600/50">
                                        ${user.role}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-400 text-right font-medium">
                                    ${new Date(user.created_at).toLocaleDateString('vi-VN')}
                                </td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    document.getElementById('user-table-body').innerHTML = '<tr><td colspan="3" class="px-6 py-10 text-center text-red-400 font-medium">Lỗi kết nối máy chủ!</td></tr>';
                });
        });
    </script>
</body>
</html>