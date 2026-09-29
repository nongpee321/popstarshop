import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

// Bypass network timeout for fonts by mocking fetch
const originalFetch = globalThis.fetch;
globalThis.fetch = async (url, options) => {
    if (url.toString().includes('fonts.bunny.net')) {
        return new Response('/* mocked fonts */', { status: 200, headers: { 'content-type': 'text/css' } });
    }
    return originalFetch(url, options);
};

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/pos-shared.css', 'resources/js/pos-web.ts', 'resources/js/app-launcher.ts', 'resources/js/greenfield/main.ts'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        vue(),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
