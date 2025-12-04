<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Auth/services/AuthService.php';
require_once __DIR__ . '/../../Book/services/BookService.php';
require_once __DIR__ . '/../../Shared/CookieService.php';


const WISHLIST_LIFETIME = 60 * 60 * 24 * 30;



/**
 * Devuelve el nombre de la cookie de wishlist
 * para el usuario actual o null si no hay usuario.
 */
function wishlist_cookie_name(): ?string 
{
    $user = auth_user();
    if($user === null) return null;

    $username = trim(mb_strtolower($user->getUsername()));
    if ($username === '') return null;

    return 'wishlist_' . $username;
}


/**
 * Devuelve los IDs de la wishlist del usuario actual.
 */
function wishlist_get_ids(): array 
{
    $cookieName = wishlist_cookie_name();
    if ($cookieName === null) return [];

    $raw = get_cookie_value($cookieName);
    if ($raw === null || $raw === '') return [];

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) return [];


    $ids = [];

    foreach($decoded as $value) {
        if (is_string($value)) $ids[] = $value;
    }

    $ids = array_values(array_unique($ids));

    return $ids;
}


/**
 * Guarda los IDs de la wishlist del usuario actual.
 */
function wishlist_save_ids(array $ids): void
{
    $cookieName = wishlist_cookie_name();
    if ($cookieName === null) return;

    $clean = [];

    foreach ($ids as $value) {
        if (is_string($value) && trim($value) !== '') {
            $clean[] = trim($value);
        }
    }

    $clean = array_values(array_unique($clean));
    
    if (empty($clean)) {
        delete_cookie($cookieName);
        return;
    }

    $json = json_encode($clean, JSON_UNESCAPED_UNICODE);

    if ($json !== false) {
        set_cookie_value($cookieName, $json, WISHLIST_LIFETIME);
    }
}


/**
 * Añade un libro a la wishlist del usuario actual.
 */
function wishlist_add(string $bookId): void 
{
    $prep = wishlist_prepare($bookId);
    if ($prep === null) return;

    $cleanId = $prep['id'];
    $ids = $prep['ids'];

    if (!in_array($cleanId, $ids, true)) {
        $ids[] = $cleanId;
        wishlist_save_ids($ids);
    }
}


/**
 * Elimina un libro de la wishlist del usuario actual.
 */
function wishlist_remove(string $bookId): void 
{
    $prep = wishlist_prepare($bookId);
    if ($prep === null) return;

    $cleanId = $prep['id'];
    $ids = $prep['ids'];

    $filtered = array_values(array_filter(
        $ids,
        static fn (string $id): bool => $id !== $cleanId
    ));

    wishlist_save_ids($filtered);
}


/**
 * Elimina varios libros de la wishlist del usuario actual
 */
function wishlist_bulk_remove(array $bookIds): void
{
    if (empty($bookIds)) return;

    $ctx = wishlist_context();
    if ($ctx === null) return;

    $toRemove = [];

    foreach ($bookIds as $id) {
        if (!is_string($id)) continue;
        $cleanId = trim($id);
        if ($cleanId !== '') $toRemove[] = $cleanId;
    }

    if (empty($toRemove)) return;

    $toRemove = array_unique($toRemove);

    $remaining = array_values(array_filter(
        $ctx['ids'],
        static fn (string $id): bool => !in_array($id, $toRemove, true)
    ));

    wishlist_save_ids($remaining);
}



/**
 * Elimina por completo la wishlist del usuario actual.
 */
function wishlist_clear(): void
{
    $ctx = wishlist_context();
    if ($ctx === null) return;

    wishlist_save_ids([]);
}


/**
 * Devuelve los libros completos (Book[]) de la wishlist del usuario actual.
 */
function wishlist_get_books(): array
{
    $ctx = wishlist_context();
    if ($ctx === null) return [];

    $books = [];

    foreach ($ctx['ids'] as $id) {
        $book = books_find_by_id($id);
        if ($book !== null) $books[] = $book;
    }

    return $books;
}


/**
 * Indica si un libro está en la wishlist del usuario actual
 */
function wishlist_has(string $bookId): bool
{
    $cleanId = trim($bookId);
    if ($cleanId === '') return false;

    $ctx = wishlist_context();
    if ($ctx === null) return false;

    return in_array($cleanId, $ctx['ids'], true);
}




/*----------------
|     HELPERS     
|----------------*/

/**
 * Devuelve el contexto de wishlist del usuario actual
 * (cookieName e ids actuales) o null si no hay usuario.
 */
function wishlist_context(): ?array 
{
    $cookieName = wishlist_cookie_name();
    if ($cookieName === null) return null;

    $ids = wishlist_get_ids();

    return [
        'cookieName' => $cookieName,
        'ids' => $ids
    ];
}


/**
 * Prepara los datos necesarios para modificar la wishlist
 * - Normaliza el bookId
 * - Obtiene el contexto del usuario (cookieName + ids)
 * 
 * Devuelve:
 *  [
 *    'id' => string,
 *    'ids' => array,
 *    'cookieName' => string,
 *  ]
 * 
 * Si algo falla -> null
 */
function wishlist_prepare(string $bookId): ?array
{
    $cleanId = trim($bookId);
    if ($cleanId === '') return null;

    $ctx= wishlist_context();
    if ($ctx === null) return null;

    return [
        'id' => $cleanId,
        'ids' => $ctx['ids'],
        'cookieName' => $ctx['cookieName']
    ];
}