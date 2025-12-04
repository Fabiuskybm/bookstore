

document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('.wishlist__form');
    if (!form) return;

    const selectAllCheckbox = form.querySelector('.wishlist__select-all-input');
    const intemCheckboxes = form.querySelectorAll('.book-card__select-input');
    const errorBox = form.querySelector('[data-wishlist-error]');

    if (!selectAllCheckbox || intemCheckboxes.length === 0) return;

    const clearError = () => {
        if (errorBox) errorBox.textContent = '';
    }


    // Marcar / desmarcar al pulsar "Seleccionar todo"
    selectAllCheckbox.addEventListener('change', () => {
        const checked = selectAllCheckbox.checked;

        intemCheckboxes.forEach((checkbox) => {
            checkbox.checked = checked;
        });

        if (checked) clearError();
    });


    intemCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const allChecked = Array.from(intemCheckboxes)
                .every((item) => item.cheched);
            
            selectAllCheckbox.checked = allChecked;

            const anyChecked = Array.from(intemCheckboxes)
                .some((item) => item.checked);
            
            if (anyChecked) clearError();
        });
    });


    form.addEventListener('submit', (event) => {

        const anyChecked = Array.from(intemCheckboxes)
                .some((item) => item.checked);
            
        if (!anyChecked) {
            event.preventDefault();

            if (errorBox) {
                errorBox.textContent = 'Debes seleccionar al menos un libro.';

                setTimeout(() => {
                    errorBox.textContent = '';
                }, 3000);
                
            } else {
                alert('Debes seleccionar al menos un libro.');
            }
        }

    });

});