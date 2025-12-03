<?php
declare(strict_types=1);

// ===============================
// |        Book Service         |
// |  Carga y acceso al catálogo |
// ===============================

require_once __DIR__ . '/../../Shared/config.php';
require_once __DIR__ . '/../models/Book.php';



// ---------------------------
//  API pública del servicio
// ---------------------------

// Devuelve todos los libros del catálogo.
function books_get_all(): array
{
    static $cache = null;
    if ($cache !== null) return $cache;

    // Si no existe el fichero, devolvemos array vacío
    if (!file_exists(BOOKS_FILE)) return $cache = [];

    $json = file_get_contents(BOOKS_FILE);
    $data = json_decode($json, true);


    // Si el JSON es inválido o no es un array, devolvemos vacío
    if (json_last_error() !== JSON_ERROR_NONE || 
        !is_array($data)
    ) {
        return $cache = [];
    }

    $books = [];

    foreach ($data as $item) {
        $book = books_create_from_array($item);
        if ($book !== null) $books[] = $book;
    }

    return $cache = $books;
}


// Devuelve solo los libros destacados (isFeatured = true).
function books_get_featured(): array {
    $allBooks = books_get_all();

    return array_filter(
        $allBooks,
        static fn (Book $book): bool => $book->isFeatured()
    );
}


// Busca un libro por su ID.
function books_find_by_id(string $id): ?Book {

    $id = trim($id);
    if ($id === '') return null;

    foreach (books_get_all() as $book) 
    {
        if ($book->getId() === $id) return $book;
    }

    return null;
}




/*----------------
|     HELPERS     
|----------------*/

// Crea un objeto Book a partir de un array asociativo.
function books_create_from_array(array $row): ?Book
{
    if (!isset(
        $row['id'],
        $row['title'],
        $row['author'],
        $row['price'],
        $row['coverImage'],
        $row['categories'],
        $row['format'],
        $row['isFeatured']

    )) { 
        return null; 
    }


    $id = (string) $row['id'];
    $title = (string) $row['title'];
    $author = (string) $row['author'];
    $price = (float) $row['price'];
    $coverImage = (string) $row['coverImage'];
    $categories = is_array($row['categories']) ? $row['categories'] : [];
    $format = (string) $row['format'];
    $isFeatured = (bool) $row['isFeatured'];


    return new Book(
        $id,
        $title,
        $author,
        $price,
        $coverImage,
        $categories,
        $format,
        $isFeatured
    );
}




