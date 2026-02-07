<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Auth/services/AuthService.php';
require_once __DIR__ . '/../../Shared/Database.php';
require_once __DIR__ . '/../repositories/WishlistRepository.php';

// Para poder cargar Books por IDs sin filtrar por idioma
require_once __DIR__ . '/../../Book/repositories/BookRepository.php';



/**
 * Devuelve los IDs de la wishlist del usuario actual.
 */
function wishlist_get_ids(): array
{
    $userId = wishlist_user_id();
    if ($userId === null) return [];

    return wishlist_repo()->getIdsByUserId($userId);
}


/**
 * Añade un libro a la wishlist del usuario actual.
 */
function wishlist_add(string $productId): void
{
    $prep = wishlist_prepare($productId);
    if ($prep === null) return;

    wishlist_repo()->add($prep['userId'], $prep['id']);
}


/**
 * Elimina un libro de la wishlist del usuario actual.
 */
function wishlist_remove(string $productId): void
{
    $prep = wishlist_prepare($productId);
    if ($prep === null) return;

    wishlist_repo()->remove($prep['userId'], $prep['id']);
}


/**
 * Elimina varios libros de la wishlist del usuario actual
 */
function wishlist_bulk_remove(array $productIds): void
{
    if (empty($productIds)) return;

    $userId = wishlist_user_id();
    if ($userId === null) return;

    $toRemove = [];

    foreach ($productIds as $id) {
        $cleanId = wishlist_normalize_product_id($id);
        if ($cleanId !== null) $toRemove[] = $cleanId;
    }

    $toRemove = array_values(array_unique($toRemove));
    if (empty($toRemove)) return;

    wishlist_repo()->bulkRemove($userId, $toRemove);
}


/**
 * Elimina por completo la wishlist del usuario actual.
 */
function wishlist_clear(): void
{
    $userId = wishlist_user_id();
    if ($userId === null) return;

    wishlist_repo()->clear($userId);
}


/**
 * Devuelve los libros completos (Book[]) de la wishlist del usuario actual.
 */
function wishlist_get_books(): array
{
    $ids = wishlist_get_ids();
    if (empty($ids)) return [];

    return wishlist_book_repo()->findByProductIds($ids);
}


/**
 * Indica si un libro está en la wishlist del usuario actual
 */
function wishlist_has(int|string $productId): bool
{
    $cleanId = wishlist_normalize_product_id($productId);
    if ($cleanId === null) return false;

    $userId = wishlist_user_id();
    if ($userId === null) return false;

    return wishlist_repo()->has($userId, $cleanId);
}



// ===================
// |     HELPERS     |
// ===================

function wishlist_user_id(): ?int
{
    $user = auth_user();
    if ($user === null) return null;

    $userId = $user->getId();
    return $userId > 0 ? $userId : null;
}

function wishlist_pdo(): PDO
{
    return Database::getInstance()->pdo();
}

function wishlist_repo(): WishlistRepository
{
    return new WishlistRepository(wishlist_pdo());
}

/**
 * Repo de libros para cargar por IDs sin depender del idioma.
 */
function wishlist_book_repo(): BookRepository
{
    return new BookRepository(wishlist_pdo());
}


/**
 * Prepara los datos necesarios para modificar la wishlist.
 */
function wishlist_prepare(string $productId): ?array
{
    $cleanId = wishlist_normalize_product_id($productId);
    if ($cleanId === null) return null;

    $userId = wishlist_user_id();
    if ($userId === null) return null;

    return [
        'id' => $cleanId,
        'userId' => $userId
    ];
}

function wishlist_normalize_product_id(int|string $productId): ?int
{
    $cleanId = trim((string) $productId);
    if ($cleanId === '') return null;

    $cleanId = (int) $cleanId;
    return $cleanId > 0 ? $cleanId : null;
}
