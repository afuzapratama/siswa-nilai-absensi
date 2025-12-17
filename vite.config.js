import { defineConfig } from 'vite';
import { resolve } from 'path';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  root: 'resources',
  base: '/assets/dist/',
  
  plugins: [
    tailwindcss(),
  ],
  
  build: {
    outDir: '../public/assets/dist',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'resources/js/app.js'),
        css: resolve(__dirname, 'resources/css/app.css'),
      },
      output: {
        entryFileNames: '[name]-[hash].js',
        chunkFileNames: '[name]-[hash].js',
        assetFileNames: '[name]-[hash].[ext]',
      },
    },
  },
  
  server: {
    port: 5173,
    strictPort: true,
    origin: 'http://localhost:5173',
  },
  
  css: {
    postcss: resolve(__dirname, 'postcss.config.js'),
  },
});
