import '../styles/main.scss';

import { initAuthTabs } from './auth/auth-tabs.js';
import { initHeaderUserMenu } from './header/header-user-menu.js';
import { initCartFromCards } from './cart/cart-from-cards.js';
import { initCartPage } from './cart/cart-page.js';
import './wishlist/wishlist-select-all.js';


console.log('Frontend ready');


document.addEventListener('DOMContentLoaded', () => {
    initAuthTabs();
    initHeaderUserMenu();
    initCartFromCards();
    initCartPage();
});