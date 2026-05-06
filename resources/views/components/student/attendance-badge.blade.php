@php
    $status = $status ?? 'present';
    $map = [
        'present' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30',
        'absent' => 'bg-red-500/10 text-red-300 border-red-500/30',
        'late' => 'bg-amber-500/10 text-amber-300 border-amber-500/30',
    ];
    $class = $map[$status] ?? $map['present'];
@endphp

<span class="inline-flex items-center px-2 py-1 rounded-full border text-[10px] uppercase font-semibold {{ $class }}">
    {{ $status }}
</span>

