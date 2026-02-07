
export function fmt(template, vars = {}) {
	const str = String(template ?? '');
	return str.replace(/\{(\w+)\}/g, (_, key) => (key in vars ? String(vars[key]) : `{${key}}`));
}


export function readRatingI18n(el) {
	if (!el) return {};

	return {
		title: el.dataset.i18nTitle,
		voteTitle: el.dataset.i18nVoteTitle,
		loading: el.dataset.i18nLoading,

		errorLoad: el.dataset.i18nErrorLoad,
		errorHtml: el.dataset.i18nErrorHtml,
		errorJson: el.dataset.i18nErrorJson,
		errorNetworkLoad: el.dataset.i18nErrorNetworkLoad,
		errorAuthRequired: el.dataset.i18nErrorAuthRequired,
		errorVote: el.dataset.i18nErrorVote,
		errorNetworkVote: el.dataset.i18nErrorNetworkVote,

		starsLabelAvg: el.dataset.i18nStarsLabelAvg,
		starsLabelVote: el.dataset.i18nStarsLabelVote,
		distAria: el.dataset.i18nDistAria,
		distStars: el.dataset.i18nDistStars,
		voteAria: el.dataset.i18nVoteAria,
	};
}
