import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [ laravel({
    input: ['resources/js/app.js', 'resources/css/app.css'],
    refresh: true,
  }) ],
  resolve: {
    alias: {
      // indique à Vite où trouver alpinejs
      'alpinejs': 'alpinejs/dist/module.esm.js',
    }
  }
});