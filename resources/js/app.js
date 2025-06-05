/* resources/js/app.js */

// Importe les scripts de base de Laravel (ex: Axios pour les requêtes HTTP)
import './bootstrap';

// Importe toutes les fonctionnalités JavaScript de Bootstrap
import * as bootstrap from 'bootstrap';

// Si vous utilisez Alpine.js (souvent avec Laravel Breeze/Jetstream), gardez ceci :
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();