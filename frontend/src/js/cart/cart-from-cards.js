
// ==================================================
//  ADD-TO-CART FROM BOOK CARDS
//  - Detecta clicks en botones de card
//  - Extrae datos del libro desde data-*
//  - Llama a addItem() del carrito
// ==================================================

import { addItem } from "./cart-storage.js";



// ==================================================
//  INICIALIZACIÓN
// ==================================================

/**
 * Conecta los botones "Añadir al carrito" de cada card
 * con la lógica del carrito.
 *
 * Cada botón debe estar dentro de un elemento .book-card
 * que contiene data-* con la info del producto.
 */
export function initCartFromCards() {

    const buttons = document.querySelectorAll('.book-card__btn--cart');
    if (!buttons.length) return;

    buttons.forEach((button) => {
        button.addEventListener('click', () => {

            const card = button.closest('.book-card');
            if (!card) return;

            // Datos del libro desde los data-attributes de la card
            const id = card.dataset.bookId;
            const title = card.dataset.bookTitle ?? '';
            const priceRaw = card.dataset.bookPrice ?? '0';

            const img = card.querySelector('.book-card__image');
            const coverImage = img ? img.getAttribute('src') || '' : '';

            if (!id) return;

            // Añadir al carrito
            addItem({
                id,
                title,
                price: Number(priceRaw),
                coverImage
            });

        });
    });
}
