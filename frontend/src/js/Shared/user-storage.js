

/**
 * Obtiene la clave única del usuario actual.
 * 
 * Se basa en <body data-user="username"> igual que cart-storage.js.
 * Si no existe, usa "guest".
 */
export function getCurrentUserKey() {
    const body = document.body;
    const key = body?.dataset.user?.trim();
    return key && key !== '' ? key.toLowerCase() : 'guest';
}


/**
 * Construye una clave completa de localStorage con prefijo.
 * Ej:
 *   prefix = "prefs_" → prefs_username
 *   prefix = "cart_"  → cart_username
 */
export function buildUserStorageKey(prefix) {
    return `${prefix}${getCurrentUserKey()}`;
}


/**
 * Lee un JSON desde localStorage bajo una clave por usuario.
 * Devuelve {} si no existe o está corrupto.
 */
export function loadUserJson(prefix) {
    const key = buildUserStorageKey(prefix);
    const raw = localStorage.getItem(key);
    if (!raw) return {};

    try {
        const parsed = JSON.parse(raw);
        return parsed && typeof parsed === 'object' ? parsed : {};
    } catch {
        return {};
    }
}


/**
 * Guarda un objeto JSON completo bajo la clave del usuario.
 */
export function saveUserJson(prefix, data) {
    const key = buildUserStorageKey(prefix);
    localStorage.setItem(key, JSON.stringify(data));
}


/**
 * Mezcla datos parciales con el JSON existente.
 * Útil para añadir prefs sin pisar las anteriores.
 */
export function mergeUserJson(prefix, partial) {
    const current = loadUserJson(prefix);
    const next = { ...current, ...partial };
    saveUserJson(prefix, next);
}
