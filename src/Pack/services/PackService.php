<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Shared/SessionService.php';
require_once __DIR__ . '/../../Shared/validation.php';
require_once __DIR__ . '/../../Book/services/BookService.php';
require_once __DIR__ . '/../models/Pack.php';



final class PackService
{
    private const SESSION_KEY = 'ut5_packs';


    /*
     * Devuelve el catálogo completo de libros.
     */
    public function getBooks(): array
    {
        return books_get_all();
    }



    /*
     * Extrae categorías únicas a partir del catálogo.
     */
    public function getAvailableCategories(array $books): array
    {
        $categories = [];
        foreach ($books as $book) {
            foreach ($book->getCategories() as $category) {
                $categories[$category] = true;
            }
        }

        return array_keys($categories);
    }



    /*
     * Devuelve los packs serializados en formato array para la vista.
     */
    public function getPacks(): array
    {
        return array_map(static function (Pack $pack): array {
            $data = $pack->toArray();
            $data['total'] = $pack->getTotal();
            return $data;
        }, $this->loadPacks());
    }



    /*
     * Valida datos de pack y lo añade a sesión.
     */
    public function addPack(array $books): array
    {
        $errors = [];

        $categories = $this->getAvailableCategories($books);

        if ($categories === []) {
            $errors['category'] = 'packs.errors.categories_unavailable';
        }

        $nameResult = validarTexto('name', 2, 60, true);

        if (!$nameResult['ok']) {
            $errors['name'] = 'packs.errors.pack_name';
        }

        $categoryResult = validarSelect('category', $categories, true);

        if (!$categoryResult['ok']) {
            $errors['category'] = 'packs.errors.pack_category';
        }

        if ($errors !== []) {
            return ['errors' => $errors];
        }

        $packs = $this->loadPacks();

        $packs[] = new Pack(
            0,
            (string) $nameResult['valor'],
            '',
            0.0,
            0,
            '',
            (string) $categoryResult['valor']
        );

        $this->savePacks($packs);

        return ['packs' => $this->serializePacks($packs)];
    }



    /*
     * Valida y añade un libro a un pack existente.
     */
    public function addItem(): array
    {
        $errors = [];

        $packs = $this->loadPacks();

        if ($packs === []) {
            $errors['pack_index'] = 'packs.errors.pack_invalid';
        }

        $maxIndex = count($packs) > 0 ? count($packs) - 1 : 0;
        $packIndexResult = validarNumero('pack_index', 'int', true, 0, $maxIndex);

        if (!$packIndexResult['ok']) {
            $errors['pack_index'] = 'packs.errors.pack_invalid';
        }

        $packIndex = (int) ($packIndexResult['valor'] ?? -1);
        $pack = $packs[$packIndex] ?? null;
        $packCategory = $pack?->getCategory() ?? '';
        $allowedBooks = $this->buildAllowedBookIdsForCategory($packCategory);

        $bookResult = validarSelect('book_id', $allowedBooks, true);

        if (!$bookResult['ok']) {
            $errors['book_id'] = 'packs.errors.book_invalid';
        }

        $quantityResult = validarNumero('quantity', 'int', true, 1);

        if (!$quantityResult['ok']) {
            $errors['quantity'] = 'packs.errors.quantity_min';
        }

        if ($errors !== []) {
            return ['errors' => $errors];
        }

        $bookId = (int) $bookResult['valor'];
        $book = $this->resolveBook($bookId);

        if ($book === null) {
            return ['errors' => ['book_id' => 'packs.errors.book_invalid']];
        }

        if ($packCategory === '' || !$book->hasCategory($packCategory)) {
            return ['errors' => ['book_id' => 'packs.errors.book_category']];
        }

        $quantity = (int) $quantityResult['valor'];
        $currentQty = $this->getExistingQuantity($pack, $book->getId());
        $newQty = $currentQty + $quantity;

        if ($newQty > $book->getStock()) {
            return ['errors' => ['quantity' => 'packs.errors.quantity_stock']];
        }

        $item = new PackItem($book, $quantity);
        $pack->addItem($item);
        $packs[$packIndex] = $pack;

        $this->savePacks($packs);

        return ['packs' => $this->serializePacks($packs)];
    }



    /*
     * Vacía todos los packs en sesión.
     */
    public function clearPacks(): void
    {
        session_set(self::SESSION_KEY, []);
    }



    /*
     * Elimina un pack por índice y guarda la sesión.
     */
    public function removePack(int $index): void
    {
        $packs = $this->loadPacks();

        if (!isset($packs[$index])) {
            return;
        }

        array_splice($packs, $index, 1);
        $this->savePacks($packs);
    }



    /*
     * Calcula total general y total por categoría.
     */
    public function calculateTotals(array $packs): array
    {
        $grandTotal = 0.0;
        $byCategory = [];

        foreach ($packs as $pack) {
            $total = (float) ($pack['total'] ?? 0.0);
            $category = (string) ($pack['category'] ?? '');

            $grandTotal += $total;

            if ($category !== '') {
                $byCategory[$category] = ($byCategory[$category] ?? 0) + $total;
            }
        }

        return [
            'grand_total' => $grandTotal,
            'by_category' => $byCategory,
        ];
    }


    /*
     * Rehidrata packs desde sesión a objetos de dominio.
     */
    private function loadPacks(): array
    {
        $raw = session_get(self::SESSION_KEY, []);

        if (!is_array($raw)) {
            return [];
        }

        $packs = [];

        foreach ($raw as $data) {

            if (!is_array($data)) {
                continue;
            }

            $pack = Pack::fromArray($data, fn(int $id) => $this->resolveBook($id));
            $category = $pack->getCategory();

            $sanitized = new Pack(
                0,
                $pack->getName(),
                '',
                0.0,
                0,
                '',
                $category
            );

            /*
             * Ajusta cantidad a stock y descarta si no hay stock.
             */
            foreach ($pack->getItems() as $item) {
                $book = $item->getProduct();
                if (!$book->hasCategory($category)) {
                    continue;
                }

                $stock = $book->getStock();
                if ($stock <= 0) {
                    continue;
                }

                $quantity = $item->getQuantity();
                if ($quantity > $stock) {
                    $quantity = $stock;
                }

                $sanitized->addItem(new PackItem($book, $quantity, $item->getId()));
            }

            $packs[] = $sanitized;
        }

        return $packs;
    }



    /*
     * Guarda packs serializados en sesión.
     */
    private function savePacks(array $packs): void
    {
        session_set(self::SESSION_KEY, $this->serializePacks($packs));
    }



    /*
     * Convierte packs de dominio a arrays para la vista/sesión.
     */
    private function serializePacks(array $packs): array
    {
        return array_map(
            static fn(Pack $pack) => $pack->toArray(),
            $packs
        );
    }



    /*
     * Busca un libro del catálogo por ID.
     */
    private function resolveBook(int $id): ?Book
    {
        return books_find_by_id($id);
    }



    /*
     * Devuelve IDs de libros que pertenezcan a la categoría dada.
     */
    private function buildAllowedBookIdsForCategory(string $category): array
    {
        if ($category === '') {
            return [];
        }

        $allowed = [];
        foreach ($this->getBooks() as $book) {
            if ($book->hasCategory($category)) {
                $allowed[] = (string) $book->getId();
            }
        }

        return $allowed;
    }


    /*
     * Devuelve la cantidad actual de un libro dentro del pack.
     */
    private function getExistingQuantity(Pack $pack, int $bookId): int
    {
        foreach ($pack->getItems() as $item) {
            if ($item->getProduct()->getId() === $bookId) {
                return $item->getQuantity();
            }
        }

        return 0;
    }

}
