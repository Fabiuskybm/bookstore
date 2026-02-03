

// ==================================================
//  WISHLIST ACTIONS (AJAX)
//  - Evita recarga en bulk remove y clear
//  - Refresca solo el contenido de la vista wishlist
// ==================================================

export function initWishlistActions() {

	document.addEventListener('submit', async (event) => {

		const form = event.target;
		if (!(form instanceof HTMLFormElement)) return;

		if (!form.classList.contains('wishlist__form')) return;

		const submitter = event.submitter;
		const action = submitter?.value ?? '';
		if (!action) return;

		event.preventDefault();

		const formData = new FormData(form);
		formData.set('action', action);

		try {
			const res = await fetch('index.php', {
				method: 'POST',
				body: formData,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'Accept': 'application/json',
				},
			});

			if (res.status === 401) {
				window.location.href = 'index.php?view=login';
				return;
			}

			const data = await res.json();
			if (!data?.ok) return;

			const htmlRes = await fetch('index.php?view=wishlist', {
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
				},
			});

			const html = await htmlRes.text();

			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');

			const newMain = doc.querySelector('.page__main');
			const main = document.querySelector('.page__main');

			if (newMain && main) {
				main.innerHTML = newMain.innerHTML;
			}

		} catch (error) {
			console.error('Wishlist AJAX error', error);
		}

	});
}
