
// ==================================================
//  SCROLL TO TOP BUTTON
//  - Muestra/oculta el botón según el scroll
//  - Desplaza suavemente al inicio de la página
// ==================================================

/**
 * Inicializa el botón "scroll to top".
 *
 * Requiere un elemento con:
 *   [data-scroll-top]
 *
 * Agrega:
 * - Visibilidad basada en scrollY
 * - Scroll suave al hacer clic
 */
export function initScrollTop() {
    const button = document.querySelector('[data-scroll-top]');
    if (!button) return;

    const VISIBLE_CLASS = 'scroll-top--visible';
    const SCROLL_THRESHOLD = 300;

    // -----------------------------------------
    //  Mostrar/ocultar botón al hacer scroll
    // -----------------------------------------
    const handleScroll = () => {
        if (window.scrollY > SCROLL_THRESHOLD) {
            button.classList.add(VISIBLE_CLASS);
        } else {
            button.classList.remove(VISIBLE_CLASS);
        }
    };

    // -----------------------------------------
    //  Ir arriba suavemente
    // -----------------------------------------
    const handleClick = () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };

    // -----------------------------------------
    //  EVENTOS
    // -----------------------------------------
    window.addEventListener('scroll', handleScroll);
    button.addEventListener('click', handleClick);

    // Estado inicial (por si entramos ya en scroll)
    handleScroll();
}
