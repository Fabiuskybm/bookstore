<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/PackService.php';


final class PackController
{
    public function __construct(
        private PackService $service = new PackService()
    ) {}


    /*
     * Construye la vista de packs con todos los datos necesarios.
     */
    public function show(): array
    {
        return [
            'view' => 'packs',
            'data' => $this->buildViewData(),
        ];
    }


    /*
     * Crea un pack y devuelve la vista con errores o datos actualizados.
     */
    public function add(): array
    {
        $books = $this->service->getBooks();
        $result = $this->service->addPack($books);

        if (isset($result['errors'])) {
            return [
                'view' => 'packs',
                'data' => $this->buildViewData([
                    'errors' => $result['errors'],
                    'form' => [
                        'name' => $_POST['name'] ?? '',
                        'category' => $_POST['category'] ?? '',
                    ],
                ]),
            ];
        }

        return [
            'view' => 'packs',
            'data' => $this->buildViewData(),
        ];
    }


    /*
     * Añade un libro a un pack y devuelve la vista actualizada.
     */
    public function addItem(): array
    {
        $result = $this->service->addItem();

        if (isset($result['errors'])) {
            return [
                'view' => 'packs',
                'data' => $this->buildViewData([
                    'errors' => $result['errors'],
                    'form' => [
                        'pack_index' => $_POST['pack_index'] ?? '',
                        'book_id' => $_POST['book_id'] ?? '',
                        'quantity' => $_POST['quantity'] ?? '',
                    ],
                ]),
            ];
        }

        return [
            'view' => 'packs',
            'data' => $this->buildViewData(),
        ];
    }


    /*
     * Vacía todos los packs y devuelve la vista.
    */
    public function clear(): array
    {
        $this->service->clearPacks();
        return [
            'view' => 'packs',
            'data' => $this->buildViewData(),
        ];
    }



    /*
     * Elimina un pack por índice y devuelve la vista actualizada.
    */
    public function remove(): array
    {
        $index = (int) ($_POST['index'] ?? -1);
        if ($index >= 0) {
            $this->service->removePack($index);
        }

        return [
            'view' => 'packs',
            'data' => $this->buildViewData(),
        ];
    }


    /*
     * Agrupa libros por categoría para renderizar selects por pack.
    */
    private function groupBooksByCategory(array $books): array
    {
        $grouped = [];

        foreach ($books as $book) {
            foreach ($book->getCategories() as $category) {
                $grouped[$category][] = $book;
            }
        }

        return $grouped;
    }


    /*
     * Centraliza la construcción de datos de la vista packs.
    */
    private function buildViewData(array $extra = []): array
    {
        $packs = $this->service->getPacks();
        $totals = $this->service->calculateTotals($packs);
        $books = $this->service->getBooks();
        $categories = $this->service->getAvailableCategories($books);
        $booksByCategory = $this->groupBooksByCategory($books);

        return array_merge([
            'packs' => $packs,
            'totals' => $totals,
            'books' => $books,
            'categories' => $categories,
            'booksByCategory' => $booksByCategory,
        ], $extra);
    }
}
