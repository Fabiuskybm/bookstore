
// ==================================================
//  CART DOM UTILITIES
//  - Textos desde data-*
//  - Referencias de DOM
// ==================================================


// ==================================================
//  TEXTOS DEL CARRITO (i18n desde data-attributes)
// ==================================================

/**
 * Obtiene los textos del carrito desde los data-* de
 * <section class="cart">.
 *
 * Devuelve valores por defecto si falta algún atributo.
 */
export function getCartTexts() {
    const section = document.querySelector('.cart');

    // Fallback seguro si no estamos en la vista de carrito
    if (!section) {
        return {
            empty: 'Your cart is empty.',
            ticketTitle: 'Order placed.',
            alertEmpty: 'The cart is empty.',
            coverAlt: 'Book cover.',
            quantityLabel: 'Quantity.',
            removeAlt: 'Remove.',
            subtotalLabel: 'Subtotal',
            discountLabel: 'Discount',
            totalLabel: 'Total',
            promoInvalid: 'Invalid code.',
            promoApplied: 'Code applied: {percent}% discount.'
        };
    }

    const {
        cartEmptyText,
        cartTicketTitle,
        cartAlertEmpty,
        cartCoverAlt,
        cartQuantityLabel,
        cartRemoveAlt,
        cartTicketSubtotalLabel,
        cartTicketDiscountLabel,
        cartTicketTotalLabel,
        cartPromoInvalid,
        cartPromoApplied
    } = section.dataset;

    return {
        empty: cartEmptyText || 'Your cart is empty.',
        ticketTitle: cartTicketTitle || 'Order placed.',
        alertEmpty: cartAlertEmpty || 'The cart is empty.',
        coverAlt: cartCoverAlt || 'Book cover.',
        quantityLabel: cartQuantityLabel || 'Quantity.',
        removeAlt: cartRemoveAlt || 'Remove.',
        subtotalLabel: cartTicketSubtotalLabel || 'Subtotal',
        discountLabel: cartTicketDiscountLabel || 'Discount',
        totalLabel: cartTicketTotalLabel || 'Total',
        promoInvalid: cartPromoInvalid || 'Invalid code.',
        promoApplied: cartPromoApplied || 'Code applied: {percent}% discount.'
    };
}



// ==================================================
//  REFERENCIAS DEL DOM DEL CARRITO
// ==================================================

/**
 * Devuelve todas las referencias del DOM necesarias para
 * operar en la página de carrito.
 *
 * Si falta el contenedor principal del carrito,
 * devuelve null para impedir inicializaciones parciales.
 */
export function getCartDomRefs() {
    const itemsContainer = document.querySelector('[data-cart-items]');
    if (!itemsContainer) return null;

    const totalQtyEl = document.querySelector('[data-cart-total-quantity]');
    const totalPriceEl = document.querySelector('[data-cart-total-price]');
    const checkoutBtn = document.querySelector('[data-cart-checkout]');
    const ticketEl = document.querySelector('[data-cart-ticket]');

    const promoInput = document.querySelector('[data-cart-promo-input]');
    const promoApplyBtn = document.querySelector('[data-cart-promo-apply]');
    const promoMessage = document.querySelector('[data-cart-promo-message]');

    return {
        itemsContainer,
        totalQtyEl,
        totalPriceEl,
        checkoutBtn,
        ticketEl,
        promoInput,
        promoApplyBtn,
        promoMessage
    };
}
