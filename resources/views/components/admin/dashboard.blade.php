@extends('components.admin.adminDashboard')

@section('title', 'Tổng quan hệ thống')

@section('content')
    <header class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-white">Tổng quan hệ thống</h1>
        <div class="text-sm text-slate-400">05 tháng 05, 2026</div>
    </header>

    <div class="bg-gradient-to-r from-emerald-600 to-teal-500 rounded-3xl p-8 mb-8 shadow-lg shadow-emerald-500/10">
        <h2 class="text-3xl font-bold mb-2">Chào mừng trở lại! 👋</h2>
        <p class="text-emerald-50 mb-6 opacity-90">Hệ thống đang hoạt động ổn định. Chúc bạn một ngày tốt lành.</p>
        <button class="bg-white text-emerald-600 px-6 py-2.5 rounded-xl font-bold hover:bg-emerald-50 transition-colors">Xem báo cáo</button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-700">
            <p class="text-slate-400 text-sm mb-2">Tổng người dùng</p>
            <h3 id="total-users" class="text-3xl font-bold text-white">...</h3>
        </div>
        <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-700">
            <p class="text-slate-400 text-sm mb-2">Tổng khóa học</p>
            <h3 id="total-courses" class="text-3xl font-bold text-white">...</h3>
        </div>
        <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-700">
            <p class="text-slate-400 text-sm mb-2">Đang trực tuyến</p>
            <h3 id="online-users" class="text-3xl font-bold text-emerald-500">...</h3>
        </div>
    </div>

    <!-- Recent Users Table -->
    <div class="bg-[#1e293b] rounded-2xl border border-slate-700 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800/20">
            <h3 class="font-bold text-white">Người dùng mới đăng ký</h3>
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
                    <tr><td colspan="3" class="px-6 py-10 text-center text-slate-500">Đang đồng bộ dữ liệu...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /** 
             * CẬP NHẬT: Sử dụng helper route() của Laravel để lấy URL chính xác 
             * tương ứng với route 'admin.api.stats' trong web.php
             */
            const API_DASHBOARD = "{{ route('admin.api.stats') }}"; 

            function loadDashboardData() {
                fetch(API_DASHBOARD)
                    .then(response => {
                        // Kiểm tra nếu response không phải JSON (có thể là lỗi 404/500 trả về HTML)
                        if (!response.ok) {
                            throw new Error('Đường dẫn API không tồn tại hoặc có lỗi server (404/500).');
                        }
                        return response.json();
                    })
                    .then(res => {
                        if (res.success) {
                            const data = res.data;
                            
                            // 1. Cập nhật các con số thống kê
                            document.getElementById('total-users').innerText = (data.total_users || 0).toLocaleString();
                            document.getElementById('total-courses').innerText = (data.total_courses || 0).toLocaleString();
                            document.getElementById('online-users').innerText = data.online_users || 0;

                            // 2. Cập nhật bảng người dùng mới
                            const tableBody = document.getElementById('user-table-body');
                            tableBody.innerHTML = '';

                            if (data.recent_users && data.recent_users.length > 0) {
                                data.recent_users.forEach(user => {
                                    const row = `
                                        <tr class="hover:bg-slate-800/40 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-[10px] font-bold">
                                                        ${user.name ? user.name.charAt(0).toUpperCase() : '?'}
                                                    </div>
                                                    <span class="text-sm font-medium text-slate-200">${user.name}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2 py-0.5 bg-slate-700 text-slate-300 rounded text-[10px] uppercase font-bold">
                                                    ${user.role || 'User'}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right text-xs text-slate-500">
                                                ${new Date(user.created_at).toLocaleDateString('vi-VN')}
                                            </td>
                                        </tr>
                                    `;
                                    tableBody.insertAdjacentHTML('beforeend', row);
                                });
                            } else {
                                tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-10 text-center text-slate-500">Không có người dùng mới.</td></tr>';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi Dashboard:', error);
                        document.getElementById('user-table-body').innerHTML = `<tr><td colspan="3" class="px-6 py-10 text-center text-red-400">Lỗi: ${error.message}</td></tr>`;
                    });
            }

            loadDashboardData();
        });
    </script>
@endsection