// ==================================================
//  CART TICKET & CHECKOUT
//  - Generación del ticket HTML
//  - Flujo de checkout
//  - Limpieza de wishlist tras la compra
// ==================================================

import { getCartItems, clearCart } from './cart-storage.js';
import { DiscountedProduct } from './Product.js';
import { getCartTexts } from './cart-dom.js';
import {
    calculateTotalsWithDiscount,
    getCurrentDiscountPercent
} from './cart-promo.js';
import {
    mapCartItemsToProducts,
    formatPrice,
    renderCart
} from './cart-render.js';



// ==================================================
//  GENERACIÓN DEL TICKET
// ==================================================

/**
 * Construye el HTML del ticket de compra a partir
 * de los items del carrito.
 *
 * - Muestra fecha y hora.
 * - Lista de productos con cantidades y totales.
 * - Subtotal, descuento y total final.
 */
export function buildTicketHtml(items) {
    const date = new Date();
    const texts = getCartTexts();

    const formattedDate =
        date.toLocaleDateString('es-ES') +
        ' ' +
        date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

    const products = mapCartItemsToProducts(items);

    const {
        subtotal,
        discountTotal,
        grandTotal,
        hasDiscount
    } = calculateTotalsWithDiscount(products, getCurrentDiscountPercent());

    let linesHtml = '';

    products.forEach((product) => {
        let lineTotal = product.lineTotal;

        if (hasDiscount) {
            const discounted = new DiscountedProduct(
                {
                    id: product.id,
                    title: product.title,
                    price: product.price,
                    quantity: product.quantity
                },
                getCurrentDiscountPercent()
            );

            lineTotal = discounted.finalLineTotal;
        }

        linesHtml += `
            <div class="ticket__item">
                <span class="ticket__title">${product.title}</span>

                <span class="ticket__qty">
                    <span class="ticket__qty-x">x</span>
                    ${product.quantity}
                </span>
                
                <span class="ticket__total">${formatPrice(lineTotal)}</span>
            </div>
        `;
    });

    const discountRowAttr = hasDiscount ? '' : ' hidden';
    const discountValue = hasDiscount ? discountTotal : 0;

    return `
        <div class="ticket">
            <h3 class="ticket__title">${texts.ticketTitle}</h3>
            <p class="ticket__date">${formattedDate}</p>

            <div class="ticket__items">
                ${linesHtml}
            </div>

            <div class="ticket__totals">
                <p class="ticket__subtotal">
                    ${texts.subtotalLabel}: <span data-ticket-subtotal>${formatPrice(subtotal)}</span>
                </p>

                <p class="ticket__discount"${discountRowAttr}>
                    ${texts.discountLabel}: <span data-ticket-discount>${formatPrice(discountValue)}</span>
                </p>

                <p class="ticket__grand-total">
                    ${texts.totalLabel}: <span data-ticket-total>${formatPrice(grandTotal)}</span>
                </p>
            </div>
        </div>
    `;
}



// ==================================================
//  CHECKOUT FLOW
// ==================================================

/**
 * Maneja el flujo completo de checkout:
 * - Si el carrito está vacío, muestra alerta.
 * - Si hay items:
 *   - Genera y muestra el ticket.
 *   - Vacía el carrito y vuelve a renderizar.
 *   - Lanza la limpieza de wishlist en el backend.
 */
export function handleCheckout(dom) {
    const items = getCartItems();
    const texts = getCartTexts();

    if (!items.length) {
        alert(texts.alertEmpty);
        return;
    }

    const ticketHtml = buildTicketHtml(items);

    dom.ticketEl.innerHTML = ticketHtml;
    dom.ticketEl.hidden = false;

    const purchasedIds = items.map((item) => item.id);

    clearCart();
    renderCart();

    sendWishlistCleanup(purchasedIds);
}



// ==================================================
//  LIMPIEZA DE WISHLIST TRAS LA COMPRA
// ==================================================

/**
 * Envía al backend la lista de IDs comprados para
 * eliminarlos de la wishlist (operación bulk).
 */
function sendWishlistCleanup(bookIds) {
    if (!Array.isArray(bookIds) || bookIds.length === 0) return;

    const formData = new FormData();

    bookIds.forEach((id) => {
        if (typeof id === 'string' && id.trim() !== '') {
            formData.append('selected_books[]', id.trim());
        }
    });

    if (!formData.has('selected_books[]')) return;

    formData.append('action', 'wishlist_bulk_remove');
    formData.append('_return', 'cart');

    fetch('index.php?view=wishlist', {
        method: 'POST',
        body: formData
    }).catch(() => {
        // Por ahora ignoramos errores de red en esta operación.
    });
}
