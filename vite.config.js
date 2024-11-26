import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/custom.css',
                'resources/js/app.js'
            ],
            refresh: true,
            https: true
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources',
            'vue': 'vue/dist/vue.esm-bundler.js'
        },
    },
    server: {
        hmr: { host: 'aitodos.test' },
        host: 'aitodos.test'
    }
});
