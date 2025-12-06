<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/WishlistService.php';
require_once __DIR__ . '/../../Preference/services/PreferenceService.php';


class WishlistController
{

    public function show(): array
    {
        $books = wishlist_get_books();
        $books = apply_items_per_page($books);

        $total = array_sum(array_map(
            fn($book) => $book->getPrice(),
            $books
        ));

        return [
            'view' => 'wishlist',
            'data' => [
                'wishlistBooks' => $books,
                'wishlistTotal' => $total
            ],
        ];
    }

    
    public function add(): array
    {
        return $this->handle('wishlist_add', true, 'home');
    }



    public function remove(): array
    {
        return $this->handle('wishlist_remove', true, 'home');
    }


    public function clear(): array 
    {
        return $this->handle('wishlist_clear', false, 'wishlist'); 
    }


    public function bulkRemove(): array
    {
        $selected = $_POST['selected_books'] ?? [];
        if (!is_array($selected)) $selected = [];

        wishlist_bulk_remove($selected);

        $return = $_POST['_return'] ?? 'wishlist';

        return [ 'redirect' => $return ];
    }


    

    private function handle(
        string $serviceFunction,
        bool $needsBookId,
        string $defaultReturn
    ): array
    {
        $bookId = null;
        if ($needsBookId) $bookId = $_POST['book_id'] ?? '';

        if ($needsBookId) {
            $serviceFunction($bookId);
        } else {
            $serviceFunction();
        }

        $return = $_POST['_return'] ?? $defaultReturn;

        return [ 'redirect' => $return ];
    }

}