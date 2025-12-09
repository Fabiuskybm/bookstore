
// ==================================================
//  BOOK CAROUSEL
//  - Carrusel de tarjetas de libro
//  - Navegación por páginas (prev/next + dots)
// ==================================================


// ==================================================
//  SETUP DE UN CARRUSEL INDIVIDUAL
// ==================================================

/**
 * Configura el carrusel.
 *
 * Estructura esperada:
 * - root [data-book-carousel]
 *   - .book-carousel__viewport
 *   - [data-book-carousel-track] (contiene .book-card)
//   - [data-book-carousel-prev]
//   - [data-book-carousel-next]
//   - [data-book-carousel-dots]
 */
function setupCarousel(root) {
    const viewport = root.querySelector('.book-carousel__viewport');
    const track = root.querySelector('[data-book-carousel-track]');
    const prevBtn = root.querySelector('[data-book-carousel-prev]');
    const nextBtn = root.querySelector('[data-book-carousel-next]');
    const dotsContainer = root.querySelector('[data-book-carousel-dots]');

    if (!viewport || !track || !prevBtn || !nextBtn || !dotsContainer) return;

    const cards = track.querySelectorAll('.book-card');
    if (!cards.length) return;


    // ==================================================
    //  CONFIGURACIÓN
    // ==================================================
    const cardsPerPage = 3;
    const totalPages = Math.ceil(cards.length / cardsPerPage);

    let currentPage = 0;


    // ==================================================
    //  CREACIÓN DE DOTS
    // ==================================================
    const dots = [];
    dotsContainer.innerHTML = '';

    for (let i = 0; i < totalPages; i++) {
        const dot = document.createElement('button');
        dot.className = 'book-carousel__dot';
        dot.setAttribute('type', 'button');
        dot.dataset.index = i;

        dotsContainer.appendChild(dot);
        dots.push(dot);

        dot.addEventListener('click', () => {
            goToPage(i);
        });
    }


    // ==================================================
    //  ACTUALIZACIÓN DE DOTS
    // ==================================================

    /**
     * Marca visualmente el dot activo según currentPage.
     */
    function updateDots() {
        dots.forEach((dot, i) => {
            dot.classList.toggle('book-carousel__dot--active', i === currentPage);
        });
    }


    // ==================================================
    //  NAVEGACIÓN POR PÁGINAS
    // ==================================================

    /**
     * Desplaza el carrusel a la página indicada.
     */
    function goToPage(pageIndex) {
        currentPage = Math.max(0, Math.min(pageIndex, totalPages - 1));

        const cardWidth = cards[0].offsetWidth;
        const styles = window.getComputedStyle(track);
        const gap = parseFloat(styles.gap || 0);

        const step = cardWidth * cardsPerPage + gap * cardsPerPage;

        viewport.scrollTo({
            left: currentPage * step,
            behavior: 'smooth'
        });

        updateDots();
    }


    // ==================================================
    //  BOTONES PREV / NEXT
    // ==================================================
    prevBtn.addEventListener('click', () => {
        goToPage(currentPage - 1);
    });

    nextBtn.addEventListener('click', () => {
        goToPage(currentPage + 1);
    });


    // ==================================================
    //  INICIALIZACIÓN DEL CARRUSEL
    // ==================================================
    goToPage(0);
}



// ==================================================
//  INICIALIZACIÓN GLOBAL
// ==================================================

/**
 * Busca todos los carruseles en la página y los inicializa.
 */
export function initBookCarousel() {
    const carousels = document.querySelectorAll('[data-book-carousel]');
    if (!carousels.length) return;

    carousels.forEach(setupCarousel);
}
