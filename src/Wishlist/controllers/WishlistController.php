<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/WishlistService.php';
require_once __DIR__ . '/../../Preference/services/PreferenceService.php';


final class WishlistController
{
	// ==========================
	// |        VISTAS (GET)     |
	// ==========================

	public function show(): array
	{
		$allBooks = wishlist_get_books();

		$total = array_sum(array_map(
			static fn($book) => $book->getPrice(),
			$allBooks
		));

		$books = apply_items_per_page($allBooks);

		return [
			'view' => 'wishlist',
			'data' => [
				'wishlistBooks' => $books,
				'wishlistTotal' => $total,
			],
		];
	}


	// ==========================
	// |     ACCIONES (POST)     |
	// ==========================

	public function add(): array
	{
		return $this->handleRedirectOnly('wishlist_add', true, 'home');
	}

	public function remove(): array
	{
		return $this->handleRedirectOnly('wishlist_remove', true, 'home');
	}

	/**
	 * Limpia toda la wishlist del usuario.
	 * - AJAX: devuelve JSON
	 * - No AJAX: redirect a wishlist
	 */
	public function clear(): array
	{
		$user = auth_user();

		if ($user === null) {
			return $this->unauthorizedResponse();
		}

		wishlist_clear();

		return $this->isAjax()
			? ['ok' => true]
			: ['redirect' => 'wishlist'];
	}

	/**
	 * Elimina seleccionados (bulk remove).
	 * - AJAX: devuelve JSON
	 * - No AJAX: redirect a la vista indicada en _return
	 */
	public function bulkRemove(): array
	{
		$user = auth_user();

		if ($user === null) {
			return $this->unauthorizedResponse();
		}

		$selected = $_POST['selected_books'] ?? [];
		if (!is_array($selected)) $selected = [];

		wishlist_bulk_remove($selected);

		if ($this->isAjax()) {
			return ['ok' => true];
		}

		$return = $_POST['_return'] ?? 'wishlist';
		return ['redirect' => $return];
	}

	/**
	 * Toggle wishlist desde cards (corazón).
	 * - AJAX esperado (fetch)
	 */
	public function toggle(): array
	{
		$user = auth_user();

		if ($user === null) {
			return $this->unauthorizedResponse();
		}

		$productId = $_POST['product_id'] ?? '';
		if ($productId === '') {
			http_response_code(400);
			return ['ok' => false, 'error' => 'missing_product_id'];
		}

		$inWishlist = wishlist_has($productId);

		if ($inWishlist) {
			wishlist_remove($productId);
			$inWishlist = false;
		} else {
			wishlist_add($productId);
			$inWishlist = true;
		}

		return [
			'ok' => true,
			'inWishlist' => $inWishlist,
		];
	}


	// ==========================
	// |        HELPERS          |
	// ==========================

	/**
	 * Para acciones que, de momento, seguimos resolviendo con redirects
	 * (por ejemplo add/remove no-AJAX).
	 */
	private function handleRedirectOnly(
		string $serviceFunction,
		bool $needsProductId,
		string $defaultReturn
	): array
	{
		$user = auth_user();

		if ($user === null) {
			return ['redirect' => 'login'];
		}

		$productId = '';

		if ($needsProductId) {
			$productId = $_POST['product_id'] ?? '';
			$serviceFunction($productId);
		} else {
			$serviceFunction();
		}

		$return = $_POST['_return'] ?? $defaultReturn;

		return ['redirect' => $return];
	}

	/**
	 * Detecta si la petición es AJAX (fetch).
	 */
	private function isAjax(): bool
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
			&& $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}

	/**
	 * Respuesta estándar cuando no hay sesión:
	 * - AJAX: 401 + JSON
	 * - No AJAX: redirect login
	 */
	private function unauthorizedResponse(): array
	{
		if ($this->isAjax()) {
			http_response_code(401);
			return ['ok' => false, 'error' => 'auth_required'];
		}

		return ['redirect' => 'login'];
	}
}
