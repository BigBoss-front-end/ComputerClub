import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss', 
                'resources/js/app.js',
                'resources/js/pages/login/script.js',
                'resources/js/pages/dashboard/script.js',
                'resiurces/js/pages/registration/script.js',
                'resiurces/js/pages/revovery/script.js'
            ],
            refresh: true,
        }),
    ],
});
