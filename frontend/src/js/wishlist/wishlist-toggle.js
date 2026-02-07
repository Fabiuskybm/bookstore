// ==================================================
//  WISHLIST TOGGLE (AJAX)
//  - Evita recarga al pulsar el corazón / botón wishlist
//  - Hace POST por fetch y actualiza el estado visual
// ==================================================

export function initWishlistToggle() {
    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;

        // Permitimos tanto cards como la nueva vista de detalle.
        const isCardForm = form.classList.contains('book-card__wishlist-form');
        const isDetailForm = form.classList.contains('product-detail__wishlist-form');
        if (!isCardForm && !isDetailForm) return;

        event.preventDefault();

        const btn = form.querySelector('.book-card__btn--wishlist, .product-detail__wishlist-btn');
        const errorBox = document.querySelector('[data-wishlist-error]');
        const formData = new FormData(form);

        // Usamos una acción "toggle" única.
        formData.set('action', 'wishlist_toggle');

        try {
            const res = await fetch('index.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (res.status === 401) {
                // No autenticado → ir a login.
                window.location.href = 'index.php?view=login';
                return;
            }

            const data = await res.json();

            if (!data?.ok) {
                if (errorBox) errorBox.textContent = 'No se pudo actualizar la wishlist.';
                return;
            }

            // Actualizar estado visual reutilizando las clases existentes.
            if (btn) {
                btn.classList.toggle('book-card__btn--wishlist-active', !!data.inWishlist);
                btn.setAttribute('aria-pressed', data.inWishlist ? 'true' : 'false');
            }

        } catch (e) {
            if (errorBox) errorBox.textContent = 'Error de red al actualizar la wishlist.';
        }
    });
}
