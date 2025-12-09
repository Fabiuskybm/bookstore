
// ==================================================
//  PREFERENCES
//  - Persistencia por usuario vía user-storage.js
//  - Aplicación de atributo data-view-mode al <html>
//  - UI de botones toggle en la página de preferencias
// ==================================================

import {
    loadUserJson,
    mergeUserJson
} from '../shared/user-storage.js';



// ==================================================
//  PREFS STORAGE
// ==================================================

const PREFS_PREFIX = 'prefs_';

/**
 * Carga las preferencias JS del usuario actual.
 */
function loadJsPrefs() {
    return loadUserJson(PREFS_PREFIX);
}

/**
 * Fusiona y guarda preferencias JS.
 */
function saveJsPrefs(partialPrefs) {
    mergeUserJson(PREFS_PREFIX, partialPrefs);
}



// ==================================================
//  VIEW MODE (normal / compact)
// ==================================================

/**
 * Devuelve el modo de vista almacenado.
 * Valores válidos: "normal" (default) o "compact".
 */
function getViewMode() {
    const prefs = loadJsPrefs();
    return prefs.viewMode === 'compact' ? 'compact' : 'normal';
}

/**
 * Guarda el modo de vista.
 */
function saveViewMode(mode) {
    const safeMode = mode === 'compact' ? 'compact' : 'normal';
    saveJsPrefs({ viewMode: safeMode });
}

/**
 * Aplica el modo de vista al documento mediante:
 * <html data-view-mode="compact|normal">
 */
function applyViewModeToDocument(mode) {
    const root = document.documentElement;
    const safeMode = mode === 'compact' ? 'compact' : 'normal';
    root.setAttribute('data-view-mode', safeMode);
}

/**
 * Actualiza el estado de los botones del toggle.
 * Mantiene aria-pressed e indicador visual.
 */
function updateToggleUI(container, mode) {
    if (!container) return;

    const buttons = container.querySelectorAll('[data-view-mode]');
    buttons.forEach((btn) => {
        const isActive = btn.dataset.viewMode === mode;

        btn.classList.toggle(
            'preferences__view-button--active',
            isActive
        );

        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
}



// ==================================================
//  INITIALIZATION
// ==================================================

/**
 * Inicializa la preferencia de modo de vista.
 *
 * - Aplica el modo al documento.
 * - Si estamos en la página de preferencias, activa UI + listeners.
 */
export function initViewModePreference() {
    const initialMode = getViewMode();

    // El modo se aplica siempre (home, wishlist, cart…)
    applyViewModeToDocument(initialMode);

    // El toggler solo existe en la vista de preferencias
    const container = document.querySelector('[data-view-toggle]');
    if (!container) return;

    updateToggleUI(container, initialMode);

    const buttons = container.querySelectorAll('[data-view-mode]');
    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const mode =
                btn.dataset.viewMode === 'compact'
                    ? 'compact'
                    : 'normal';

            saveViewMode(mode);
            applyViewModeToDocument(mode);
            updateToggleUI(container, mode);
        });
    });
}
