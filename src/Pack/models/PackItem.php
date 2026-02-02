<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Product/models/Product.php';

final class PackItem
{
    private float $total;

    public function __construct(
        private Product $product,
        private int $quantity,
        private ?int $id = null
    ) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be > 0');
        }

        $this->total = $this->product->getPrice() * $this->quantity;
    }

    public function getId(): ?int { return $this->id; }
    public function getProduct(): Product { return $this->product; }
    public function getQuantity(): int { return $this->quantity; }
    public function getTotal(): float { return $this->total; }
}
