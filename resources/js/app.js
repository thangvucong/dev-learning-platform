require('./bootstrap');
require('./modules/home/home');
require('./modules/auth/auth-modal');
require('./modules/posts/post-editor');

const initHeroSlider = require('./components/hero-slider');

// Init after full load to avoid early layout mismatch
// (fonts/images/CSS settling can cause slider width glitches).
window.addEventListener('load', function () {
    initHeroSlider();
}, { once: true });
