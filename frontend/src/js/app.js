
import 'normalize.css';
import '../styles/main.scss';

import { initAuthTabs } from './auth/auth-tabs.js';
import { initViewModePreference } from './preferences/preferences.js';
import { initHeaderDropdowns } from './header/header-dropdown.js';
import { initCartFromCards } from './cart/cart-from-cards.js';
import { initCartPage } from './cart/cart-page.js';
import { initCartBadge } from './cart/cart-badge.js';
import { initScrollTop } from './Shared/scroll-top.js';
import './wishlist/wishlist-select-all.js';


console.log('Frontend ready');


document.addEventListener('DOMContentLoaded', () => {
    initAuthTabs();
    initViewModePreference();
    initHeaderDropdowns();
    initCartFromCards();
    initCartPage();
    initScrollTop();
    initCartBadge();
});