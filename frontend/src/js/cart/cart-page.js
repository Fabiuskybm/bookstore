
import {
    getCartItems,
    getTotals,
    setQuantity,
    removeItem,
    clearCart
} from './cart-storage.js';

import { Product } from './Product.js';



function getCartTexts() {
    const section = document.querySelector('.cart');

    if (!section) {
        return {
            empty: 'Your cart is empty.',
            ticketTitle: 'Order placed.',
            alertEmpty: 'The cart is empty.',
            coverAlt: 'Book cover.',
            quantityLabel: 'Quantity.',
            removeAlt: 'Remove.'
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
        cartTicketTotalLabel

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
        totalLabel: cartTicketTotalLabel || 'Total'
    };
}


function formatPrice(price) {
    const num = Number(price) || 0;
    return num.toFixed(2).replace('.', ',') + ' €';
}


function mapCartItemsToProducts(items) {
    return items.map((item) => new Product({
        id: item.id,
        title: item.title,
        price: item.price,
        quantity: item.quantity
    }));
}


function buildTicketHtml(items) {
    const date = new Date();
    const texts = getCartTexts();

    const formattedDate =
        date.toLocaleDateString('es-ES') +
        ' ' +
        date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    
    let linesHtml = '';

    const products = mapCartItemsToProducts(items);

    let grandTotal = 0;

    products.forEach((product) => {
        const lineTotal = product.lineTotal;
        grandTotal += lineTotal;

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


    return `
        <div class="ticket">
            <h3 class="ticket__title">${texts.ticketTitle}</h3>
            <p class="ticket__date">${formattedDate}</p>

            <div class="ticket__items">
                ${linesHtml}
            </div>

            <div class="ticket__totals">
                <p class="ticket__subtotal">
                    ${texts.subtotalLabel}: <span data-ticket-subtotal>${formatPrice(grandTotal)}</span>
                </p>

                <p class="ticket__discount" hidden>
                    ${texts.discountLabel}: <span data-ticket-discount>0,00 €</span>
                </p>

                <p class="ticket__grand-total">
                    ${texts.totalLabel}: <span data-ticket-total>${formatPrice(grandTotal)}</span>
                </p>
            </div>
        </div>
    `;
}




/**
 * Render principal del carrito.
 */
function renderCart() {
    const dom = getCartDomRefs();
    if (!dom) return;

    const { itemsContainer } = dom;
    const items = getCartItems();

    itemsContainer.innerHTML = '';

    if (!items.length) {
        renderEmptyCart(dom);
        return;
    }

    items.forEach((item) => {
        const article = createCartItemElement(item);
        itemsContainer.appendChild(article);
    });

    if (items.length) {
        const header = document.querySelector('.cart__header');
        if (header) header.style.display = '';
    }

    updateCartTotals(dom);
}


export function initCartPage() {
    const dom = getCartDomRefs();
    if (!dom) return;

    renderCart();

    if (dom.checkoutBtn) {
        dom.checkoutBtn.addEventListener('click', () => {
            handleCheckout(dom);
        });
    }

    if (dom.promoApplyBtn && dom.promoInput) {
        dom.promoApplyBtn.addEventListener('click', () => {
            const code = dom.promoInput.value.trim();
            handlePromoCode(dom, code);
        });
    }
}


/**
 * Obtiene y agrupa las referencias del DOM
 * de la página de carrito.
 */
function getCartDomRefs() {
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


/**
 * Pinta el estado de carrito vacío.
 */
function renderEmptyCart(dom) {
    const { itemsContainer, totalQtyEl, totalPriceEl, checkoutBtn } = dom;
    const texts = getCartTexts();

    const header = document.querySelector('.cart__header');
    if (header) header.style.display = 'none';

    itemsContainer.innerHTML = `
        <p class="cart__empty">${texts.empty}</p>
    `;

    if (totalQtyEl) totalQtyEl.textContent = '0';
    if (totalPriceEl) totalPriceEl.textContent = formatPrice(0);
    if (checkoutBtn) checkoutBtn.disabled = true;

}


/**
 * Crea el <article> de una línea de carrito
 * y engancha los eventos.
 */
function createCartItemElement(item) {
    const article = document.createElement('article');
    article.className = 'book-card book-card--cart';
    article.dataset.bookId = item.id;

    const lineTotal = item.price * item.quantity;

    const texts = getCartTexts();

    article.innerHTML = `
        <div class="book-card__image-wrapper">
            <img
                src="${item.coverImage}"
                alt="${texts.coverAlt}"
                class="book-card__image"
            >
        </div>

        <div class="book-card__body">

            <h2 class="book-card__title">
                ${item.title}
            </h2>

            <div class="book-card__cart-row">

                <p class="book-card__price">
                    ${formatPrice(item.price)}
                </p>

                <div class="book-card__quantity">
                    <button 
                        type="button" 
                        class="book-card__qty-btn book-card__qty-btn--minus" 
                        data-cart-decrease>
                        -
                    </button>

                    <input type="number"
                        min="1"
                        class="book-card__quantity-input"
                        value="${item.quantity}"
                        data-cart-quantity>

                    <button 
                        type="button" 
                        class="book-card__qty-btn book-card__qty-btn--plus" 
                        data-cart-increase>
                        +
                    </button>
                </div>

                <p 
                    class="book-card__line-total"
                    data-cart-line-total
                >
                    ${formatPrice(lineTotal)}
                </p>

                <button 
                    type="button"
                    class="book-card__remove-btn"
                    data-cart-remove>
                    
                    <svg
                        class="book-card__remove-icon"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M10 2L9 3H4V5H5V20C5 20.5222 5.19133 21.0546 5.56836 21.4316C5.94539 21.8087 6.47778 22 7 22H17C17.5222 22 18.0546 21.8087 18.4316 21.4316C18.8087 21.0546 19 20.5222 19 20V5H20V3H15L14 2H10ZM7 5H17V20H7V5ZM9 7V18H11V7H9ZM13 7V18H15V7H13Z"
                            fill="currentColor"
                        />
                    </svg>

                </button>

            </div>

        </div>
    `;


    attachCartItemEvents(article, item.id);

    return article;
}


/**
 * Engancha los eventos de cantidad y eliminar
 * de una línea de carrito.
 */
function attachCartItemEvents(article, itemId) {
    const qtyInput = article.querySelector('[data-cart-quantity]');
    const removeBtn = article.querySelector('[data-cart-remove]');
    const minusBtn = article.querySelector('[data-cart-decrease]');
    const plusBtn = article.querySelector('[data-cart-increase]');

    // --- Input manual ---
    if (qtyInput) {
        qtyInput.addEventListener('change', (event) => {
            const value = Number(event.target.value);
            const safeValue =
                Number.isInteger(value) && value > 0 ? value : 1;

            event.target.value = String(safeValue);

            setQuantity(itemId, safeValue);
            renderCart();
        });
    }

    // --- Botón "-" ---
    if (minusBtn) {
        minusBtn.addEventListener('click', () => {
            const current = Number(qtyInput.value) || 1;
            const newValue = Math.max(1, current - 1);

            qtyInput.value = newValue;
            setQuantity(itemId, newValue);
            renderCart();
        });
    }

    // --- Botón "+" ---
    if (plusBtn) {
        plusBtn.addEventListener('click', () => {
            const current = Number(qtyInput.value) || 1;
            const newValue = current + 1;

            qtyInput.value = newValue;
            setQuantity(itemId, newValue);
            renderCart();
        });
    }

    // --- Botón eliminar ---
    if (removeBtn) {
        removeBtn.addEventListener('click', () => {
            removeItem(itemId);
            renderCart();
        });
    }
}


/**
 * Actualiza los totales en el resumen.
 */
function updateCartTotals(dom) {
    const { totalQtyEl, totalPriceEl, checkoutBtn } = dom;
    const totals = getTotals();

    if (totalQtyEl) totalQtyEl.textContent = String(totals.totalQuantity);
    if (totalPriceEl) totalPriceEl.textContent = formatPrice(totals.totalPrice);
    if (checkoutBtn) checkoutBtn.disabled = totals.totalQuantity === 0;
}



function handleCheckout(dom) {
    const items = getCartItems();
    const texts = getCartTexts();

    if (!items.length) {
        alert(texts.alertEmpty);
        return;
    }

    const totals = getTotals();
    const ticketHtml = buildTicketHtml(items, totals);

    dom.ticketEl.innerHTML = ticketHtml;
    dom.ticketEl.hidden = false;

    const purchasedIds = items.map((item) => item.id);

    clearCart();
    renderCart();

    sendWishlistCleanup(purchasedIds);
}


function handlePromoCode(dom, code) {
    const trimmed = (code || '').trim();

    if (!trimmed) {
        if (dom.promoMessage) {
            dom.promoMessage.hidden = true;
            dom.promoMessage.textContent = '';
        }
        return;
    }

    console.log('Promo code entered:', trimmed);

    // Más adelante aquí:
    // - validaremos el código
    // - calcularemos el descuento
    // - actualizaremos subtotal / descuento / total
    // - mostraremos el mensaje al usuario
}


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
        // Se ignora por el momento.
    });

}