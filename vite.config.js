import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', // ต้องแน่ใจว่า app.css ตอนนี้มีแต่ Bootstrap
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});