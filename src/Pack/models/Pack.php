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
        private ?string $description = null,
        bool $isActive = true,
        bool $isFeatured = false
    ) {
        parent::__construct($id, $name, $slug, $price, $stock, $imagePath, $isActive, $isFeatured);
    }


    public function getDescription(): ?string { return $this->description; }


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


    public function getItems(): array
    {
        return $this->items;
    }

    
    public function hasItems(): bool
    {
        return !empty($this->items);
    }

    
    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }
}
