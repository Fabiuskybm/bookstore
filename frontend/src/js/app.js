
// ==================================================
//  GLOBAL FRONTEND
//  - Importa estilos globales
//  - Inicializa módulos JS cuando el DOM está listo
// ==================================================

import 'normalize.css';
import '../styles/main.scss';


// ==================================================
//  MODULE IMPORTS
// ==================================================

// Auth
import { initAuthTabs } from './auth/auth-tabs.js';

// Preferences (view mode, etc.)
import { initViewModePreference } from './preferences/preferences.js';

// Header interactions
import { initHeaderDropdowns } from './header/header-dropdown.js';

// Cart features
import { initCartFromCards } from './cart/cart-from-cards.js';
import { initCartPage } from './cart/cart-page.js';
import { initCartBadge } from './cart/cart-badge.js';

// Shared utilities
import { initScrollTop } from './Shared/scroll-top.js';

// Home page
import { initBookCarousel } from './home/book-carousel.js';

// Wishlist (side effects init)
import './wishlist/wishlist-select-all.js';


// ==================================================
//  DEBUG / LOADING INDICATOR
// ==================================================

console.log('Frontend ready');



// ==================================================
//  ROOT INITIALIZATION
// ==================================================

/**
 * Inicializa todos los módulos cuando el DOM está listo.
 *
 * Cada módulo es responsable de:
 * - Detectar si su vista está presente.
 * - Auto-silenciarse si no aplica.
 *
 * Esto permite cargar un único bundle global sin problemas.
 */
document.addEventListener('DOMContentLoaded', () => {

    // Auth (login/register tabs)
    initAuthTabs();

    // Preferencias de usuario
    initViewModePreference();

    // Header (dropdown menus)
    initHeaderDropdowns();

    // Añadir libros al carrito desde cards
    initCartFromCards();

    // Vista del carrito + promo + checkout
    initCartPage();

    // Scroll-to-top button
    initScrollTop();

    // Badge del carrito en el header
    initCartBadge();

    // Carrusel de inicio (libros destacados)
    initBookCarousel();
});
