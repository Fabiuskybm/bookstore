<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Product/models/Product.php';
require_once __DIR__ . '/PackItem.php';


final class Pack extends Product
{
    private array $items = [];

    public function __construct(
        int $id,
        string $name,
        string $slug,
        float $price,
        int $stock,
        string $imagePath,
        private string $category = '',
        private ?string $description = null,
        bool $isActive = true,
        bool $isFeatured = false
    ) {
        parent::__construct($id, $name, $slug, $price, $stock, $imagePath, $isActive, $isFeatured);
    }


    public function getDescription(): ?string { return $this->description; }
    public function getCategory(): string { return $this->category; }


    /*
    * Añade items al pack agrupando cantidades del mismo libro.
    */
    public function addItem(PackItem $item): void
    {
        foreach ($this->items as $i => $existing) {
            if ($existing->getProduct()->getId() === $item->getProduct()->getId()) {
                $newQty = $existing->getQuantity() + $item->getQuantity();
                $this->items[$i] = new PackItem(
                    $existing->getProduct(),
                    $newQty,
                    $existing->getId()
                );
                return;
            }
        }

        $this->items[] = $item;
    }


    /*
     * Devuelve los items del pack.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    
    
    /*
     * Indica si el pack contiene items.
     */
    public function hasItems(): bool
    {
        return !empty($this->items);
    }


    
    /*
     * Suma el total de todos los items del pack.
     */
    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }



    /*
     * Serializa el pack para la sesión o la vista.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'category' => $this->category,
            'items' => array_map(static fn(PackItem $item) => $item->toArray(), $this->items),
        ];
    }



    /*
     * Reconstruye un pack desde arrays de sesión.
     */
    public static function fromArray(array $data, callable $bookResolver): self
    {
        $name = (string) ($data['name'] ?? '');
        $category = (string) ($data['category'] ?? '');

        $pack = new self(0, $name, '', 0.0, 0, '', $category);

        $items = $data['items'] ?? [];

        if (is_array($items)) {

            foreach ($items as $itemData) {
                $bookId = (int) ($itemData['book_id'] ?? 0);

                if ($bookId <= 0) {
                    continue;
                }

                $book = $bookResolver($bookId);

                if ($book === null) {
                    continue;
                }

                $quantity = (int) ($itemData['quantity'] ?? 0);
                
                if ($quantity <= 0) {
                    continue;
                }

                $pack->addItem(new PackItem($book, $quantity));
            }
        }

        return $pack;
    }
}
