<?php
declare(strict_types=1);

final class WishlistRepository
{
    public function __construct(private PDO $pdo) {}


    public function getIdsByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT product_id FROM wishlist_items WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (!is_array($ids)) return [];

        return array_values(array_map('intval', $ids));
    }


    public function add(int $userId, int $productId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO wishlist_items (user_id, product_id)
             VALUES (:user_id, :product_id)
             ON DUPLICATE KEY UPDATE created_at = created_at'
        );

        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }


    public function remove(int $userId, int $productId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM wishlist_items WHERE user_id = :user_id AND product_id = :product_id'
        );
        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }


    public function bulkRemove(int $userId, array $productIds): void
    {
        if (empty($productIds)) return;

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $this->pdo->prepare(
            "DELETE FROM wishlist_items WHERE user_id = ? AND product_id IN ($placeholders)"
        );

        $stmt->execute(array_merge([$userId], $productIds));
    }


    public function clear(int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM wishlist_items WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
    }

    
    public function has(int $userId, int $productId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM wishlist_items WHERE user_id = :user_id AND product_id = :product_id LIMIT 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return (bool) $stmt->fetchColumn();
    }
}
