import { defineConfig } from 'vite';
import postcss from './postcss.config';

export default defineConfig({
    root: 'src',
    base: process.env.NODE_ENV === 'production' ? '/wp-content/plugins/discount-esp-influencer-referral-program-signup-form/dist/' : '/',
    build: {
        outDir: '../dist',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                main: './src/main.js'
            }
        }
    },
    server: {
        strictPort: true,
        port: 3000,
        open: '/public/index.php'
    },
    css: {
        postcss
    }
});