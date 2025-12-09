
// ==================================================
//  CART RENDER
//  - Helpers de formato
//  - Render de líneas y estados
//  - Actualización de totales
// ==================================================

import {
    getCartItems,
    getTotals,
    setQuantity,
    removeItem
} from './cart-storage.js';

import { Product } from './Product.js';
import { getCartDomRefs, getCartTexts } from './cart-dom.js';
import {
    calculateTotalsWithDiscount,
    getCurrentDiscountPercent
} from './cart-promo.js';



// ==================================================
//  HELPERS DE FORMATO Y MAPEO
// ==================================================

/**
 * Formatea un precio numérico a cadena con 2 decimales y símbolo €.
 */
export function formatPrice(price) {
    const num = Number(price) || 0;
    return num.toFixed(2).replace('.', ',') + ' €';
}


/**
 * Convierte items crudos del carrito en instancias de Product.
 */
export function mapCartItemsToProducts(items) {
    return items.map((item) => new Product({
        id: item.id,
        title: item.title,
        price: item.price,
        quantity: item.quantity
    }));
}



// ==================================================
//  RENDER PRINCIPAL DEL CARRITO
// ==================================================

/**
 * Render principal del carrito.
 * - Pinta las líneas del carrito.
 * - Muestra el estado vacío si no hay items.
 * - Actualiza los totales del resumen.
 */
export function renderCart() {
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

    // Mostrar cabecera de tabla si hay elementos
    if (items.length) {
        const header = document.querySelector('.cart__header');
        if (header) header.style.display = '';
    }

    updateCartTotals(dom);
}



// ==================================================
//  ESTADO DE CARRITO VACÍO
// ==================================================

/**
 * Pinta el estado de carrito vacío en la interfaz.
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



// ==================================================
//  CREACIÓN DE LÍNEAS DE CARRITO
// ==================================================

/**
 * Crea el <article> de una línea de carrito
 * y engancha los eventos correspondientes.
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



// ==================================================
//  EVENTOS DE LÍNEA (CANTIDAD Y ELIMINAR)
// ==================================================

/**
 * Engancha los eventos de cantidad y eliminar
 * de una línea de carrito.
 */
function attachCartItemEvents(article, itemId) {
    const qtyInput = article.querySelector('[data-cart-quantity]');
    const removeBtn = article.querySelector('[data-cart-remove]');
    const minusBtn = article.querySelector('[data-cart-decrease]');
    const plusBtn = article.querySelector('[data-cart-increase]');

    // Input manual de cantidad
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

    // Botón "-"
    if (minusBtn) {
        minusBtn.addEventListener('click', () => {
            const current = Number(qtyInput.value) || 1;
            const newValue = Math.max(1, current - 1);

            qtyInput.value = newValue;
            setQuantity(itemId, newValue);
            renderCart();
        });
    }

    // Botón "+"
    if (plusBtn) {
        plusBtn.addEventListener('click', () => {
            const current = Number(qtyInput.value) || 1;
            const newValue = current + 1;

            qtyInput.value = newValue;
            setQuantity(itemId, newValue);
            renderCart();
        });
    }

    // Botón eliminar
    if (removeBtn) {
        removeBtn.addEventListener('click', () => {
            removeItem(itemId);
            renderCart();
        });
    }
}



// ==================================================
//  ACTUALIZACIÓN DE TOTALES DEL RESUMEN
// ==================================================

/**
 * Actualiza los totales del resumen lateral del carrito:
 * - Cantidad total de items
 * - Importe total (con descuento aplicado, si lo hay)
 * - Estado de habilitación del botón de checkout
 */
export function updateCartTotals(dom) {
    const { totalQtyEl, totalPriceEl, checkoutBtn } = dom;

    const items = getCartItems();
    const totals = getTotals();

    const products = mapCartItemsToProducts(items);
    const { grandTotal } = calculateTotalsWithDiscount(
        products,
        getCurrentDiscountPercent()
    );

    if (totalQtyEl) totalQtyEl.textContent = String(totals.totalQuantity);
    if (totalPriceEl) totalPriceEl.textContent = formatPrice(grandTotal);
    if (checkoutBtn) checkoutBtn.disabled = totals.totalQuantity === 0;
}
