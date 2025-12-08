// vite.config.js

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/login.css',
                'resources/css/profile.css',
                'resources/css/edit-profile.css',
                'resources/css/edit-user.css',
                'resources/css/user-management.css',
                'resources/css/slide.css',
                'resources/css/product-index.css',
                'resources/css/product-detail.css',
                'resources/css/wishlist.css',
                'resources/css/shipping-create.css',
            ],
            refresh: true,
        }),
    ],
});