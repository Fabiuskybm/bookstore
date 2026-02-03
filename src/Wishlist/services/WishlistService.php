<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Auth/services/AuthService.php';
require_once __DIR__ . '/../../Book/services/BookService.php';
require_once __DIR__ . '/../../Shared/Database.php';
require_once __DIR__ . '/../repositories/WishlistRepository.php';



/**
 * Devuelve los IDs de la wishlist del usuario actual.
 */
function wishlist_get_ids(): array 
{
    $userId = wishlist_user_id();
    if ($userId === null) return [];

    $repo = wishlist_repo();
    return $repo->getIdsByUserId($userId);
}


/**
 * Añade un libro a la wishlist del usuario actual.
 */
function wishlist_add(string $productId): void 
{
    $prep = wishlist_prepare($productId);
    if ($prep === null) return;

    $repo = wishlist_repo();
    $repo->add($prep['userId'], $prep['id']);
}


/**
 * Elimina un libro de la wishlist del usuario actual.
 */
function wishlist_remove(string $productId): void 
{
    $prep = wishlist_prepare($productId);
    if ($prep === null) return;

    $repo = wishlist_repo();
    $repo->remove($prep['userId'], $prep['id']);
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

    if (empty($toRemove)) return;

    $toRemove = array_values(array_unique($toRemove));
    $repo = wishlist_repo();
    $repo->bulkRemove($userId, $toRemove);
}



/**
 * Elimina por completo la wishlist del usuario actual.
 */
function wishlist_clear(): void
{
    $userId = wishlist_user_id();
    if ($userId === null) return;

    $repo = wishlist_repo();
    $repo->clear($userId);
}


/**
 * Devuelve los libros completos (Book[]) de la wishlist del usuario actual.
 */
function wishlist_get_books(): array
{
    $ids = wishlist_get_ids();
    if (empty($ids)) return [];

    $books = [];

    foreach ($ids as $id) {
        $book = books_find_by_id($id);
        if ($book !== null) $books[] = $book;
    }

    return $books;
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

    $repo = wishlist_repo();
    return $repo->has($userId, $cleanId);
}




/*----------------
|     HELPERS     
|----------------*/

function wishlist_user_id(): ?int
{
    $user = auth_user();
    if ($user === null) return null;

    $userId = $user->getId();
    return $userId > 0 ? $userId : null;
}


function wishlist_repo(): WishlistRepository
{
    $pdo = Database::getInstance()->pdo();
    return new WishlistRepository($pdo);
}


/**
 * Prepara los datos necesarios para modificar la wishlist
 * - Normaliza el productId
 * - Obtiene el userId actual
 *
 * Devuelve:
 *  [
 *    'id' => int,
 *    'userId' => int,
 *  ]
 * 
 * Si algo falla -> null
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
