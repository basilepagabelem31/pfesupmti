/* resources/js/app.js */

// Importe les scripts de base de Laravel (ex: Axios pour les requêtes HTTP)
import './bootstrap';

// Importe toutes les fonctionnalités JavaScript de Bootstrap
import * as bootstrap from 'bootstrap';

import $ from 'jquery';
window.$ = window.jQuery = $; // Rend jQuery disponible globalement pour Select2
import 'select2';
import 'select2/dist/css/select2.css'; // Importe le CSS de Select2
// Si vous utilisez Alpine.js (souvent avec Laravel Breeze/Jetstream), gardez ceci :
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();