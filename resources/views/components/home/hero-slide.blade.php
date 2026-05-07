@props([
    'item' => [],
])

@php
    $title = (string) ($item['title'] ?? '');
    $description = (string) ($item['description'] ?? '');
    $buttonText = (string) ($item['button_text'] ?? '');
    $buttonUrl = (string) ($item['button_url'] ?? '#');
    $thumbnailUrl = (string) ($item['thumbnail_url'] ?? '');
    $backgroundGradient = (string) ($item['background_gradient'] ?? '');
    $badgeText = $item['badge_text'] ?? null;
@endphp

<article
    class="relative overflow-hidden rounded-3xl text-white"
    style="{{ !empty($backgroundGradient) ? 'background-image: ' . e($backgroundGradient) . ';' : '' }}"
>
    <div class="absolute inset-0 bg-gradient-to-r from-black/25 via-black/0 to-black/0"></div>

    <div class="relative grid min-h-[280px] grid-cols-1 gap-6 p-6 sm:min-h-[292px] sm:p-8 lg:min-h-[300px] lg:grid-cols-2 lg:gap-10 lg:p-10">
        <div class="hero-slide-content flex flex-col justify-center">
            @if (!empty($badgeText))
                <div class="mb-4">
                    <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold tracking-wide ring-1 ring-white/20">
                        {{ $badgeText }}
                    </span>
                </div>
            @endif

            <h2 class="text-[28px] font-black leading-[1.12] tracking-tight sm:text-[34px] lg:text-[40px]">
                {{ $title }}
            </h2>

            @if (!empty($description))
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-white/90 sm:text-base">
                    {{ $description }}
                </p>
            @endif

            @if (!empty($buttonText))
                <div class="mt-6">
                    <a
                        href="{{ $buttonUrl }}"
                        class="hero-cta inline-flex items-center justify-center rounded-full border border-white/70 bg-white/10 px-6 py-2.5 text-sm font-bold uppercase tracking-wide text-white backdrop-blur transition"
                    >
                        <span>{{ $buttonText }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="ml-2 h-4 w-4" aria-hidden="true">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>

        <div class="relative flex items-center justify-center lg:justify-end">
            <div class="hero-image-wrap relative h-[160px] w-full overflow-hidden rounded-2xl bg-white/10 ring-1 ring-white/15 sm:h-[200px] lg:h-[210px] lg:w-[92%]">
                @if (!empty($thumbnailUrl))
                    <img
                        src="{{ $thumbnailUrl }}"
                        alt="{{ $title }}"
                        loading="lazy"
                        decoding="async"
                        class="h-full w-full object-cover"
                    />
                @else
                    <div class="flex h-full w-full items-center justify-center text-white/70">
                        <span class="text-sm font-semibold">No preview</span>
                    </div>
                @endif
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-black/20 via-transparent to-white/10"></div>
            </div>
        </div>
    </div>
</article>
