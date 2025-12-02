

export function initHeaderUserMenu() {

    const headerUser = document.querySelector('.header__user');
    if (!headerUser) return;

    const trigger = headerUser.querySelector('.header__user-trigger');
    const menu = headerUser.querySelector('.header__user-menu');

    if (!trigger || !menu) return;


    let isOpen = false;

    const openMenu = () => {
        if (isOpen) return;
        isOpen = true;

        menu.hidden = false;
        menu.classList.add('header__user-menu--open');
        trigger.setAttribute('aria-expanded', 'true');

        document.addEventListener('click', handleDocumentClick);
        document.addEventListener('keydown', handleKeydown);
    };

    
    const closeMenu = () => {
        if (!isOpen) return;
        isOpen = false;

        menu.hidden = true;
        menu.classList.remove('header__user-menu--open');
        trigger.setAttribute('aria-expanded', 'false');

        document.removeEventListener('click', handleDocumentClick);
        document.removeEventListener('keydown', handleKeydown);
    };


    const toggleMenu = (event) => {
        event.stopPropagation();
        isOpen ? closeMenu() : openMenu();
    };


    const handleDocumentClick = (event) => {
        if (!headerUser.contains(event.target)) closeMenu();
    };


    const handleKeydown = (event) => {

        if (event.key === 'Escape') {
            closeMenu();
            trigger.focus();
        }
    };


    trigger.addEventListener('click', toggleMenu);
}