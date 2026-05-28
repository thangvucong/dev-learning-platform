const Splide = require('@splidejs/splide').default;
require('@splidejs/splide/dist/css/splide-core.min.css');

function initHeroSlider() {
    const roots = document.querySelectorAll('[data-hero-slider]');
    if (!roots || roots.length === 0) return;

    roots.forEach((root) => {
        if (root.dataset.heroSliderInitialized === '1') return;
        root.dataset.heroSliderInitialized = '1';

        const el = root.querySelector('.hero-splide');
        if (!el) return;

        const splide = new Splide(el, {
            type: 'loop',
            perPage: 1,
            perMove: 1,
            gap: 0,
            speed: 750,
            rewind: false,
            autoplay: true,
            interval: 4500,
            pauseOnHover: true,
            pauseOnFocus: true,
            resetProgress: false,
            arrows: true,
            pagination: true,
            drag: true,
            keyboard: 'global',
            lazyLoad: 'nearby',
            classes: {
                pagination: 'splide__pagination hero-slider-pagination',
                page: 'splide__pagination__page hero-bullet',
            },
        });

        splide.on('mounted move', () => {
            requestAnimationFrame(() => {
                const active = el.querySelector('.splide__slide.is-active .hero-slide-content');
                if (active) active.style.willChange = 'opacity, transform';
            });
        });

        splide.mount();

        // One extra refresh after mount for stability on first render.
        setTimeout(() => {
            try {
                splide.refresh();
            } catch (e) {}
        }, 0);
    });
}

module.exports = initHeroSlider;

