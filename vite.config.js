import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/rtl.css',
                'resources/js/app.js',
                'resources/js/admin/admin.js',
                'resources/js/admin/dashboard.js',
                'resources/js/admin/categories.js'
            ],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss(),
                autoprefixer(),
            ],
        },
    },
    build: {
        // Enable minification for both JS and CSS
        minify: 'terser',
        // Terser options
        terserOptions: {
            compress: {
                drop_console: true, // Remove console logs in production
                drop_debugger: true,
            },
        },
        // CSS minification
        cssMinify: true,
        // Code splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                },
                // Improve chunking strategy
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
            },
        },
        // Output directory
        outDir: 'public/build',
        // Enable source maps for debugging in dev, disable in prod
        sourcemap: process.env.NODE_ENV === 'development',
    },
});
