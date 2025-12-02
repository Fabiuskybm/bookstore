import '../styles/main.scss';

import { initAuthTabs } from './auth/auth-tabs.js';
import { initHeaderUserMenu } from './header/header-user-menu.js';

console.log('Frontend ready');


document.addEventListener('DOMContentLoaded', () => {
    initAuthTabs();
    initHeaderUserMenu();
});