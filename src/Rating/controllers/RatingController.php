<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/RatingService.php';
require_once __DIR__ . '/../../Auth/services/AuthService.php';

final class RatingController
{
    public function __construct(
        private RatingService $service = new RatingService()
    ) {}

    public function vote(): array
    {
        $user = auth_user();
        if ($user === null) {
            http_response_code(401);
            return [
                'ok' => false,
                'error' => 'auth_required',
            ];
        }

        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $value = isset($_POST['value']) ? (int) $_POST['value'] : 0;

        $result = $this->service->vote($user->getId(), $productId, $value);

        if (($result['ok'] ?? false) === false) {
            http_response_code(400);
        }

        return $result;
    }

    public function stats(): array
    {
        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

        if ($productId <= 0) {
            http_response_code(400);
            return [
                'ok' => false,
                'error' => 'invalid_product_id',
            ];
        }

        return [
            'ok' => true,
            'stats' => $this->service->stats($productId),
        ];
    }
}
