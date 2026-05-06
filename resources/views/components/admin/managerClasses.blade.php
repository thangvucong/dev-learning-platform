@extends('components.admin.adminDashboard')

@section('title', 'Quản lý lớp học')

@section('content')
    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Quản lý lớp học</h1>
            <p class="text-sm text-slate-400">Danh sách các lớp học đang diễn ra và sắp tới</p>
        </div>
        <button id="open-create-class"
            class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-emerald-500 transition-all flex items-center gap-2 shadow-lg shadow-emerald-900/20"
            type="button">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tạo lớp mới
        </button>
    </header>

    <!-- Create class modal -->
    <div id="create-class-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" data-close-create-class></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div
                class="relative bg-[#1e293b] w-full max-w-4xl rounded-2xl border border-slate-700 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
                    <h3 class="text-xl font-bold text-white">Tạo lớp học mới</h3>
                    <button type="button" data-close-create-class class="p-2 text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="p-6">
                    <div id="create-class-error"
                        class="hidden mb-4 rounded-lg border border-red-700 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    </div>

                    <div class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-slate-400 mb-2">Khóa học *</label>
                                <select id="create-class-course-id"
                                    class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                                    <option value="">-- Chọn khóa học --</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-2">Tên lớp *</label>
                                <input id="create-class-name" type="text" required
                                    class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white"
                                    placeholder="Ví dụ: Lớp React căn bản">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-slate-400 mb-2">Mã lớp *</label>
                                <input id="create-class-code" type="text" required
                                    class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white"
                                    placeholder="Ví dụ: REACT-001">
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-2">Hình thức *</label>
                                <select id="create-class-mode"
                                    class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                                    <option value="online" selected>Online</option>
                                    <option value="offline">Offline</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-slate-400 mb-2">Trạng thái *</label>
                                <select id="create-class-status"
                                    class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                                    <option value="upcoming" selected>Sắp tới</option>
                                    <option value="ongoing">Đang diễn ra</option>
                                    <option value="completed">Đã kết thúc</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-2">Sức chứa *</label>
                                <input id="create-class-capacity" type="number" min="0" required value="30"
                                    class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-slate-400 mb-2">Thời gian bắt đầu *</label>
                                <input id="create-class-start-at" type="datetime-local" required
                                    class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-2">Thời gian kết thúc *</label>
                                <input id="create-class-end-at" type="datetime-local" required
                                    class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-700 bg-slate-900/20 p-4">
                            <p class="text-xs uppercase tracking-wider text-slate-400 mb-3">Cấu hình buổi học tự động</p>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs text-slate-400 mb-2">Số buổi</label>
                                    <input id="create-class-sessions-count" type="number" min="1" value="10"
                                        class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-400 mb-2">Giờ bắt đầu</label>
                                    <input id="create-class-session-start-time" type="time" value="19:00"
                                        class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-400 mb-2">Giờ kết thúc</label>
                                    <input id="create-class-session-end-time" type="time" value="21:00"
                                        class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-400 mb-2">Tạo lịch</label>
                                    <select id="create-class-generation-mode"
                                        class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                                        <option value="auto" selected>Tự động (phân bổ đều)</option>
                                        <option value="custom">Tuỳ chỉnh theo thứ học</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-xs text-slate-400 mb-2">Thứ học trong tuần</p>
                                <div class="flex flex-wrap gap-2 text-xs text-slate-300">
                                    @foreach ([1 => 'T2', 2 => 'T3', 3 => 'T4', 4 => 'T5', 5 => 'T6', 6 => 'T7', 7 => 'CN'] as $dayValue => $dayLabel)
                                        <label
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border border-slate-700 bg-slate-900/40">
                                            <input type="checkbox" class="create-class-day-of-week"
                                                value="{{ $dayValue }}"
                                                @if ($dayValue >= 1 && $dayValue <= 5) checked @endif>
                                            <span>{{ $dayLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-3">
                                Chế độ <strong>Auto</strong>: hệ thống phân bổ đều số buổi trong toàn bộ thời gian lớp.
                                Chế độ <strong>Custom</strong>: hệ thống tạo buổi theo các thứ đã chọn và dừng khi đủ số buổi.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Địa điểm</label>
                            <input id="create-class-location" type="text"
                                class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white"
                                placeholder="Tùy chọn">
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-700 bg-slate-800/20 flex justify-end gap-2">
                    <button type="button" data-close-create-class
                        class="px-4 py-2 rounded-lg border border-slate-600 text-slate-300">Hủy</button>
                    <button id="btn-create-class" type="button"
                        class="px-5 py-2 rounded-lg bg-emerald-500 text-white font-semibold">Tạo lớp học</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add students modal -->
    <div id="add-students-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" data-close-add-students></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div
                class="relative bg-[#1e293b] w-full max-w-3xl rounded-2xl border border-slate-700 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
                    <h3 class="text-xl font-bold text-white">Thêm học viên</h3>
                    <button type="button" data-close-add-students class="p-2 text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="p-6 space-y-4">
                    <div id="add-students-error"
                        class="hidden rounded-lg border border-red-700 bg-red-500/10 px-4 py-3 text-sm text-red-200"></div>

                    <div class="bg-slate-900/30 border border-slate-800 rounded-xl p-4">
                        <p class="text-xs text-slate-400 mb-1">Lớp đang chọn</p>
                        <p id="add-students-class-meta" class="text-sm text-slate-200 font-semibold">-</p>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-2">Thêm thủ công (user_id hoặc email, ngăn cách bởi
                            dấu phẩy/dòng mới) *</label>
                        <textarea id="add-students-members" rows="5"
                            class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white"
                            placeholder="Ví dụ:
12
user@example.com"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-2">Import Excel/CSV</label>
                        <input id="add-students-import-file" type="file" accept=".xlsx,.csv"
                            class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                        <p class="text-[11px] text-slate-500 mt-2">Hỗ trợ cột tiêu đề: <code>email</code> hoặc
                            <code>user_id</code>. Nếu không có header, lấy cột A.
                        </p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-700 bg-slate-800/20 flex justify-between gap-2">
                    <button type="button" data-close-add-students
                        class="px-4 py-2 rounded-lg border border-slate-600 text-slate-300">Đóng</button>
                    <div class="flex gap-2">
                        <button id="btn-add-students-manual" type="button"
                            class="px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-200 font-semibold">Thêm
                            thủ công</button>
                        <button id="btn-add-students-import" type="button"
                            class="px-4 py-2 rounded-lg bg-emerald-500 text-white font-semibold">Import</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="mb-6 flex flex-wrap gap-4">
        <div class="flex-1 min-w-[300px]">
            <input type="text" id="search-input" placeholder="Tìm kiếm tên lớp, mã lớp..."
                class="w-full bg-[#1e293b] border border-slate-700 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500 transition-all">
        </div>
        <select id="status-filter"
            class="bg-[#1e293b] border border-slate-700 text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500">
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
            const COURSES_API_URL = '/admin/courses/api/list';
            const tableBody = document.getElementById('class-table-body');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const createClassModal = document.getElementById('create-class-modal');
            const openCreateClassBtn = document.getElementById('open-create-class');
            const createCloseButtons = document.querySelectorAll('[data-close-create-class]');
            const createErrorBox = document.getElementById('create-class-error');

            const addStudentsModal = document.getElementById('add-students-modal');
            const addStudentsClassMeta = document.getElementById('add-students-class-meta');
            const addStudentsCloseButtons = document.querySelectorAll('[data-close-add-students]');
            const addStudentsErrorBox = document.getElementById('add-students-error');
            const addStudentsMembersTextarea = document.getElementById('add-students-members');
            const addStudentsImportFileInput = document.getElementById('add-students-import-file');
            const btnAddStudentsManual = document.getElementById('btn-add-students-manual');
            const btnAddStudentsImport = document.getElementById('btn-add-students-import');

            const btnCreateClass = document.getElementById('btn-create-class');
            const CREATE_URL = "{{ route('admin.classes.store') }}";
            const CLASSES_BASE = '/admin/classes';
            let currentSelectedClassId = null;

            function showError(boxEl, message) {
                if (!boxEl) return;
                boxEl.textContent = message;
                boxEl.classList.remove('hidden');
            }

            function clearError(boxEl) {
                if (!boxEl) return;
                boxEl.textContent = '';
                boxEl.classList.add('hidden');
            }

            function openModal(modalEl) {
                if (!modalEl) return;
                modalEl.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modalEl) {
                if (!modalEl) return;
                modalEl.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            function closeCreateClassModal() {
                closeModal(createClassModal);
                clearError(createErrorBox);
            }

            function closeAddStudentsModal() {
                closeModal(addStudentsModal);
                clearError(addStudentsErrorBox);
            }

            // Load courses for create-class dropdown
            async function loadCoursesForClassSelect() {
                const selectEl = document.getElementById('create-class-course-id');
                if (!selectEl) return;

                try {
                    const res = await fetch(`${COURSES_API_URL}?perPage=1000`);
                    if (!res.ok) throw new Error('Không thể tải danh sách khóa học');
                    const json = await res.json();
                    const courses = json.data || [];

                    selectEl.innerHTML = `<option value="">-- Chọn khóa học --</option>`;
                    courses.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.name || c.title || `Course #${c.id}`;
                        selectEl.appendChild(opt);
                    });
                } catch (e) {
                    // Không chặn page nếu danh sách khóa học không tải được
                    console.error(e);
                }
            }

            if (openCreateClassBtn) {
                openCreateClassBtn.addEventListener('click', function() {
                    clearError(createErrorBox);
                    openModal(createClassModal);
                    loadCoursesForClassSelect();
                });
            }

            createCloseButtons.forEach(function(btn) {
                btn.addEventListener('click', closeCreateClassModal);
            });

            addStudentsCloseButtons.forEach(function(btn) {
                btn.addEventListener('click', closeAddStudentsModal);
            });

            function fetchClasses(page = 1) {
                // Loading state
                tableBody.innerHTML =
                    `<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500 italic">Đang tải dữ liệu...</td></tr>`;

                fetch(`${API_URL}?page=${page}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(response => {
                        tableBody.innerHTML = '';
                        const classes = response.data;

                        if (classes.length === 0) {
                            tableBody.innerHTML =
                                `<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500">Không tìm thấy lớp học nào.</td></tr>`;
                            return;
                        }

                        classes.forEach(item => {
                            // Color Logic for Status
                            let statusBadge = '';
                            switch (item.status) {
                                case 'upcoming':
                                    statusBadge =
                                        '<span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded-md text-[10px] font-bold uppercase">Sắp tới</span>';
                                    break;
                                case 'ongoing':
                                    statusBadge =
                                        '<span class="px-2 py-1 bg-amber-500/10 text-amber-500 rounded-md text-[10px] font-bold uppercase">Đang học</span>';
                                    break;
                                default:
                                    statusBadge =
                                        `<span class="px-2 py-1 bg-blue-500/10 text-blue-400 rounded-md text-[10px] font-bold uppercase">${item.status}</span>`;
                            }

                            // Color Logic for Mode
                            const modeBadge = item.mode === 'online' ?
                                '<span class="px-2 py-1 bg-indigo-500/10 text-indigo-400 rounded-md text-[10px] font-bold uppercase">Online</span>' :
                                '<span class="px-2 py-1 bg-slate-700 text-slate-300 rounded-md text-[10px] font-bold uppercase">Offline</span>';

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
                                    <div class="text-sm text-slate-300 font-semibold">
                                        ${item.current_students || 0}/${item.capacity} học viên
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col text-[11px]">
                                        <div class="flex items-center gap-1 text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            ${item.start_at || 'Chưa cập nhật'}
                                        </div>
                                        <div class="text-slate-600 text-[10px] mt-0.5">
                                            ${item.end_at ? `Kết thúc: ${item.end_at}` : 'Chưa cập nhật'}
                                        </div>
                                        <span class="text-slate-600 text-[10px] mt-0.5">${item.location || 'N/A'}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 opacity-100">
                                        <button
                                            type="button"
                                            data-open-add-students
                                            data-class-id="${item.id}"
                                            class="px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-200 rounded-lg text-xs hover:bg-emerald-500/20 transition-all">
                                            Thêm học viên
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
                        tableBody.innerHTML =
                            `<tr><td colspan="7" class="px-6 py-10 text-center text-red-400 font-medium">Lỗi hệ thống: Không thể tải dữ liệu.</td></tr>`;
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

            // Create class
            if (btnCreateClass) {
                btnCreateClass.addEventListener('click', async function() {
                    clearError(createErrorBox);
                    try {
                        const courseId = document.getElementById('create-class-course-id')?.value;
                        const name = document.getElementById('create-class-name')?.value?.trim();
                        const code = document.getElementById('create-class-code')?.value?.trim();
                        const mode = document.getElementById('create-class-mode')?.value;
                        const status = document.getElementById('create-class-status')?.value;
                        const capacity = document.getElementById('create-class-capacity')?.value;
                        const startAt = document.getElementById('create-class-start-at')?.value;
                        const endAt = document.getElementById('create-class-end-at')?.value;
                        const location = document.getElementById('create-class-location')?.value
                            ?.trim();
                        const sessionsCount = document.getElementById('create-class-sessions-count')
                            ?.value;
                        const sessionStartTime = document.getElementById(
                            'create-class-session-start-time')?.value;
                        const sessionEndTime = document.getElementById('create-class-session-end-time')
                            ?.value;
                        const generationMode = document.getElementById('create-class-generation-mode')
                            ?.value || 'auto';
                        const daysOfWeek = Array.from(document.querySelectorAll(
                                '.create-class-day-of-week:checked'))
                            .map(function(el) {
                                return Number(el.value);
                            });

                        if (!courseId || !name || !code || !startAt || !endAt) {
                            showError(createErrorBox, 'Vui lòng điền đầy đủ thông tin bắt buộc.');
                            return;
                        }

                        if (generationMode === 'custom' && daysOfWeek.length === 0) {
                            showError(createErrorBox,
                                'Bạn đang chọn chế độ tùy chỉnh, vui lòng chọn ít nhất 1 thứ học trong tuần.'
                            );
                            return;
                        }

                        const payload = {
                            course_id: Number(courseId),
                            name,
                            code,
                            mode,
                            status,
                            capacity: Number(capacity || 0),
                            start_at: startAt,
                            end_at: endAt,
                            location: location || null,
                            schedule_config: {
                                generation_mode: generationMode,
                                sessions_count: Number(sessionsCount || 0),
                                days_of_week: daysOfWeek,
                                session_start_time: sessionStartTime || null,
                                session_end_time: sessionEndTime || null
                            }
                        };

                        const res = await fetch(CREATE_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                ...(csrfToken ? {
                                    'X-CSRF-TOKEN': csrfToken
                                } : {})
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json().catch(() => ({}));

                        if (!res.ok) {
                            const message = data?.message || data?.error || 'Tạo lớp thất bại.';
                            showError(createErrorBox, message);
                            return;
                        }

                        closeCreateClassModal();
                        fetchClasses();
                    } catch (e) {
                        console.error(e);
                        showError(createErrorBox, 'Lỗi hệ thống khi tạo lớp.');
                    }
                });
            }

            // Open add-students modal (manual + import)
            tableBody.addEventListener('click', function(event) {
                const btn = event.target.closest('[data-open-add-students]');
                if (!btn) return;

                const classId = btn.getAttribute('data-class-id');
                if (!classId) return;

                currentSelectedClassId = Number(classId);
                addStudentsClassMeta.textContent = `#${currentSelectedClassId}`;
                addStudentsMembersTextarea.value = '';
                addStudentsImportFileInput.value = '';
                clearError(addStudentsErrorBox);

                openModal(addStudentsModal);
            });

            // Manual add students
            if (btnAddStudentsManual) {
                btnAddStudentsManual.addEventListener('click', async function() {
                    clearError(addStudentsErrorBox);
                    if (!currentSelectedClassId) {
                        showError(addStudentsErrorBox, 'Chưa chọn lớp.');
                        return;
                    }

                    const members = (addStudentsMembersTextarea.value || '').trim();
                    if (!members) {
                        showError(addStudentsErrorBox, 'Vui lòng nhập user_id hoặc email để thêm.');
                        return;
                    }

                    try {
                        const res = await fetch(`${CLASSES_BASE}/${currentSelectedClassId}/students`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                ...(csrfToken ? {
                                    'X-CSRF-TOKEN': csrfToken
                                } : {})
                            },
                            body: JSON.stringify({
                                members
                            })
                        });

                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            showError(addStudentsErrorBox, data?.message || 'Không thể thêm học viên.');
                            return;
                        }

                        closeAddStudentsModal();
                        fetchClasses();
                    } catch (e) {
                        console.error(e);
                        showError(addStudentsErrorBox, 'Lỗi hệ thống khi thêm học viên.');
                    }
                });
            }

            // Import students
            if (btnAddStudentsImport) {
                btnAddStudentsImport.addEventListener('click', async function() {
                    clearError(addStudentsErrorBox);
                    if (!currentSelectedClassId) {
                        showError(addStudentsErrorBox, 'Chưa chọn lớp.');
                        return;
                    }

                    const file = addStudentsImportFileInput.files?.[0];
                    if (!file) {
                        showError(addStudentsErrorBox, 'Vui lòng chọn file Excel/CSV.');
                        return;
                    }

                    try {
                        const formData = new FormData();
                        formData.append('file', file);

                        const res = await fetch(
                            `${CLASSES_BASE}/${currentSelectedClassId}/students/import`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    ...(csrfToken ? {
                                        'X-CSRF-TOKEN': csrfToken
                                    } : {})
                                },
                                body: formData
                            });

                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            showError(addStudentsErrorBox, data?.message ||
                                'Không thể import học viên.');
                            return;
                        }

                        closeAddStudentsModal();
                        fetchClasses();
                    } catch (e) {
                        console.error(e);
                        showError(addStudentsErrorBox, 'Lỗi hệ thống khi import.');
                    }
                });
            }
        });
    </script>
@endpush
