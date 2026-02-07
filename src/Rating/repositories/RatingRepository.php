<?php
declare(strict_types=1);


final class RatingRepository
{
    public function __construct(private PDO $pdo) {}

    
    /**
     * Convierte product_id (edición) -> work_id (obra).
     */
    private function getWorkIdByProductId(int $productId): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT work_id FROM books WHERE product_id = :product_id LIMIT 1'
        );
        $stmt->execute(['product_id' => $productId]);

        $workId = $stmt->fetchColumn();
        if ($workId === false) return null;

        return (int) $workId;
    }


    public function upsertVote(int $userId, int $productId, int $value): bool
    {
        $workId = $this->getWorkIdByProductId($productId);
        if ($workId === null) return false;

        // true si fue update, false si fue insert (en MySQL suele ser 2 vs 1)
        $stmt = $this->pdo->prepare(
            'INSERT INTO ratings (user_id, work_id, value)
             VALUES (:user_id, :work_id, :value)
             ON DUPLICATE KEY UPDATE value = VALUES(value)'
        );

        $stmt->execute([
            'user_id' => $userId,
            'work_id' => $workId,
            'value' => $value,
        ]);

        return ((int) $stmt->rowCount()) === 2;
    }


    public function getStats(int $productId): array
    {
        $workId = $this->getWorkIdByProductId($productId);
        if ($workId === null) {
            return [
                'average' => 0.0,
                'count' => 0,
                'distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            ];
        }

        $summaryStmt = $this->pdo->prepare(
            'SELECT AVG(value) AS avg_value, COUNT(*) AS total
             FROM ratings
             WHERE work_id = :work_id'
        );
        $summaryStmt->execute(['work_id' => $workId]);
        $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $distributionStmt = $this->pdo->prepare(
            'SELECT value, COUNT(*) AS votes
             FROM ratings
             WHERE work_id = :work_id
             GROUP BY value'
        );
        $distributionStmt->execute(['work_id' => $workId]);
        $rows = $distributionStmt->fetchAll(PDO::FETCH_ASSOC);

        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($rows as $row) {
            $v = (int) ($row['value'] ?? 0);
            $votes = (int) ($row['votes'] ?? 0);
            if ($v >= 1 && $v <= 5) $distribution[$v] = $votes;
        }

        return [
            'average' => isset($summary['avg_value']) ? (float) $summary['avg_value'] : 0.0,
            'count' => isset($summary['total']) ? (int) $summary['total'] : 0,
            'distribution' => $distribution,
        ];
    }


    public function getUserVote(int $userId, int $productId): ?int
    {
        $workId = $this->getWorkIdByProductId($productId);
        if ($workId === null) return null;

        $stmt = $this->pdo->prepare(
            'SELECT value
             FROM ratings
             WHERE user_id = :user_id AND work_id = :work_id
             LIMIT 1'
        );

        $stmt->execute([
            'user_id' => $userId,
            'work_id' => $workId,
        ]);

        $val = $stmt->fetchColumn();
        if ($val === false) return null;

        return (int) $val;
    }
}
