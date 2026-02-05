
import { initPacksSelects } from './packs-select.js';



const updatePacksView = async (responseText) => {
    const parser = new DOMParser();
    const doc = parser.parseFromString(responseText, 'text/html');
    const nextMain = doc.querySelector('.page__main');
    const currentMain = document.querySelector('.page__main');

    if (!nextMain || !currentMain) {
        return;
    }

    currentMain.innerHTML = nextMain.innerHTML;
    initPacksAsync();
    initPacksSelects();
};


export const initPacksAsync = () => {
    const packsSection = document.querySelector('.packs');

    if (!packsSection) { return; }

    const forms = packsSection.querySelectorAll('form');

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            const submitter = event.submitter;
            const formData = submitter ? new FormData(form, submitter) : new FormData(form);
            let action = formData.get('action');

            if (!action && submitter && submitter.name === 'action') {
                action = submitter.value;
            }

            if (!action) {
                action = form.querySelector('input[name=\"action\"]')?.value ?? null;
            }

            if (!['pack_add', 'pack_item_add', 'pack_remove', 'pack_clear'].includes(action)) {
                return;
            }

            event.preventDefault();

            const actionAttr = form.getAttribute('action');
            const url = actionAttr ? new URL(actionAttr, window.location.href).toString() : window.location.href;
            
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                console.error('Pack async request failed:', response.status);
                return;
            }

            const text = await response.text();
            await updatePacksView(text);
        });
    });
};
