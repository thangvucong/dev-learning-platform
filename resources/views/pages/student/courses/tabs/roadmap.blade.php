<div class="space-y-4">
    <div class="rounded-2xl border border-slate-700 bg-slate-900/40 p-5">
        <h3 class="text-lg font-semibold text-white">Learning Roadmap</h3>
        <p class="text-sm text-slate-300 mt-2">Lộ trình được thiết kế theo hành trình học bootcamp: hoàn thành nền tảng, tăng độ khó theo module và khóa mở theo tiến độ.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach ($roadmap as $item)
            @include('components.student.courses.roadmap-item', [
                'title' => $item['title'],
                'subtitle' => $item['subtitle'],
                'state' => $item['state'],
                'sessions' => $item['sessions'],
            ])
        @endforeach
    </div>
</div>

