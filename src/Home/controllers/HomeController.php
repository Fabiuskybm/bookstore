<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Book/services/BookService.php';
require_once __DIR__ . '/../../Preference/services/PreferenceService.php';


class HomeController
{
    public function show(): array
    {
        // Aplicar preferencia de ítems por página
        $allBooks = books_get_all();
        $books = apply_items_per_page($allBooks);

        // Libros destacados
        $featured = books_get_featured();

        return [
            'view' => 'home',
            'data' => [
                'books' => $books,
                'featuredBooks' => $featured,
            ]
        ];
    }
}
