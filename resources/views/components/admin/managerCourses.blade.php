@extends('components.admin.adminDashboard')

@section('title', 'Quản lý khóa học')

@section('content')
    @include('components.admin.modalDetailCourse')

    <div id="create-course-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" data-close-create-course></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-[#1e293b] w-full max-w-3xl rounded-2xl border border-slate-700 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
                    <h3 class="text-xl font-bold text-white">Tạo khóa học mới</h3>
                    <button type="button" data-close-create-course class="p-2 text-slate-400 hover:text-white">✕</button>
                </div>
                <form method="POST" action="{{ route('admin.courses.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Tên khóa học *</label>
                            <input id="course-title" name="title" type="text" value="{{ old('title') }}" required class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                            @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Slug</label>
                            <input id="course-slug" name="slug" type="text" value="{{ old('slug') }}" class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white" placeholder=".....">
                            @error('slug') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Giảng viên *</label>
                            <select name="instructor_id" required class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                                <option value="">-- Chọn giảng viên --</option>
                                @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" @selected((string) old('instructor_id') === (string) $instructor->id)>
                                        {{ $instructor->name }} ({{ $instructor->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('instructor_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Trạng thái *</label>
                            <select name="status" required class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                                <option value="0" @selected((string) old('status', '0') === '0')>Ẩn</option>
                                <option value="1" @selected((string) old('status') === '1')>Hiển thị</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Giá bán *</label>
                            <input name="price" type="number" min="0" step="1000" value="{{ old('price', 0) }}" required class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                            @error('price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Thumbnail URL</label>
                            <input name="thumbnail_url" type="url" value="{{ old('thumbnail_url') }}" class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                            @error('thumbnail_url') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Intro video URL</label>
                            <input name="intro_video_url" type="url" value="{{ old('intro_video_url') }}" class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                            @error('intro_video_url') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-2">Mô tả</label>
                        <textarea name="description" rows="4" class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" data-close-create-course class="px-4 py-2 rounded-lg border border-slate-600 text-slate-300">Hủy</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-500 text-white font-semibold">Lưu khóa học</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-emerald-700 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Quản lý khóa học</h1>
            <p class="text-sm text-slate-400">Dữ liệu được cập nhật từ hệ thống</p>
        </div>
        <div class="flex gap-3">
            <button id="btn-open-change-instructor" type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all hidden">
                + Đổi giảng viên
            </button>
            <button id="open-create-course" type="button" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-500/20 transition-all">
                + Thêm khóa học
            </button>
        </div>
    </header>

    <div class="bg-[#1e293b] rounded-2xl border border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/50 text-slate-400 text-[10px] uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Khóa học</th>
                        <th class="px-6 py-4 font-semibold">Giảng viên</th>
                        <th class="px-6 py-4 font-semibold text-center">Số lớp</th>
                        <th class="px-6 py-4 font-semibold">Giá</th>
                        <th class="px-6 py-4 font-semibold text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="course-table-body" class="divide-y divide-slate-700">
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-500 italic">Đang đồng bộ dữ liệu...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="pagination-info" class="p-4 border-t border-slate-700 bg-slate-800/20 text-xs flex justify-between items-center text-slate-400">
            </div>
    </div>

    <div id="change-instructor-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" onclick="closeChangeInstructorModal()"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-[#1e293b] w-full max-w-md rounded-2xl border border-slate-700 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 bg-slate-800/50">
                    <h3 class="text-xl font-bold text-white">Thay đổi giảng viên</h3>
                    <p id="target-course-name" class="text-xs text-emerald-400 mt-1"></p>
                </div>
                
                <form id="form-change-instructor" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="change-course-id" name="course_id">
                    
                    <div>
                        <label class="block text-xs text-slate-400 mb-2">Chọn giảng viên mới *</label>
                        <select name="instructor_id" required class="w-full bg-slate-900/40 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white">
                            <option value="">-- Danh sách giảng viên --</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}">
                                    {{ $instructor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" onclick="closeChangeInstructorModal()" class="px-4 py-2 rounded-lg border border-slate-600 text-slate-300">Hủy</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-blue-500 text-white font-semibold">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const API_URL = '/admin/courses/api/list';
            const tableBody = document.getElementById('course-table-body');
            const paginationInfo = document.getElementById('pagination-info');
            const detailModal = document.getElementById('course-detail-modal');
            const createCourseModal = document.getElementById('create-course-modal');
            const openCreateCourseBtn = document.getElementById('open-create-course');
            const closeCreateCourseButtons = document.querySelectorAll('[data-close-create-course]');
            const courseTitleInput = document.getElementById('course-title');
            const courseSlugInput = document.getElementById('course-slug');
            const changeInstructorModal = document.getElementById('change-instructor-modal');
            const btnOpenChangeInstructor = document.getElementById('btn-open-change-instructor');
            const formChangeInstructor = document.getElementById('form-change-instructor');
            const targetCourseName = document.getElementById('target-course-name');
            const changeCourseIdInput = document.getElementById('change-course-id');

            let selectedCourse = null;

            // --- Logic Đổi Giảng Viên ---
            window.openChangeInstructorModal = function() {
                if (!selectedCourse) return;
                targetCourseName.innerText = `Đang sửa cho: ${selectedCourse.name}`;
                changeCourseIdInput.value = selectedCourse.id;
                formChangeInstructor.action = `/admin/courses/${selectedCourse.id}/instructor`;
                changeInstructorModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            window.closeChangeInstructorModal = function() {
                changeInstructorModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            };

            btnOpenChangeInstructor.addEventListener('click', openChangeInstructorModal);

            // --- Logic Click Dòng Table (Gộp Detail & Select) ---
            tableBody.addEventListener('click', function(event) {
                const row = event.target.closest('tr[data-course]');
                if (!row) return;

                // Nếu click vào nút "Sửa" riêng lẻ thì không làm gì để nút đó tự xử lý (nếu có)
                if (event.target.closest('button')) return;

                // 1. Highlight dòng
                document.querySelectorAll('tr[data-course]').forEach(r => r.classList.remove('bg-slate-700/50'));
                row.classList.add('bg-slate-700/50');

                // 2. Lưu thông tin selected
                const encoded = row.getAttribute('data-course');
                selectedCourse = JSON.parse(decodeURIComponent(encoded));

                // 3. Hiện nút đổi giảng viên
                btnOpenChangeInstructor.classList.remove('hidden');

                // 4. Mở modal chi tiết (Giữ nguyên tính năng cũ của bạn)
                showCourseDetail(encoded);
            });

            // --- Các hàm tiện ích (Slug, Modal Create) ---
            function slugify(value) {
                return (value || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            }

            function openCreateCourseModal() {
                if (!createCourseModal) return;
                createCourseModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeCreateCourseModal() {
                if (!createCourseModal) return;
                createCourseModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            if (openCreateCourseBtn) openCreateCourseBtn.addEventListener('click', openCreateCourseModal);
            closeCreateCourseButtons.forEach(btn => btn.addEventListener('click', closeCreateCourseModal));

            if (courseTitleInput && courseSlugInput) {
                courseTitleInput.addEventListener('input', function() {
                    if (courseSlugInput.value.trim() !== '') return;
                    courseSlugInput.value = slugify(courseTitleInput.value);
                });
            }

            if (@json($errors->any())) openCreateCourseModal();

            // --- Logic Hiển Thị Detail ---
            window.showCourseDetail = function(courseEncoded) {
                const course = JSON.parse(decodeURIComponent(courseEncoded));
                document.getElementById('detail-title').innerText = course.name || 'Chi tiết khóa học';
                document.getElementById('detail-instructor').innerText = "Giảng viên: " + course.instructor;
                
                const classesBody = document.getElementById('detail-classes-body');
                classesBody.innerHTML = '';

                if (course.classes && course.classes.length > 0) {
                    course.classes.forEach(c => {
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
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        ${c.location || 'Chưa xác định'}
                                    </div>
                                </td>
                            </tr>`;
                    });
                } else {
                    classesBody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 italic">Khóa học này chưa mở lớp nào.</td></tr>';
                }
                detailModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            window.closeDetailModal = function() {
                detailModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            };

            // --- Logic Load Data ---
            window.loadCourses = function(url) {
                tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500 italic">Đang tải dữ liệu...</td></tr>';
                fetch(url)
                    .then(response => response.json())
                    .then(res => {
                        tableBody.innerHTML = '';
                        if (res.data && res.data.length > 0) {
                            res.data.forEach(course => {
                                const courseData = encodeURIComponent(JSON.stringify(course));
                                const row = `
                                <tr data-course="${courseData}" class="hover:bg-slate-800/60 cursor-pointer transition-all group">
                                    <td class="px-6 py-4 text-sm text-slate-500 font-mono">#${course.id}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors">${course.name}</div>
                                        <div class="text-[10px] text-slate-500 font-mono mt-0.5">${course.slug}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-200 group-hover:text-emerald-400 transition-colors">${course.instructor}</td>
                                    <td class="px-6 py-4 text-center text-sm text-slate-300">${course.class_count}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-white">${course.price}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 rounded text-[10px] font-bold uppercase border border-emerald-500/20">${course.status}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button type="button" class="p-1.5 hover:bg-slate-700 rounded text-slate-400 hover:text-white transition-colors text-xs font-medium">Sửa</button>
                                    </td>
                                </tr>`;
                                tableBody.insertAdjacentHTML('beforeend', row);
                            });
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500">Không có dữ liệu khóa học.</td></tr>';
                        }
                        renderPagination(res);
                    })
                    .catch(err => {
                        tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-10 text-center text-red-400">Lỗi đồng bộ dữ liệu.</td></tr>';
                    });
            };

            function renderPagination(meta) {
                if (!paginationInfo) return;
                const total = meta.total || 0;
                const from = meta.from || 0;
                const to = meta.to || 0;
                const currentPage = meta.current_page || 1;
                const lastPage = meta.last_page || 1;
                paginationInfo.innerHTML = `
                    <span>Hiển thị ${from} - ${to} / ${total} khóa học</span>
                    <div class="flex items-center gap-2">
                        ${currentPage > 1 ? `<button data-page="${currentPage - 1}" class="px-3 py-1 rounded border border-slate-600 hover:bg-slate-700">Trước</button>` : `<button disabled class="px-3 py-1 rounded border border-slate-600 text-slate-600">Trước</button>`}
                        <span>Trang ${currentPage}/${lastPage}</span>
                        ${currentPage < lastPage ? `<button data-page="${currentPage + 1}" class="px-3 py-1 rounded border border-slate-600 hover:bg-slate-700">Sau</button>` : `<button disabled class="px-3 py-1 rounded border border-slate-600 text-slate-600">Sau</button>`}
                    </div>`;
            }

            if (paginationInfo) {
                paginationInfo.addEventListener('click', function(event) {
                    const button = event.target.closest('button[data-page]');
                    if (!button) return;
                    const page = button.getAttribute('data-page');
                    const targetUrl = new URL(API_URL, window.location.origin);
                    targetUrl.searchParams.set('page', page);
                    loadCourses(targetUrl.toString());
                });
            }

            loadCourses(API_URL);
        });
    </script>
@endpush