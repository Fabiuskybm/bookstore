<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Shared/Database.php';
require_once __DIR__ . '/../repositories/RatingRepository.php';

final class RatingService
{
    private RatingRepository $repository;


    public function __construct(?RatingRepository $repository = null)
    {
        // Si no se inyecta repositorio, usamos la conexión compartida.
        if ($repository === null) {
            $pdo = Database::getInstance()->pdo();
            $repository = new RatingRepository($pdo);
        }

        $this->repository = $repository;
    }



    public function vote(int $userId, int $productId, int $value): array
    {
        if ($userId <= 0 || $productId <= 0) {
            return [
                'ok' => false,
                'error' => 'invalid_identifiers',
            ];
        }

        if ($value < 1 || $value > 5) {
            return [
                'ok' => false,
                'error' => 'invalid_value',
            ];
        }

        $isUpdate = $this->repository->upsertVote($userId, $productId, $value);

        $stats = $this->stats($productId);

        return [
            'ok' => true,
            'updated' => $isUpdate,
            'userVote' => $value,
            'stats' => $stats,
        ];
    }


    public function stats(int $productId): array
    {
        if ($productId <= 0) {
            return [
                'average' => 0.0,
                'averageRounded' => 0.0,
                'count' => 0,
                'distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'stars' => [
                    'full' => 0,
                    'half' => false,
                    'empty' => 5,
                ],
            ];
        }

        $raw = $this->repository->getStats($productId);

        $average = (float) ($raw['average'] ?? 0.0);
        $count = (int) ($raw['count'] ?? 0);
        $distribution = $raw['distribution'] ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        $stars = $this->buildStarPaintData($average);

        return [
            'average' => $average,
            'averageRounded' => round($average, 2),
            'count' => $count,
            'distribution' => $distribution,
            'stars' => $stars,
        ];
    }


    public function statsWithUserVote(int $productId, ?int $userId): array
    {
        $stats = $this->stats($productId);

        $payload = [
            'ok' => true,
            'stats' => $stats,
        ];

        if ($userId !== null && $userId > 0) {
            $userVote = $this->repository->getUserVote($userId, $productId);
            if ($userVote !== null) {
                $payload['userVote'] = $userVote;
            }
        }

        return $payload;
    }


    
    private function buildStarPaintData(float $average): array
    {
        $fullStars = (int) floor($average);
        $decimal = $average - $fullStars;
        $hasHalfStar = $decimal >= 0.5;

        if ($hasHalfStar && $fullStars < 5) {
            $emptyStars = 5 - $fullStars - 1;
        } else {
            $emptyStars = 5 - $fullStars;
        }

        if ($emptyStars < 0) {
            $emptyStars = 0;
        }

        return [
            'full' => min($fullStars, 5),
            'half' => $hasHalfStar,
            'empty' => $emptyStars,
        ];
    }
}
