<?php
declare(strict_types=1);

// ===============================
// |        Book Service         |
// |  Carga y acceso al catálogo |
// ===============================

require_once __DIR__ . '/../../Shared/Database.php';
require_once __DIR__ . '/../repositories/BookRepository.php';
require_once __DIR__ . '/../../Preference/services/PreferenceService.php';
require_once __DIR__ . '/../../Shared/config.php';
require_once __DIR__ . '/../models/Book.php';



// ---------------------------
//  API pública del servicio
// ---------------------------

function books_get_all_for_lang(string $lang): array
{
    static $cache = [];

    if (isset($cache[$lang])) return $cache[$lang];

    $pdo = Database::getInstance()->pdo();
    $repo = new BookRepository($pdo);

    return $cache[$lang] = $repo->findAllByLang($lang);
}



// Devuelve todos los libros del catálogo.
function books_get_all(): array
{
    $lang = pref_language();
    return books_get_all_for_lang($lang);
}


// Devuelve solo los libros destacados (isFeatured = true).
function books_get_featured(): array
{
    $lang = pref_language();

    $pdo = Database::getInstance()->pdo();
    $repo = new BookRepository($pdo);

    return $repo->findFeaturedByLang($lang);
}


// Busca un libro por su ID.
function books_find_by_id(int $id): ?Book
{
    if ($id <= 0) return null;

    foreach (books_get_all() as $book) {
        if ($book->getId() === $id) return $book;
    }

    return null;
}


function books_find_by_id_in_lang(int $id, string $lang): ?Book
{
    if ($id <= 0) return null;

    foreach (books_get_all_for_lang($lang) as $book) {
        if ($book->getId() === $id) return $book;
    }

    return null;
}
