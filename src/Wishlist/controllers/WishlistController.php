<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/WishlistService.php';


class WishlistController
{

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