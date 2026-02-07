

/**
 * Fetch robusto: si el servidor devuelve HTML (layout) en vez de JSON,
 * lo detectamos leyendo texto y parseando manualmente.
 */
export async function postForm(action, payload) {
	const form = new URLSearchParams();
	form.set('action', action);
	Object.entries(payload).forEach(([k, v]) => form.set(k, String(v)));

	const res = await fetch('/index.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
			Accept: 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
		},
		body: form.toString(),
	});

	const text = await res.text();

	if (text.trim().startsWith('<')) {
		throw Object.assign(new Error('html_instead_of_json'), { status: res.status });
	}

	try {
		return JSON.parse(text);
	} catch {
		throw Object.assign(new Error('invalid_json'), { status: res.status });
	}
}



export function fetchStats(productId) {
	return postForm('rating_stats', { product_id: productId });
}



export function sendVote(productId, value) {
	return postForm('rating_vote', { product_id: productId, value });
}
