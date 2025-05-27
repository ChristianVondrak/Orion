import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: { 
        host: '0.0.0.0',
        hmr: {
            host: '127.0.0.1',
        },
        watch: {
            usePolling:true,
        }
    }, 
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/statistics-dashboard.css',
                'resources/js/statistics-dashboard.js'
            ],          
            refresh: true,
        }),
    ],
});
