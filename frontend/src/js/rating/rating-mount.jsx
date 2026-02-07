
import { createRoot } from 'react-dom/client';
import { readRatingI18n } from './i18n.js';
import { RatingApp } from './RatingApp.jsx';


export function initRatingIsland() {
	const el = document.getElementById('rating-root');
	if (!el) return;

	const productId = Number(el.dataset.productId || 0);
	if (!productId) return;

	const i18n = readRatingI18n(el);

	const root = createRoot(el);
	root.render(<RatingApp productId={productId} i18n={i18n} />);
}
