

import {
    loadUserJson,
    mergeUserJson
} from '../shared/user-storage.js';


const PREFS_PREFIX = 'prefs_';



// ---------- helpers de prefs JS ----------

function loadJsPrefs() {
    return loadUserJson(PREFS_PREFIX);
}

function saveJsPrefs(partialPrefs) {
    mergeUserJson(PREFS_PREFIX, partialPrefs);
}




// ---------- view mode ----------

function getViewMode() {
    const prefs = loadJsPrefs();
    return prefs.viewMode === 'compact' ? 'compact' : 'normal';
}


function saveViewMode(mode) {
    const safeMode = mode === 'compact' ? 'compact' : 'normal';
    saveJsPrefs({ viewMode: safeMode });
}


function applyViewModeToDocument(mode) {
    const root = document.documentElement;
    const safeMode = mode === 'compact' ? 'compact' : 'normal';

    root.setAttribute('data-view-mode', safeMode);
}


function updateToggleUI(container, mode) {
    if (!container) return;

    const buttons = container.querySelectorAll('[data-view-mode]');
    buttons.forEach((btn) => {
        const isActive = btn.dataset.viewMode === mode;
        btn.classList.toggle('preferences__view-button--active', isActive);
        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
}



// ---------- inicialización pública ----------

export function initViewModePreference() {
    const initialMode = getViewMode();

    // Aplicar siempre a la página actual (home, wishlist, cart…)
    applyViewModeToDocument(initialMode);

    // Solo habrá toggle en la página de preferencias
    const container = document.querySelector('[data-view-toggle]');
    if (!container) return;

    updateToggleUI(container, initialMode);

    const buttons = container.querySelectorAll('[data-view-mode]');
    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const mode = btn.dataset.viewMode === 'compact' ? 'compact' : 'normal';

            saveViewMode(mode);
            applyViewModeToDocument(mode);
            updateToggleUI(container, mode);
        });
    });
}
