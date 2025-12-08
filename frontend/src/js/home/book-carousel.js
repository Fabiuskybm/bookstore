
function setupCarousel(root) {
    const viewport = root.querySelector('.book-carousel__viewport');
    const track = root.querySelector('[data-book-carousel-track]');
    const prevBtn = root.querySelector('[data-book-carousel-prev]');
    const nextBtn = root.querySelector('[data-book-carousel-next]');
    const dotsContainer = root.querySelector('[data-book-carousel-dots]');

    if (!viewport || !track || !prevBtn || !nextBtn || !dotsContainer) return;

    const cards = track.querySelectorAll('.book-card');
    if (!cards.length) return;

    // ---- CONFIGURACIÓN ----
    const cardsPerPage = 3; // puedes ajustar esto
    const totalPages = Math.ceil(cards.length / cardsPerPage);

    let currentPage = 0;

    // ---- CREAR DOTS ----
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

    // ---- ACTUALIZAR DOTS ----
    function updateDots() {
        dots.forEach((dot, i) => {
            dot.classList.toggle('book-carousel__dot--active', i === currentPage);
        });
    }

    // ---- IR A UNA PÁGINA ----
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

    // ---- BOTONES PREV/NEXT ----
    prevBtn.addEventListener('click', () => {
        goToPage(currentPage - 1);
    });

    nextBtn.addEventListener('click', () => {
        goToPage(currentPage + 1);
    });

    // ---- INICIAR ----
    goToPage(0);
}


export function initBookCarousel() {
    const carousels = document.querySelectorAll('[data-book-carousel]');
    if (!carousels.length) return;

    carousels.forEach(setupCarousel);
}
