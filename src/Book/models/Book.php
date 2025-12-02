<?php
declare(strict_types=1);


class Book {

    public function __construct(
        private string $id,
        private string $title,
        private string $author,
        private float $price,
        private string $coverImage,
        private array $categories,
        private string $format,
        private bool $isFeatured
    ) {}


    public function getId(): string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getPrice(): float { return $this->price; }
    public function getCoverImage(): string { return $this->coverImage; }
    public function getCategories(): array { return $this->categories; }
    public function getFormat(): string { return $this->format; }
    public function isFeatured(): bool { return $this->isFeatured; }


    /*----------------
    |     HELPERS     
    |----------------*/

    public function hasCategory(string $category): bool {
        return in_array($category, $this->categories, true);
    }

}