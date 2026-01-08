import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss({
            theme: {
                extend: {
                    screens: {
                        'xs': '475px',
                        '3xl': '1600px',
                        '4xl': '1920px',
                        '5xl': '2560px',
                        'tall': { 'raw': '(min-height: 800px)' },
                        'short': { 'raw': '(max-height: 600px)' },
                        'ultrawide': { 'raw': '(min-aspect-ratio: 21/9)' },
                        'square': { 'raw': '(aspect-ratio: 1/1)' },
                        'portrait': { 'raw': '(orientation: portrait)' },
                        'landscape': { 'raw': '(orientation: landscape)' },
                    },
                    aspectRatio: {
                        '21/9': '21 / 9',
                        '16/10': '16 / 10',
                        '5/4': '5 / 4',
                        '4/3': '4 / 3',
                        '3/2': '3 / 2',
                        '1/1': '1 / 1',
                        '2/3': '2 / 3',
                        '3/4': '3 / 4',
                        '4/5': '4 / 5',
                        '9/16': '9 / 16',
                        '10/16': '10 / 16',
                        '9/21': '9 / 21',
                    },
                    colors: {
                        'primary': 'oklch(0.69 0.14 232.2)',
                        'background': 'oklch(0.69 0.14 232.2 / 0.05)',
                    }
                }
            }
        }),
    ],
    server: {
        host: '192.168.176.80',
        cors: true,
        secure: true,
    },
});
