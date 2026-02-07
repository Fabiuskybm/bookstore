
import { fmt } from '../i18n.js';

export function Distribution({ distribution, total, i18n }) {
	const rows = [5, 4, 3, 2, 1].map((stars) => {
		const count = Number(distribution?.[String(stars)] ?? distribution?.[stars] ?? 0);
		const percent = total > 0 ? (count / total) * 100 : 0;
		return { stars, count, percent };
	});

	return (
		<div className="rating__dist" aria-label={i18n?.distAria || 'Distribución de valoraciones'}>
			{rows.map((r) => (
				<div className="rating__distRow" key={r.stars}>
					<div className="rating__distLabel">
						{fmt(i18n?.distStars || `${r.stars} estrellas`, { stars: r.stars })}
					</div>

					<div className="rating__distBar" aria-hidden="true">
						<div className="rating__distFill" style={{ width: `${r.percent}%` }} />
					</div>

					<div className="rating__distRight">
						<span className="rating__distCount">{r.count}</span>
					</div>
				</div>
			))}
		</div>
	);
}
