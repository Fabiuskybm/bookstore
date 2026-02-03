<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Product/models/Product.php';


final class Book extends Product
{
    public function __construct(
        int $id,
        string $name,
        string $slug,
        float $price,
        int $stock,
        string $imagePath,
        private string $author,
        private array $categories,
        private string $format,
        bool $isActive = true,
        bool $isFeatured = false
    ) {
        parent::__construct($id, $name, $slug, $price, $stock, $imagePath, $isActive, $isFeatured);
    }

    public function getAuthor(): string { return $this->author; }
    public function getCategories(): array { return $this->categories; }
    public function getFormat(): string { return $this->format; }

    public function hasCategory(string $category): bool
    {
        return in_array($category, $this->categories, true);
    }
}
