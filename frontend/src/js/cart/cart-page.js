
import {
    getCartItems,
    getTotals,
    setQuantity,
    removeItem,
    clearCart
} from './cart-storage.js';



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
        cartRemoveAlt

    } = section.dataset;


    return {
        empty: cartEmptyText || 'Your cart is empty.',
        ticketTitle: cartTicketTitle || 'Order placed.',
        alertEmpty: cartAlertEmpty || 'The cart is empty.',
        coverAlt: cartCoverAlt || 'Book cover.',
        quantityLabel: cartQuantityLabel || 'Quantity.',
        removeAlt: cartRemoveAlt || 'Remove.'
    };
}


function formatPrice(price) {
    const num = Number(price) || 0;
    return num.toFixed(2).replace('.', ',') + ' €';
}


function buildTicketHtml(items, totals) {
    const date = new Date();
    const texts = getCartTexts();

    const formattedDate =
        date.toLocaleDateString('es-ES') +
        ' ' +
        date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    
    let linesHtml = '';

    items.forEach((item) => {
        const lineTotal = item.price * item.quantity;

        linesHtml += `
            <div class="ticket__item">
                <span class="ticket__title">${item.title}</span>
                <span class="ticket__qty">${item.quantity}</span>
                <span class="ticket__total">${formatPrice(lineTotal)}</span>
            </div>
        `;
    });


    return `
        <div class="ticket">
            <h3 class="ticket__title">${texts.ticketTitle}</h3>
            <p class="ticket__date">${formattedDate}</p>

            <div class="ticket__item">
                ${linesHtml}
            </div>

            <p class="ticket__grand-total">
                Total: ${formatPrice(totals.totalPrice)}
            </p>
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

    return {
        itemsContainer,
        totalQtyEl,
        totalPriceEl,
        checkoutBtn,
        ticketEl
    };
}


/**
 * Pinta el estado de carrito vacío.
 */
function renderEmptyCart(dom) {
    const { itemsContainer, totalQtyEl, totalPriceEl, checkoutBtn } = dom;
    const texts = getCartTexts();

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

                <label class="book-card__quantity">
                    <span class="book-card__quantity-label">${texts.quantityLabel}</span>
                    <input 
                        type="number" 
                        min="1"
                        class="book-card__quantity-input"
                        value="${item.quantity}"
                        data-cart-quantity
                    >
                </label>

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