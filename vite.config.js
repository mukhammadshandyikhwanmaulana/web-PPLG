import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Poppins', {
                    weights: [400, 500, 600, 700],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],

    server: {
        host: '0.0.0.0',
        hmr: process.env.VITE_HMR_HOST 
            ? { host: process.env.VITE_HMR_HOST } 
            : true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});