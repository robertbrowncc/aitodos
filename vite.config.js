import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const appUrl = env.APP_URL || 'http://localhost';
    const hostname = new URL(appUrl).hostname;

    return {
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
            hmr: { host: hostname },
            host: hostname
        },
        define: {
            'import.meta.env.VITE_APP_URL': JSON.stringify(appUrl)
        }
    }
});
