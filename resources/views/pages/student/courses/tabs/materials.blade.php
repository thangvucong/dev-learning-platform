<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    @foreach ($materials as $item)
        <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-white">{{ $item['name'] }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $item['type'] }}</p>
            </div>
            @if ($item['status'] === 'available')
                <a href="#" class="h-8 px-3 rounded-lg border border-emerald-500/30 text-emerald-300 text-xs font-semibold hover:bg-emerald-500/10 transition-colors inline-flex items-center">
                    Mở tài liệu
                </a>
            @else
                <span class="text-xs text-slate-500">Sắp cập nhật</span>
            @endif
        </div>
    @endforeach
</div>

