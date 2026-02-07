
import { useState } from 'react';
import { fmt } from '../i18n.js';

export const STARS = [1, 2, 3, 4, 5];


function StarIcon({ className }) {
	return (
		<svg className={className} viewBox="0 0 24 24" aria-hidden="true" focusable="false">
			<path
				d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
				fill="currentColor"
			/>
		</svg>
	);
}


function StarFill({ percent }) {
	const safe = Math.max(0, Math.min(100, percent));

	return (
		<span className="rating__starWrap" aria-hidden="true">
			<StarIcon className="rating__starSvg rating__starEmpty" />
			<span className="rating__starFillClip" style={{ width: `${safe}%` }}>
				<StarIcon className="rating__starSvg rating__starFill" />
			</span>
		</span>
	);
}


export function StarsReadOnly({ value, i18n }) {
	const fallback = `Valoración media ${value} sobre 5`;
	const label = fmt(i18n?.starsLabelAvg || fallback, { value });

	return (
		<div className="rating__starsRow" aria-label={label}>
			{STARS.map((_, i) => {
				const x = value - i;
				const percent = Math.max(0, Math.min(1, x)) * 100;
				return <StarFill key={i} percent={percent} />;
			})}
		</div>
	);
}


export function StarsVote({ value, onVote, disabled, i18n }) {
	const [hover, setHover] = useState(0);
	const shown = hover > 0 ? hover : value;

	return (
		<div
			className="rating__starsRow rating__starsRow--interactive"
			onMouseLeave={() => setHover(0)}
			aria-label={i18n?.starsLabelVote || 'Votar con estrellas'}
		>
			{STARS.map((v) => (
				<button
					key={v}
					type="button"
					className="rating__starBtn"
					aria-label={fmt(i18n?.voteAria || `Votar ${v} estrellas`, { stars: v })}
					disabled={disabled}
					onMouseEnter={() => setHover(v)}
					onFocus={() => setHover(v)}
					onBlur={() => setHover(0)}
					onClick={() => onVote?.(v)}
				>
					<StarFill percent={v <= shown ? 100 : 0} />
				</button>
			))}
		</div>
	);
}
