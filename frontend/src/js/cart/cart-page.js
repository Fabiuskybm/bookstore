
// ==================================================
//  CART PAGE
//  - Conecta DOM, render, promo y checkout
// ==================================================

import { getCartDomRefs } from './cart-dom.js';
import { renderCart, updateCartTotals } from './cart-render.js';
import { initCartPromo } from './cart-promo.js';
import { handleCheckout } from './cart-ticket.js';



// ==================================================
//  INITIALIZATION
// ==================================================

/**
 * Inicializa la página de carrito.
 *
 * - Obtiene referencias del DOM.
 * - Renderiza el carrito inicial.
 * - Engancha el botón de checkout.
 * - Inicializa la lógica de código promocional.
 */
export function initCartPage() {
    const dom = getCartDomRefs();
    if (!dom) return;

    // Render inicial del carrito
    renderCart();

    // Checkout: genera ticket, vacía carrito y limpia wishlist
    if (dom.checkoutBtn) {
        dom.checkoutBtn.addEventListener('click', () => {
            handleCheckout(dom);
        });
    }

    // Código promocional: validación + actualización de totales
    initCartPromo(dom, () => updateCartTotals(dom));
}
