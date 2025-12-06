
import { addItem, getCartItems } from "../cart/cart-storage.js";


document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('.wishlist__form');
    if (!form) return;

    const selectAllCheckbox = form.querySelector('.wishlist__select-all-input');
    const itemCheckboxes = form.querySelectorAll('.book-card__select-input');
    const errorBox = form.querySelector('[data-wishlist-error]');
    const addToCartBtn = form.querySelector('.wishlist__btn--cart');


    if (!selectAllCheckbox || itemCheckboxes.length === 0) return;

    const clearError = () => {
        if (errorBox) errorBox.textContent = '';
    }

    const showError = (msg) => {
        if (!errorBox) {
            alert(msg);
            return;
        }

        errorBox.textContent = msg;

        setTimeout(() => {
            errorBox.textContent = '';
        }, 3000);
    }

    const anyChecked = () =>
        Array.from(itemCheckboxes).some((item) => item.checked);


    // ======================
    // |  Seleccionar todo  |
    // ======================

    selectAllCheckbox.addEventListener('change', () => {
        const checked = selectAllCheckbox.checked;

        itemCheckboxes.forEach((checkbox) => {
            checkbox.checked = checked;
        });

        if (checked) clearError();
    });


    itemCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {

            const allChecked = Array
                .from(itemCheckboxes)
                .every((item) => item.checked);
            
            selectAllCheckbox.checked = allChecked;
            
            if (anyChecked()) clearError();
        });
    });


    // =================================
    // |  Validación botón "Eliminar"  |
    // =================================

    form.addEventListener('submit', (event) => {

        const submitter = event.submitter;
        const action = submitter?.value ?? '';

        if (action === 'wishlist_clear') return;
            
        if (!anyChecked()) {
            event.preventDefault();
            showError('Debes seleccionar al menos un libro.');
        }

    });


    // ===============================
    // |  Botón "Añadir al carrito"  |
    // ===============================

    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => {

            const selected = Array
                .from(itemCheckboxes)
                .filter((checkbox) => checkbox.checked);

            if (selected.length === 0) {
                showError('Debes seleccionar al menos un libro.');
                return;
            }

            const cartItems = getCartItems();
            const cartIds = new Set(cartItems.map((item) => item.id));

            let addedCount = 0;

            selected.forEach((checkbox) => {
                const card = checkbox.closest('.book-card');
                if (!card) return;

                const bookId = card.dataset.bookId;
                if (!bookId ||cartIds.has(bookId)) return;

                const img = card.querySelector('.book-card__image');

                const itemData = {
                    id: card.dataset.bookId,
                    title: card.dataset.bookTitle,
                    price: Number(card.dataset.bookPrice),
                    coverImage: img ? img.getAttribute('src') : ''
                };

                addItem(itemData);
                addedCount += 1;
            });

            if (addedCount === 0) {
                showError('Todos los libros seleccionados ya están en el carrito.');
                return;
            }

            selectAllCheckbox.checked = false;
            itemCheckboxes.forEach((c) => { c.checked = false; });
            clearError();

            window.location.href = 'index.php?view=cart';
        });
    }

});