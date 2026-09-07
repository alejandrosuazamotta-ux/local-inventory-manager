import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/sass/app.scss',
                'resources/css/admin/admin.css',
                'resources/css/admin/products.css',
                'resources/css/admin/clientes.css',
                'resources/css/auth.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
