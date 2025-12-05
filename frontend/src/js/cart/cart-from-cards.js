
import { addItem } from "./cart-storage.js";


export function initCartFromCards() {

    const buttons = document.querySelectorAll('.book-card__btn--cart');
    if (!buttons.length) return;

    buttons.forEach((button) => {
        button.addEventListener('click', () => {

            const card = button.closest('.book-card');
            if (!card) return;

            const id = card.dataset.bookId;
            const title = card.dataset.bookTitle ?? '';
            const priceRaw = card.dataset.bookPrice ?? '0';

            const img = card.querySelector('.book-card__image');
            const coverImage = img ? img.getAttribute('src') || '' : '';

            if (!id) return;

            addItem({
                id,
                title,
                price: Number(priceRaw),
                coverImage
            });

        });
    });
}