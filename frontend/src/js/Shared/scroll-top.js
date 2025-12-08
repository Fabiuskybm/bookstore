

export function initScrollTop() {
    const button = document.querySelector('[data-scroll-top]');
    if (!button) return;

    const VISIBLE_CLASS = 'scroll-top--visible';
    const SCROLL_THRESHOLD = 300;

    const handleScroll = () => {
        if (window.scrollY > SCROLL_THRESHOLD) {
            button.classList.add(VISIBLE_CLASS);
        } else {
            button.classList.remove(VISIBLE_CLASS);
        }
    };

    const handleClick = () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };

    window.addEventListener('scroll', handleScroll);
    button.addEventListener('click', handleClick);

    handleScroll();
}
