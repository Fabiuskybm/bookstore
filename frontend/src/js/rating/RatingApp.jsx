
import { useEffect, useMemo, useState } from 'react';
import { fetchStats, sendVote } from './api.js';
import { Distribution } from './components/Distribution.jsx';
import { StarsReadOnly, StarsVote } from './components/Stars.jsx';


function mapErrorMessage(e, i18n, kind) {

	if (e?.message === 'html_instead_of_json') {
		return i18n?.errorHtml || 'Error: el servidor devolvió HTML en vez de JSON.';
	}

	if (e?.message === 'invalid_json') {
		return i18n?.errorJson || 'Error: respuesta inválida (no es JSON).';
	}

	if (kind === 'vote') {
		return i18n?.errorNetworkVote || 'Error de red al enviar tu voto.';
	}

	return i18n?.errorNetworkLoad || 'Error de red al cargar valoraciones.';
}


export function RatingApp({ productId, i18n }) {

	const [stats, setStats] = useState(null);
	const [error, setError] = useState('');
	const [myVote, setMyVote] = useState(0);
	const [isVoting, setIsVoting] = useState(false);
	const [canVote, setCanVote] = useState(false);


	useEffect(() => {
		let alive = true;

		fetchStats(productId)
			.then((data) => {
				if (!alive) return;

				if (!data?.ok) {
					setError(i18n?.errorLoad || 'No se pudieron cargar las valoraciones.');
					return;
				}

				setStats(data.stats);
				setCanVote(Boolean(data.canVote));

				if (typeof data.userVote !== 'undefined') {
					setMyVote(Number(data.userVote) || 0);
				} else {
					setMyVote(0);
				}
			})
			.catch((e) => {
				if (!alive) return;
				setError(mapErrorMessage(e, i18n, 'load'));
			});

		return () => {
			alive = false;
		};

	}, [productId, i18n]);


	const avgRounded = useMemo(() => Number(stats?.averageRounded ?? 0), [stats]);
	const count = useMemo(() => Number(stats?.count ?? 0), [stats]);

	
	async function handleVote(v) {
		try {
			setIsVoting(true);
			setError('');

			const data = await sendVote(productId, v);

			if (!data?.ok) {
				if (data?.error === 'auth_required') {
					// Si el backend responde 401, ocultamos el bloque de votar
					setCanVote(false);
					setError(i18n?.errorAuthRequired || 'Inicia sesión para poder votar.');
					return;
				}
				setError(i18n?.errorVote || 'No se pudo registrar tu voto.');
				return;
			}

			if (data?.stats) setStats(data.stats);

			if (typeof data.userVote !== 'undefined') {
				setMyVote(Number(data.userVote) || 0);
			} else {
				setMyVote(v);
			}

		} catch (e) {
			setError(mapErrorMessage(e, i18n, 'vote'));
			
		} finally {
			setIsVoting(false);
		}
	}

	if (error) return <p className="rating__error">{error}</p>;
	if (!stats) return <p className="rating__loading">{i18n?.loading || 'Cargando valoraciones...'}</p>;

	return (
		<div className="rating">
			<div className="rating__title">{i18n?.title || 'Valoraciones de los clientes'}</div>

			<div className="rating__summaryRow">
				<StarsReadOnly value={avgRounded} i18n={i18n} />
				<span className="rating__avg">{avgRounded}</span>
				<span className="rating__count">({count})</span>
			</div>

			<Distribution distribution={stats.distribution} total={count} i18n={i18n} />

			{canVote ? (
				<div className="rating__vote">
					<div className="rating__voteTitle">{i18n?.voteTitle || 'Valorar producto'}</div>
					<StarsVote value={myVote} onVote={handleVote} disabled={isVoting} i18n={i18n} />
				</div>
			) : null}
		</div>
	);
}
