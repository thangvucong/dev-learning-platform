@props([
    'items' => [],
])

@php
    $items = is_array($items) ? $items : [];
@endphp

<section aria-label="Hero banners" class="mb-10">
    <div
        class="relative overflow-visible"
        data-hero-slider
    >
        <div class="splide hero-splide" aria-label="Hero banner slider">
            <div class="splide__track w-full overflow-hidden rounded-3xl">
                <ul class="splide__list">
                    @foreach ($items as $item)
                        <li class="splide__slide">
                            <x-home.hero-slide :item="$item" />
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="splide__arrows">
                <button
                    type="button"
                    class="splide__arrow splide__arrow--prev hero-slider-nav"
                    aria-label="Previous slide"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="h-5 w-5" aria-hidden="true">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>
                <button
                    type="button"
                    class="splide__arrow splide__arrow--next hero-slider-nav"
                    aria-label="Next slide"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="h-5 w-5" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>
