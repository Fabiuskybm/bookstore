
// =============================================================
// |  AUTH ASYNC (AJAX)                                        |
// |  - Intercepta login/registro sin recargar la página       | 
// |  - Muestra errores dinámicamente (por campo y generales)  |
// |  - Mantiene compatibilidad con envío clásico (fallback)   |
// =============================================================


/**
 * Limpia los mensajes de error por campo.
 * Se ejecuta antes de cada envío para resetear el estado visual.
 */
function clearFieldErrors(form) {
	const fields = form.querySelectorAll('.auth__field');

	fields.forEach((field) => {
		const p = field.querySelector('.auth__error-message');
		if (p) p.textContent = '';
	});
}


/**
 * Muestra errores asociados a campos concretos.
 * Recibe un objeto con el nombre del campo y sus mensajes de error.
 */
function showFieldErrors(form, fieldErrors) {
	if (!fieldErrors) return;

	Object.entries(fieldErrors).forEach(([name, messages]) => {
		const input = form.querySelector(`[name="${CSS.escape(name)}"]`);
		if (!input) return;

		const field = input.closest('.auth__field');
		if (!field) return;

		const p = field.querySelector('.auth__error-message');
		if (!p) return;

		const text = Array.isArray(messages) ? messages.join(' ') : String(messages);
		p.textContent = text;
	});
}


/**
 * Muestra errores generales del formulario.
 * Acepta tanto un array de errores como un mensaje único.
 */
function showGeneralErrors(form, errorsOrMessage) {
	const errorsBox = form.querySelector('.auth__errors');
	if (!errorsBox) return;

	errorsBox.innerHTML = '';

	if (!errorsOrMessage) return;

	const errors = Array.isArray(errorsOrMessage)
		? errorsOrMessage
		: [String(errorsOrMessage)];

	errors.forEach((msg) => {
		const li = document.createElement('li');
		li.className = 'auth__errors-item';
		li.textContent = String(msg);
		errorsBox.appendChild(li);
	});
}


/**
 * Envía el formulario de login/registro mediante fetch (AJAX).
 * Devuelve la respuesta del backend en formato JSON.
 * Detecta respuestas HTML inesperadas (fallback de seguridad).
 */
async function postAuthForm(form) {
	const formData = new FormData(form);

	// Asegura que el campo action se envía correctamente
	if (!formData.get('action')) {
		const submit = form.querySelector('button[type="submit"][name="action"]');
		if (submit?.value) formData.set('action', submit.value);
	}

	const res = await fetch('index.php', {
		method: 'POST',
		body: formData,
		headers: {
			'X-Requested-With': 'XMLHttpRequest',
			Accept: 'application/json',
		},
	});

	const text = await res.text();

	// Si llega HTML en vez de JSON, se considera error
	if (text.trim().startsWith('<')) {
		throw new Error('html_instead_of_json');
	}

	return JSON.parse(text);
}


/**
 * Inicializa el comportamiento asíncrono del login y registro.
 * Intercepta el submit del formulario y gestiona todo el flujo AJAX.
 */
export function initAuthAsync() {
	document.addEventListener('submit', async (event) => {
		const form = event.target;
		if (!(form instanceof HTMLFormElement)) return;
		if (!form.classList.contains('auth__form')) return;

		// Solo se aplica a login y registro
		const submitter = event.submitter;
		const action = submitter?.value ?? form.querySelector('[name="action"]')?.value ?? '';
		if (action !== 'login' && action !== 'register') return;

		event.preventDefault();

		// Reset visual de errores
		clearFieldErrors(form);
		showGeneralErrors(form, null);

		try {
			const data = await postAuthForm(form);

			// Login correcto: navegación controlada por JS
			if (data?.ok && data?.redirect) {
				window.location.href = `index.php?view=${encodeURIComponent(data.redirect)}`;
				return;
			}

			// Mostrar errores
			showFieldErrors(form, data?.fieldErrors);

			if (Array.isArray(data?.errors) && data.errors.length) {
				showGeneralErrors(form, data.errors);
			} else if (data?.message) {
				showGeneralErrors(form, data.message);
			} else {
				showGeneralErrors(form, 'Ha ocurrido un error.');
			}

		} catch (error) {
			console.error('Auth AJAX error', error);
			showGeneralErrors(form, 'Error de red o respuesta inválida.');
		}
	});
}
