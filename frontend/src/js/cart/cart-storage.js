
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




// ==========================
// |  Funciones de Dominio  |
// ==========================


/**
 * Devuelve los items del carrito.
 */
function getCartItems() { return loadCart(); }


/**
 * Añade un producto al carrito.
 * itemData = { id, title, price, coverImage }
 */
function addItem(itemData) {
    if (!itemData || typeof itemData.id !== 'string' ) return;

    const items = loadCart();
    const existing = items.find((i) => i.id === itemData.id);

    // Si existe, aumenta la cantidad.
    if (existing) {
        existing.quantity += 1;
        saveCart(items);
        return;
    }

    // Si no existe, añadir nuevo con quantity = 1
    const newItem = normalizeCartItem({
        ...itemData,
        quantity: 1
    });

    if (!newItem) return;

    items.push(newItem);
    saveCart(items);
}



/**
 * Elimina un producto del carrito por id.
 */
function removeItem(id) {
    if (!id) return;

    const items = loadCart();
    const filtered = items.filter((item) => item.id !== id);

    saveCart(filtered);
}


/**
 * Cambia la cantidad de un producto.
 * cantidad mínima: 1
 */
function setQuantity(id, quantity) {
    if (!id) return;

    const qty = Number(quantity);
    if (!Number.isInteger(qty) || qty < 1) return;

    const items = loadCart();
    const item = items.find((i) => i.id === id);
    if (!item) return;

    item.quantity = qty;
    saveCart(items);
}


/**
 * Vacía completamente el carrito.
 */
function clearCart() { saveCart([]); }


/**
 * Calcula los totales del carrito.
 * Devuelve { totalQuantity, totalPrice }
 */
function getTotals() {
    const items = loadCart();

    let totalQuantity = 0;
    let totalPrice = 0;

    for (const item of items) {
        totalQuantity += item.quantity;
        totalPrice += item.price * item.quantity;
    }

    return { totalQuantity, totalPrice };
}



export { 
    loadCart,
    saveCart,
    getCartItems,
    addItem,
    removeItem,
    setQuantity,
    clearCart,
    getTotals
};
