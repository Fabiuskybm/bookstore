import '../styles/main.scss';

import { initAuthTabs } from './auth/auth-tabs.js';

console.log('Frontend ready');


document.addEventListener('DOMContentLoaded', () => {
    initAuthTabs();
});