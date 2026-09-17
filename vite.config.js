import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ command }) => {
    const isDev = command === 'serve';

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
                fonts: [
                    bunny('Poppins', {
                        weights: [400, 500, 600, 700],
                        optimizedFallbacks: true,
                    }),
                ],
            }),
            tailwindcss(),
        ],

        // Konfigurasi server hanya diaktifkan secara aman saat mode development
        ...(isDev && {
            server: {
                host: process.env.VITE_HMR_HOST ? '0.0.0.0' : 'localhost',
                hmr: process.env.VITE_HMR_HOST 
                    ? { host: process.env.VITE_HMR_HOST } 
                    : true,
                watch: {
                    ignored: [
                        '**/storage/framework/views/**',
                        '**/storage/logs/**',
                        '**/bootstrap/cache/**',
                    ],
                },
            },
        }),

        build: {
            chunkSizeWarningLimit: 1000,
            manifest: 'manifest.json',
            outDir: 'public/build',
            emptyOutDir: true,
        },
    };
});