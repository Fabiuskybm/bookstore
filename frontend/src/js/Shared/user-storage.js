
// ==================================================
//  USER STORAGE UTILITIES
//  - Claves por usuario en localStorage
//  - Lectura / escritura de JSON
//  - Mezcla de datos parciales
// ==================================================


// ==================================================
//  USUARIO ACTUAL
// ==================================================

/**
 * Obtiene la clave única del usuario actual.
 *
 * Basado en:
 *   <body data-user="username">
 *
 * Si no existe → usa "guest".
 */
export function getCurrentUserKey() {
    const body = document.body;
    const key = body?.dataset.user?.trim();

    return key && key !== '' ? key.toLowerCase() : 'guest';
}



// ==================================================
//  GENERACIÓN DE CLAVES POR USUARIO
// ==================================================

/**
 * Construye una clave de localStorage con un prefijo y el usuario actual.
 *
 * Ejemplos:
 *   prefix = "prefs_" → "prefs_username"
 *   prefix = "cart_"  → "cart_username"
 */
export function buildUserStorageKey(prefix) {
    return `${prefix}${getCurrentUserKey()}`;
}



// ==================================================
//  LECTURA Y ESCRITURA DE JSON
// ==================================================

/**
 * Lee un JSON desde localStorage asociado al usuario actual.
 * Devuelve {} si no existe o si el contenido está corrupto.
 */
export function loadUserJson(prefix) {
    const key = buildUserStorageKey(prefix);
    const raw = localStorage.getItem(key);

    if (!raw) return {};

    try {
        const parsed = JSON.parse(raw);
        return parsed && typeof parsed === 'object' ? parsed : {};
    } catch {
        // Si falla el parseo, el JSON se trata como vacío
        return {};
    }
}


/**
 * Guarda un objeto completo como JSON bajo la clave del usuario.
 */
export function saveUserJson(prefix, data) {
    const key = buildUserStorageKey(prefix);
    localStorage.setItem(key, JSON.stringify(data));
}



// ==================================================
//  MERGE DE JSON (actualización parcial)
// ==================================================

/**
 * Mezcla datos parciales con el JSON existente bajo el mismo prefijo.
 *
 * Útil para:
 * - Añadir o modificar una preferencia
 * - Actualizar estructuras de usuario manteniendo otras intactas
 */
export function mergeUserJson(prefix, partial) {
    const current = loadUserJson(prefix);
    const next = { ...current, ...partial };
    saveUserJson(prefix, next);
}
