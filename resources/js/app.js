import './bootstrap';
import '../css/login.css';
import './login.js';

import Alpine from 'alpinejs';
import { initEggCalculator } from './calculate_eggs.js';
import { initDashboard } from './dashboard.js';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initEggCalculator();
    initDashboard();
});
