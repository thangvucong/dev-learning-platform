@extends('components.admin.adminDashboard')

@section('title', 'Quản lý lớp học')

@section('content')
    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Quản lý lớp học</h1>
            <p class="text-sm text-slate-400">Danh sách các lớp học đang diễn ra và sắp tới</p>
        </div>
        <button class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-emerald-500 transition-all flex items-center gap-2 shadow-lg shadow-emerald-900/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tạo lớp mới
        </button>
    </header>

    <!-- Filter & Search Bar -->
    <div class="mb-6 flex flex-wrap gap-4">
        <div class="flex-1 min-w-[300px]">
            <input type="text" id="search-input" placeholder="Tìm kiếm tên lớp, mã lớp..." 
                class="w-full bg-[#1e293b] border border-slate-700 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500 transition-all">
        </div>
        <select id="status-filter" class="bg-[#1e293b] border border-slate-700 text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
            <option value="">Tất cả trạng thái</option>
            <option value="upcoming">Sắp tới</option>
            <option value="ongoing">Đang diễn ra</option>
            <option value="completed">Đã kết thúc</option>
        </select>
    </div>

    <!-- Classes Table -->
    <div class="bg-[#1e293b] rounded-2xl border border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/50 text-slate-400 text-[10px] uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Lớp học</th>
                        <th class="px-6 py-4 font-semibold text-center">Hình thức</th>
                        <th class="px-6 py-4 font-semibold text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold text-center">Sức chứa</th>
                        <th class="px-6 py-4 font-semibold">Thời gian</th>
                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="class-table-body" class="divide-y divide-slate-700">
                    <!-- Data will be injected here via JavaScript -->
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-500 italic">
                            Đang tải dữ liệu lớp học...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Container -->
    <div id="pagination-container" class="mt-6 flex justify-between items-center">
        <!-- Pagination info and buttons will be injected here -->
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const API_URL = "{{ route('admin.classes.api.list') }}";
        const tableBody = document.getElementById('class-table-body');

        function fetchClasses(page = 1) {
            // Loading state
            tableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500 italic">Đang tải dữ liệu...</td></tr>`;

            fetch(`${API_URL}?page=${page}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(response => {
                    tableBody.innerHTML = '';
                    const classes = response.data;

                    if (classes.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500">Không tìm thấy lớp học nào.</td></tr>`;
                        return;
                    }

                    classes.forEach(item => {
                        // Color Logic for Status
                        let statusBadge = '';
                        switch(item.status) {
                            case 'upcoming':
                                statusBadge = '<span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded-md text-[10px] font-bold uppercase">Sắp tới</span>';
                                break;
                            case 'ongoing':
                                statusBadge = '<span class="px-2 py-1 bg-amber-500/10 text-amber-500 rounded-md text-[10px] font-bold uppercase">Đang học</span>';
                                break;
                            default:
                                statusBadge = `<span class="px-2 py-1 bg-blue-500/10 text-blue-400 rounded-md text-[10px] font-bold uppercase">${item.status}</span>`;
                        }

                        // Color Logic for Mode
                        const modeBadge = item.mode === 'online' 
                            ? '<span class="px-2 py-1 bg-indigo-500/10 text-indigo-400 rounded-md text-[10px] font-bold uppercase">Online</span>'
                            : '<span class="px-2 py-1 bg-slate-700 text-slate-300 rounded-md text-[10px] font-bold uppercase">Offline</span>';

                        const row = `
                            <tr class="hover:bg-slate-800/40 transition-colors group">
                                <td class="px-6 py-4 text-sm text-slate-500 font-mono">${item.id}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-200 group-hover:text-emerald-400 transition-colors">${item.name}</span>
                                        <span class="text-[11px] text-slate-500 font-mono uppercase tracking-tighter">Mã: ${item.code}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">${modeBadge}</td>
                                <td class="px-6 py-4 text-center">${statusBadge}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-slate-300 font-semibold">${item.capacity} học viên</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col text-[11px]">
                                        <div class="flex items-center gap-1 text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            ${item.start_at || 'Chưa cập nhật'}
                                        </div>
                                        <span class="text-slate-600 text-[10px] mt-0.5">${item.location || 'N/A'}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button title="Chỉnh sửa" class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-all">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <button title="Xóa lớp" class="p-2 hover:bg-red-500/10 rounded-lg text-slate-400 hover:text-red-500 transition-all">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                    
                    renderPagination(response);
                })
                .catch(err => {
                    console.error("Lỗi khi load danh sách lớp:", err);
                    tableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-10 text-center text-red-400 font-medium">Lỗi hệ thống: Không thể tải dữ liệu.</td></tr>`;
                });
        }

        function renderPagination(data) {
            const container = document.getElementById('pagination-container');
            if (!data.links || data.total <= data.per_page) {
                container.innerHTML = '';
                return;
            }
            
            // Đơn giản hóa: Hiển thị info và nút Prev/Next
            container.innerHTML = `
                <p class="text-xs text-slate-500">Hiển thị ${data.from}-${data.to} trong tổng số ${data.total} lớp học</p>
                <div class="flex gap-2">
                    <button onclick="changePage(${data.current_page - 1})" ${data.current_page === 1 ? 'disabled' : ''} 
                        class="px-3 py-1.5 bg-[#1e293b] border border-slate-700 rounded-lg text-slate-400 hover:text-white disabled:opacity-50 transition-all text-xs">
                        Trước
                    </button>
                    <button onclick="changePage(${data.current_page + 1})" ${data.current_page === data.last_page ? 'disabled' : ''} 
                        class="px-3 py-1.5 bg-[#1e293b] border border-slate-700 rounded-lg text-slate-400 hover:text-white disabled:opacity-50 transition-all text-xs">
                        Sau
                    </button>
                </div>
            `;
        }

        window.changePage = function(page) {
            if (page < 1) return;
            fetchClasses(page);
        };

        // Initial fetch
        fetchClasses();
    });
</script>
@endpush