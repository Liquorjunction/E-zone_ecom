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
                    colors: {
                        'primary': 'oklch(0.69 0.14 232.2)',
                        'background': 'oklch(0.69 0.14 232.2 / 0.05)',
                    }
                }
            }
        }),
    ],
    server: {
        host: '192.168.176.64',
        cors: true,
    },
});
