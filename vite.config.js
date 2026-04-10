import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import legacy from '@vitejs/plugin-legacy';
// Ganti import ini:
import tailwindcss from '@tailwindcss/postcss';
import autoprefixer from 'autoprefixer';
import "tailwindcss";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Tambahkan file JavaScript baru Anda di sini:
                'resources/js/sidebar-toggle.js',
                'resources/js/sidebar-collapse.js',
                'resources/js/approval-handler.js',
            ],
            refresh: true,
        }),
        legacy({
            targets: ['defaults', 'not IE 11'],
        }),
    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss(), // Sekarang menggunakan @tailwindcss/postcss
                autoprefixer(),
            ],
        },
    },
    server: {
        host: '0.0.0.0',
        hmr: {
            host: '192.168.1.8',
        },
    },
});
