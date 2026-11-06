import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const appUrl = env.APP_URL || '/';
    const configuredBase = process.env.VITE_BASE || env.VITE_BASE;
    const appPath = appUrl.startsWith('http') ? new URL(appUrl).pathname : '/';
    const basePath = (configuredBase || appPath).replace(/^\/+|\/+$/g, '');
    const base = basePath ? `/${basePath}/` : '/';

    return {
        base,
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            vue(),
        ],
    };
});
