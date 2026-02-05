<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Product/models/Product.php';


final class PackItem
{

    public function __construct(
        private Product $product,
        private int $quantity,
        private ?int $id = null
    ) {}


    public function getId(): ?int { return $this->id; }
    public function getProduct(): Product { return $this->product; }
    public function getQuantity(): int { return $this->quantity; }


    /*
     * Calcula el total del item según precio y cantidad.
     */
    public function getTotal(): float
    {
        return $this->product->getPrice() * $this->quantity;
    }

    
    /*
     * Serializa el item para la sesión o la vista.
     */
    public function toArray(): array
    {
        return [
            'book_id' => $this->product->getId(),
            'name' => $this->product->getName(),
            'quantity' => $this->quantity,
            'price' => $this->product->getPrice(),
            'total' => $this->getTotal(),
            'stock' => $this->product->getStock(),
        ];
    }
}
