@props(['status'])

@php
    $map = [
        \App\Models\Post::STATUS_PUBLISHED => ['label' => 'Đã xuất bản', 'class' => 'bg-emerald-500/15 text-emerald-600 border-emerald-500/20'],
        \App\Models\Post::STATUS_DRAFT => ['label' => 'Bản nháp', 'class' => 'bg-slate-500/15 text-slate-600 border-slate-500/20'],
        \App\Models\Post::STATUS_PENDING => ['label' => 'Chờ admin duyệt', 'class' => 'bg-amber-500/15 text-amber-500 border-amber-500/20'],
        \App\Models\Post::STATUS_PENDING_AI_REVIEW => ['label' => 'AI đang duyệt', 'class' => 'bg-sky-500/15 text-sky-500 border-sky-500/20'],
        \App\Models\Post::STATUS_PENDING_HUMAN_REVIEW => ['label' => 'Chờ admin duyệt', 'class' => 'bg-amber-500/15 text-amber-500 border-amber-500/20'],
        \App\Models\Post::STATUS_REJECTED => ['label' => 'Từ chối', 'class' => 'bg-red-500/10 text-red-500 border-red-500/20'],
    ];

    $info = $map[$status] ?? ['label' => $status, 'class' => 'bg-slate-500/10 text-slate-600 border-slate-500/20'];
@endphp

<span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-bold {{ $info['class'] }}">
    {{ $info['label'] }}
</span>
