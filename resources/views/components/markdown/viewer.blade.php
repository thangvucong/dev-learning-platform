@props([
    'value' => '',
    'theme' => 'dark', // dark|light
])

@php
    $id = 'mdv-' . md5($value . '-' . uniqid('', true));
@endphp

@php
    $isLight = $theme === 'light';
    $baseClass = $isLight
        ? 'rounded-xl border border-gray-200 bg-white overflow-hidden'
        : 'rounded-xl border border-slate-700 bg-slate-900/30 overflow-hidden';
    $headerClass = $isLight
        ? 'px-4 py-3 border-b border-gray-200 bg-gray-50'
        : 'px-4 py-3 border-b border-slate-700 bg-slate-800/40';
    $headerTextClass = $isLight ? 'text-gray-600' : 'text-slate-400';
@endphp

<div class="{{ $baseClass }}"
    data-markdown-viewer
    data-viewer-id="{{ $id }}"
    data-viewer-theme="{{ $theme }}"
>
    <div class="{{ $headerClass }}">
        <p class="text-xs font-semibold {{ $headerTextClass }}">Nội dung</p>
    </div>
    <div class="p-4">
        <div id="{{ $id }}"></div>
        <textarea class="hidden" data-viewer-textarea>{{ $value }}</textarea>
    </div>
</div>

