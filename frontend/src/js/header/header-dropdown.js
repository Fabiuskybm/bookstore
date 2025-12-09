
// ==================================================
//  HEADER DROPDOWNS
//  - Control de menús desplegables del header
//  - Un único dropdown abierto a la vez
//  - Cierre por click fuera o tecla Escape
// ==================================================


/**
 * Inicializa los dropdowns del header.
 *
 * Cada dropdown debe tener:
 * - .header__dropdown               → contenedor
 * - .header__dropdown-trigger       → botón que abre/cierra
 * - .header__dropdown-menu          → menú desplegable (hidden por defecto)
 *
 * Se garantiza:
 * - Solo un dropdown abierto a la vez.
 * - Cierre automático al clicar fuera.
 * - Cierre con tecla Escape.
 * - ARIA: aria-expanded actualizado correctamente.
 */
export function initHeaderDropdowns() {

    const dropdowns = document.querySelectorAll('.header__dropdown');
    if (!dropdowns.length) return;

    // Controladores individuales para permitir cerrar otros dropdowns
    const controllers = [];

    dropdowns.forEach((dropdown) => {

        const trigger = dropdown.querySelector('.header__dropdown-trigger');
        const menu = dropdown.querySelector('.header__dropdown-menu');

        if (!trigger || !menu) return;

        // Controlador interno del dropdown
        const controller = {
            dropdown,
            trigger,
            menu,
            isOpen: false,
            closeMenu: () => {} // se sobrescribe más abajo
        };

        controllers.push(controller);


        // -----------------------------------------
        //  Abrir menú
        // -----------------------------------------
        const openMenu = () => {
            if (controller.isOpen) return;

            // Cerrar cualquier otro dropdown abierto
            controllers.forEach((other) => {
                if (other !== controller && other.isOpen) {
                    other.closeMenu();
                }
            });

            controller.isOpen = true;

            controller.menu.hidden = false;
            controller.menu.classList.add('header__dropdown-menu--open');
            controller.trigger.setAttribute('aria-expanded', 'true');

            document.addEventListener('click', handleDocumentClick);
            document.addEventListener('keydown', handleKeydown);
        };


        // -----------------------------------------
        //  Cerrar menú
        // -----------------------------------------
        const closeMenu = () => {
            if (!controller.isOpen) return;

            controller.isOpen = false;

            controller.menu.hidden = true;
            controller.menu.classList.remove('header__dropdown-menu--open');
            controller.trigger.setAttribute('aria-expanded', 'false');

            document.removeEventListener('click', handleDocumentClick);
            document.removeEventListener('keydown', handleKeydown);
        };

        // Guardar en el controller
        controller.closeMenu = closeMenu;


        // -----------------------------------------
        //  Alternar menú (click en trigger)
        // -----------------------------------------
        const toggleMenu = (event) => {
            event.stopPropagation(); // evita que el click cierre inmediatamente
            controller.isOpen ? closeMenu() : openMenu();
        };


        // -----------------------------------------
        //  Cerrar al clicar fuera del dropdown
        // -----------------------------------------
        const handleDocumentClick = (event) => {
            if (!controller.dropdown.contains(event.target)) {
                closeMenu();
            }
        };


        // -----------------------------------------
        //  Cerrar con tecla Escape
        // -----------------------------------------
        const handleKeydown = (event) => {
            if (event.key === 'Escape') {
                closeMenu();
                controller.trigger.focus();
            }
        };


        // -----------------------------------------
        //  EVENTOS
        // -----------------------------------------
        trigger.addEventListener('click', toggleMenu);
    });
}
