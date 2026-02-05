export const initPacksSelects = () => {
    const wrappers = document.querySelectorAll('.packs__select');
    if (wrappers.length === 0) {
        return;
    }

    wrappers.forEach((wrapper) => {
        const select = wrapper.querySelector('select');
        if (!select) {
            return;
        }

        const toggleOpen = () => {
            if (select.disabled) {
                return;
            }
            wrapper.classList.toggle('is-open');
        };

        select.addEventListener('click', toggleOpen);
        select.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                toggleOpen();
            }
        });
        select.addEventListener('blur', () => wrapper.classList.remove('is-open'));
        select.addEventListener('change', () => wrapper.classList.remove('is-open'));
    });
};
