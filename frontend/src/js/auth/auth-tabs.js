
// ==================================================
//  AUTH TABS
//  - Alterna entre pestañas Login / Register
//  - Maneja ARIA + clases BEM
// ==================================================


/**
 * Inicializa el componente de pestañas en la vista de Auth.
 *
 * - Cambia entre tabs al hacer clic.
 * - Muestra/oculta paneles correspondientes.
 * - Actualiza atributos ARIA para accesibilidad.
 */
export function initAuthTabs() {

    const auth = document.querySelector('.auth');
    if (!auth) return;

    const tabs = auth.querySelectorAll('.auth__tab');
    const panels = auth.querySelectorAll('.auth__panel');

    // Si no hay tabs o no hay paneles → nada que hacer
    if (!tabs.length || !panels.length) return;

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {

            const isActive = tab.classList.contains('auth__tab--active');
            if (isActive) return;

            const targetId = tab.getAttribute('aria-controls');

            const targetPanel = targetId
                ? auth.querySelector(`#${targetId}`)
                : null;
            
            if (!targetPanel) return;


            // -----------------------------------------
            //  Desactivar todas las pestañas
            // -----------------------------------------
            tabs.forEach((t) => {
                t.classList.remove('auth__tab--active');
                t.setAttribute('aria-selected', 'false');
            });


            // -----------------------------------------
            //  Ocultar todos los paneles
            // -----------------------------------------
            panels.forEach((panel) => {
                panel.classList.remove('auth__panel--active');
                panel.hidden = true;
            });


            // -----------------------------------------
            //  Activar pestaña clicada
            // -----------------------------------------
            tab.classList.add('auth__tab--active');
            tab.setAttribute('aria-selected', 'true');


            // -----------------------------------------
            //  Mostrar panel correspondiente
            // -----------------------------------------
            targetPanel.classList.add('auth__panel--active');
            targetPanel.hidden = false;

        });
    });
}
