
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

// Wishlist
import './wishlist/wishlist-select-all.js'; // listeners pasivos (no init explícito)
import { initWishlistToggle } from './wishlist/wishlist-toggle.js';
import { initWishlistActions } from './wishlist/wishlist-actions.js';

// Packs
import { initPacksSelects } from './packs/packs-select.js';
import { initPacksAsync } from './packs/packs-async.js';

// Rating (React island)
import { initRatingIsland } from './rating/rating-mount.jsx';


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
 * Cada módulo:
 * - Comprueba si su vista existe.
 * - No hace nada si no aplica.
 *
 * Permite usar un único bundle global.
 */
document.addEventListener('DOMContentLoaded', () => {

    // Auth: tabs de login / registro
    initAuthTabs();

    // Preferencias: tema, modo de vista, etc.
    initViewModePreference();

    // Header: dropdowns de usuario e idioma
    initHeaderDropdowns();

    // Cart: añadir productos desde cards
    initCartFromCards();

    // Cart: vista completa (cantidades, promo, checkout)
    initCartPage();

    // Utilidad global: botón scroll-to-top
    initScrollTop();

    // Header: badge dinámico del carrito
    initCartBadge();

    // Home: carrusel de libros destacados
    initBookCarousel();

    // Wishlist: toggle añadir / quitar producto
    initWishlistToggle();

    // Wishlist: acciones masivas (eliminar, añadir al carrito)
    initWishlistActions();

    // Packs: selects dependientes (categoría → libros)
    initPacksSelects();

    // Packs: operaciones async (añadir, eliminar, refrescar vista)
    initPacksAsync();

    // Rating: monta el island React de valoraciones
    initRatingIsland();
});
