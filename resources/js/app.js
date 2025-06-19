// Import de base Laravel & Bootstrap
import './bootstrap';
import * as bootstrap from 'bootstrap';
import '@fortawesome/fontawesome-free/js/all';

// jQuery & Select2
import $ from 'jquery';
window.$ = window.jQuery = $;
import 'select2';

// Si tu utilises Alpine via NPM (option b)
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
