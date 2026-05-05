<div id="course-detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Overlay với hiệu ứng làm mờ -->
    <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity" onclick="closeDetailModal()"></div>
    
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-[#1e293b] w-full max-w-2xl rounded-2xl border border-slate-700 shadow-2xl overflow-hidden transform transition-all scale-95 duration-300">
            <!-- Header: Cố định phía trên -->
            <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center bg-slate-800/50 backdrop-blur-md">
                <div>
                    <h3 id="detail-title" class="text-xl font-bold text-white uppercase tracking-tight">Chi tiết khóa học</h3>
                    <p id="detail-instructor" class="text-xs text-emerald-400 font-medium mt-1"></p>
                </div>
                <button type="button" onclick="closeDetailModal()" class="p-2 text-slate-400 hover:text-white hover:bg-slate-700/50 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body: Có thanh cuộn nếu danh sách dài -->
            <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                <h4 class="text-[10px] font-bold text-slate-500 uppercase mb-4 tracking-[0.2em]">Danh sách lớp đang mở</h4>
                
                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-800/80 text-[10px] text-slate-400 uppercase tracking-wider">
                            <tr>
                               <th class="px-4 py-3">Mã lớp</th>
            <th class="px-4 py-3">Thông tin lớp</th>
            <th class="px-4 py-3 text-center">Sĩ số</th>
            <th class="px-4 py-3">Địa điểm</th>
                            </tr>
                        </thead>
                        <tbody id="detail-classes-body" class="divide-y divide-slate-800">
                          
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer: Cố định phía dưới -->
            <div class="px-6 py-4 border-t border-slate-700 bg-slate-800/30 text-right">
                <button type="button" onclick="closeDetailModal()" 
                    class="px-6 py-2 rounded-xl bg-slate-700 text-white text-sm font-bold hover:bg-slate-600 transition-all border border-slate-600 active:scale-95">
                    Đóng cửa sổ
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Thanh cuộn tùy chỉnh cho Modal để đồng bộ với layout TQCare */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }
</style>