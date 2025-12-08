
import { getCartItems } from './cart-storage.js';


export function updateCartBadge() {
    const badge = document.querySelector('[data-cart-badge]');
    if (!badge) return;

    const VISIBLE_CLASS = 'header__cart-badge--visible';

    const items = getCartItems();
    const total = items.reduce((sum, item) => sum + (item.quantity || 0), 0);

    if (total > 0) {
        badge.textContent = String(total);
        badge.classList.add(VISIBLE_CLASS);
    } else {
        badge.textContent = '';
        badge.classList.remove(VISIBLE_CLASS);
    }
}


export function initCartBadge() {
    updateCartBadge();

    const animateCartIcon = () => {
        const cartAction = document.querySelector('.header__action--cart');
        if (!cartAction) return;

        cartAction.classList.remove('header__action--cart--animating');
        
        // Forzar reflow para reiniciar la animación
        void cartAction.offsetWidth;
        cartAction.classList.add('header__action--cart--animating');
    };

    window.addEventListener('cart:updated', () => {
        updateCartBadge();
        animateCartIcon();
    });
}

