@props([
    'name',
    'value' => '',
    'uploadUrl' => null,
    'height' => '420px',
    'placeholder' => 'Viết nội dung bằng Markdown…',
    'theme' => 'dark',
])

@php
    $id = $attributes->get('id') ?: ('md-' . md5($name . '-' . uniqid('', true)));
    $initial = old($name, $value);
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-700 bg-slate-900/30 overflow-hidden']) }}
    data-markdown-editor
    data-editor-id="{{ $id }}"
    data-editor-height="{{ $height }}"
    data-editor-placeholder="{{ $placeholder }}"
    data-editor-theme="{{ $theme }}"
    @if ($uploadUrl) data-editor-upload-url="{{ $uploadUrl }}" @endif
>
    <div class="px-4 py-3 border-b border-slate-700 bg-slate-800/40">
        <p class="text-xs font-semibold text-slate-400">Markdown editor</p>
    </div>

    <div id="{{ $id }}" class="bg-transparent"></div>

    <textarea name="{{ $name }}" class="hidden" data-editor-textarea>{{ $initial }}</textarea>
</div>

