import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// --- Swiper Init ---
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

document.addEventListener('DOMContentLoaded', () => {
    const swiper = new Swiper('.hero-swiper', {
        modules: [Navigation, Pagination, Autoplay, EffectFade],

        loop: true,
        // effect: 'fade', // Removed smooth fade effect for testing
        // fadeEffect: {
        //     crossFade: true
        // },
        speed: 1000,

        autoplay: {
            delay: 1000,
            disableOnInteraction: false,
        },

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            dynamicBullets: true,
        },

        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
});
