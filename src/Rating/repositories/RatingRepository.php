<?php
declare(strict_types=1);

final class RatingRepository
{
    public function __construct(private PDO $pdo) {}



    public function upsertVote(int $userId, int $productId, int $value): bool
    {
        // Devuelve true si fue actualización, false si fue inserción.
        $stmt = $this->pdo->prepare(
            'INSERT INTO ratings (user_id, product_id, value)
             VALUES (:user_id, :product_id, :value)
             ON DUPLICATE KEY UPDATE value = VALUES(value)'
        );

        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
            'value' => $value,
        ]);

        $affected = (int) $stmt->rowCount();

        return $affected === 2;
    }

    
    public function getStats(int $productId): array
    {
        // Consulta principal de media y total de votos.
        $summaryStmt = $this->pdo->prepare(
            'SELECT AVG(value) AS avg_value, COUNT(*) AS total FROM ratings WHERE product_id = :product_id'
        );

        $summaryStmt->execute(['product_id' => $productId]);
        $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // Consulta de distribución por valor (1..5).
        $distributionStmt = $this->pdo->prepare(
            'SELECT value, COUNT(*) AS votes
             FROM ratings
             WHERE product_id = :product_id
             GROUP BY value'
        );

        $distributionStmt->execute(['product_id' => $productId]);
        $rows = $distributionStmt->fetchAll(PDO::FETCH_ASSOC);

        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($rows as $row) {
            $value = (int) ($row['value'] ?? 0);
            $votes = (int) ($row['votes'] ?? 0);

            if ($value >= 1 && $value <= 5) {
                $distribution[$value] = $votes;
            }
        }

        return [
            'average' => isset($summary['avg_value']) ? (float) $summary['avg_value'] : 0.0,
            'count' => isset($summary['total']) ? (int) $summary['total'] : 0,
            'distribution' => $distribution,
        ];
    }
}
