<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Book.php';

final class BookRepository
{
    public function __construct(private PDO $pdo) {}


    public function findAllByLang(string $lang): array
    {
        $sql = "
            SELECT
                p.id,
                p.name,
                p.slug,
                p.price,
                p.stock,
                p.image_path,
                p.is_active,
                p.is_featured,
                b.format,
                COALESCE(MAX(a.name), '') AS author,
                GROUP_CONCAT(DISTINCT c.slug ORDER BY c.slug SEPARATOR ',') AS categories
            FROM products p
            INNER JOIN books b ON b.product_id = p.id
            INNER JOIN book_works bw ON bw.id = b.work_id
            LEFT JOIN book_work_authors bwa ON bwa.work_id = bw.id
            LEFT JOIN authors a ON a.id = bwa.author_id
            LEFT JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN categories c ON c.id = pc.category_id
            WHERE b.lang = :lang AND p.is_active = 1
            GROUP BY
                p.id, p.name, p.slug, p.price, p.stock, p.image_path,
                p.is_active, p.is_featured, b.format
            ORDER BY p.is_featured DESC, p.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['lang' => $lang]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $books = [];

        foreach ($rows as $r) {
            $books[] = $this->mapRowToBook($r);
        }

        return $books;
    }


    public function findFeaturedByLang(string $lang): array
    {
        $sql = "
            SELECT
                p.id,
                p.name,
                p.slug,
                p.price,
                p.stock,
                p.image_path,
                p.is_active,
                p.is_featured,
                b.format,
                COALESCE(MAX(a.name), '') AS author,
                GROUP_CONCAT(DISTINCT c.slug ORDER BY c.slug SEPARATOR ',') AS categories
            FROM products p
            INNER JOIN books b ON b.product_id = p.id
            INNER JOIN book_works bw ON bw.id = b.work_id
            LEFT JOIN book_work_authors bwa ON bwa.work_id = bw.id
            LEFT JOIN authors a ON a.id = bwa.author_id
            LEFT JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN categories c ON c.id = pc.category_id
            WHERE b.lang = :lang AND p.is_featured = 1 AND p.is_active = 1
            GROUP BY
                p.id, p.name, p.slug, p.price, p.stock, p.image_path,
                p.is_active, p.is_featured, b.format
            ORDER BY p.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['lang' => $lang]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $books = [];

        foreach ($rows as $r) {
            $books[] = $this->mapRowToBook($r);
        }

        return $books;
    }


    public function findDetailByIdAndLang(int $bookId, string $lang): ?array
    {
        $sql = "
            SELECT
                p.id,
                p.name,
                p.slug,
                p.price,
                p.stock,
                p.image_path,
                p.is_active,
                p.is_featured,
                b.format,
                b.pages,
                b.synopsis,
                b.published_year,
                COALESCE(MAX(a.name), '') AS author,
                GROUP_CONCAT(DISTINCT c.slug ORDER BY c.slug SEPARATOR ',') AS categories
            FROM products p
            INNER JOIN books b ON b.product_id = p.id
            INNER JOIN book_works bw ON bw.id = b.work_id
            LEFT JOIN book_work_authors bwa ON bwa.work_id = bw.id
            LEFT JOIN authors a ON a.id = bwa.author_id
            LEFT JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN categories c ON c.id = pc.category_id
            WHERE p.id = :id AND b.lang = :lang AND p.is_active = 1
            GROUP BY
                p.id, p.name, p.slug, p.price, p.stock, p.image_path,
                p.is_active, p.is_featured, b.format, b.pages, b.synopsis, b.published_year
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $bookId,
            'lang' => $lang,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        $book = $this->mapRowToBook($row);

        return [
            'book' => $book,
            'synopsis' => (string) ($row['synopsis'] ?? ''),
            'publishedYear' => isset($row['published_year']) ? (int) $row['published_year'] : null,
            'pages' => isset($row['pages']) ? (int) $row['pages'] : null,
        ];
    }


    public function findEquivalentProductIdByLang(int $sourceProductId, string $targetLang): ?int
    {
        $sql = "
            SELECT b2.product_id
            FROM books b1
            INNER JOIN books b2 ON b2.work_id = b1.work_id
            INNER JOIN products p2 ON p2.id = b2.product_id
            WHERE b1.product_id = :sourceProductId
                AND b2.lang = :targetLang
                AND p2.is_active = 1
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'sourceProductId' => $sourceProductId,
            'targetLang' => $targetLang,
        ]);

        $result = $stmt->fetchColumn();

        if ($result === false) {
            return null;
        }

        return (int) $result;
    }


    private function mapRowToBook(array $r): Book
    {
        $categories = [];
        if (!empty($r['categories'])) {
            $categories = array_values(array_filter(explode(',', (string) $r['categories'])));
        }

        return new Book(
            (int) $r['id'],
            (string) $r['name'],
            (string) $r['slug'],
            (float) $r['price'],
            (int) $r['stock'],
            (string) $r['image_path'],
            (string) $r['author'],
            $categories,
            (string) $r['format'],
            (bool) $r['is_active'],
            (bool) $r['is_featured']
        );
    }
}
