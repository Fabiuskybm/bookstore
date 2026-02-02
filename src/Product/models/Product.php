<?php
declare(strict_types=1);

abstract class Product
{
    public function __construct(
        protected int $id,
        protected string $name,
        protected string $slug,
        protected float $price,
        protected int $stock,
        protected string $imagePath,
        protected bool $isActive = true,
        protected bool $isFeatured = false
    ) {}


    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }
    public function getPrice(): float { return $this->price; }
    public function getStock(): int { return $this->stock; }
    public function getImagePath(): string { return $this->imagePath; }
    public function isActive(): bool { return $this->isActive; }
    public function isFeatured(): bool { return $this->isFeatured; }

}
