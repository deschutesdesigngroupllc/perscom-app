import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import viteReact from "@vitejs/plugin-react";
import vue from "@vitejs/plugin-vue2";

export default defineConfig({
    plugins: [
        viteReact(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/form.js',
            ],
            refresh: true
        }),
    ],
});