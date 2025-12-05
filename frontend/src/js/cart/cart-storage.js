
const CART_PREFIX = 'cart_';


/**
 * Devuelve la clave de LocalStorage para el carrito
 * del usuario actual.
 * 
 * Usa data-user en <body>, o 'guest' si no existe.
 */
function getCartKey() {
    const body = document.body;
    const username = (body?.dataset.user || 'guest').trim() || 'guest';

    return `${CART_PREFIX}${username}`;
}


/**
 * Normaliza un item del carrito.
 * Devuelve un objeto limpio o null si no es válido.
 */
function normalizeCartItem(item) {
    if (!item || typeof item.id !== 'string') return null;

    const id = String(item.id).trim();
    if (id === '') return null;

    const priceNumber = Number(item.price);
    const quantityNumber = Number(item.quantity);

    const quantityValid =
        Number.isInteger(quantityNumber) && quantityNumber > 0
            ? quantityNumber
            : 1;
    
    return {
        id,
        title: String(item.title ?? ''),
        price: Number.isFinite(priceNumber) ? priceNumber : 0,
        coverImage: String(item.coverImage ?? ''),
        quantity: quantityValid
    };
}


/**
 * Lee el carrito de LocalStorage.
 */
function loadCart() {
    const key = getCartKey();
    const raw = localStorage.getItem(key);

    if (!raw) return [];

    try {
        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed)) return [];

        return parsed
            .map(normalizeCartItem)
            .filter((item) => item !== null);

    } catch {
        return [];
    }
}


/**
 * Guarda el carrito en LocalStorage.
 * Si el array está vacío o todo se invalida, elimina la entrada.
 */
function saveCart(items) {
    const key = getCartKey();

    if (!Array.isArray(items) || items.length === 0) {
        localStorage.removeItem(key);
        return;
    }

    const cleanItems = items
        .map(normalizeCartItem)
        .filter((item) => item !== null);

    if (cleanItems.length === 0) {
        localStorage.removeItem(key);
        return;
    }

    localStorage.setItem(key, JSON.stringify(cleanItems));
}



export { loadCart, saveCart };