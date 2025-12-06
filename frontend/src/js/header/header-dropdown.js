// frontend/src/js/header/header-dropdown.js

export function initHeaderDropdowns() {

    const dropdowns = document.querySelectorAll('.header__dropdown');
    if (!dropdowns.length) return;

    const controllers = [];

    dropdowns.forEach((dropdown) => {

        const trigger = dropdown.querySelector('.header__dropdown-trigger');
        const menu = dropdown.querySelector('.header__dropdown-menu');

        if (!trigger || !menu) return;

        const controller = {
            dropdown,
            trigger,
            menu,
            isOpen: false,
            closeMenu: () => {}
        };

        controllers.push(controller);

        const openMenu = () => {
            if (controller.isOpen) return;

            // Cerrar otros dropdowns abiertos
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

        const closeMenu = () => {
            if (!controller.isOpen) return;

            controller.isOpen = false;

            controller.menu.hidden = true;
            controller.menu.classList.remove('header__dropdown-menu--open');
            controller.trigger.setAttribute('aria-expanded', 'false');

            document.removeEventListener('click', handleDocumentClick);
            document.removeEventListener('keydown', handleKeydown);
        };

        controller.closeMenu = closeMenu;

        const toggleMenu = (event) => {
            event.stopPropagation();
            controller.isOpen ? closeMenu() : openMenu();
        };

        const handleDocumentClick = (event) => {
            if (!controller.dropdown.contains(event.target)) {
                closeMenu();
            }
        };

        const handleKeydown = (event) => {
            if (event.key === 'Escape') {
                closeMenu();
                controller.trigger.focus();
            }
        };

        trigger.addEventListener('click', toggleMenu);
    });
}
