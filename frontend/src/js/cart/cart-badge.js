// ==================================================
//  CART BADGE (Icono del carrito en el header)
//  - Actualiza el número de items
//  - Anima el icono al cambiar el carrito
// ==================================================

import { getCartItems } from './cart-storage.js';



// ==================================================
//  ACTUALIZACIÓN DEL BADGE
// ==================================================

/**
 * Actualiza el badge del carrito en el header.
 *
 * - Muestra la cantidad total de items.
 * - Oculta el badge cuando el carrito está vacío.
 */
export function updateCartBadge() {
    const badge = document.querySelector('[data-cart-badge]');
    if (!badge) return;

    const VISIBLE_CLASS = 'header__cart-badge--visible';

    const items = getCartItems();
    const total = items.reduce((sum, item) => sum + (item.quantity || 0), 0);

    if (total > 0) {
        badge.textContent = String(total);
        badge.classList.add(VISIBLE_CLASS);
    } else {
        badge.textContent = '';
        badge.classList.remove(VISIBLE_CLASS);
    }
}



// ==================================================
//  INICIALIZACIÓN DEL BADGE
// ==================================================

/**
 * Inicializa el badge del carrito:
 * - Actualiza el estado inicial.
 * - Escucha el evento global "cart:updated".
 * - Aplica una animación visual al icono del carrito.
 */
export function initCartBadge() {
    updateCartBadge();

    // Animación al actualizar el carrito
    const animateCartIcon = () => {
        const cartAction = document.querySelector('.header__action--cart');
        if (!cartAction) return;

        cartAction.classList.remove('header__action--cart--animating');

        // Forzar reflow para reiniciar la animación
        void cartAction.offsetWidth;

        cartAction.classList.add('header__action--cart--animating');
    };

    // Cuando el carrito cambia → actualizar badge + animar icono
    window.addEventListener('cart:updated', () => {
        updateCartBadge();
        animateCartIcon();
    });
}
