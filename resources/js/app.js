import './bootstrap';
import '../css/login.css';
import './login.js';
// El import de SweetAlert2 solo debe estar en calculate_eggs.js

import Alpine from 'alpinejs';
import { initEggCalculator } from './calculate_eggs.js';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initEggCalculator();
});
