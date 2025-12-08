<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Auth/services/AuthService.php';


class AdminController
{
    public function show(): array
    {
        $user = auth_user();

        if ($user === null || !$user->isAdmin()) {
            header('Location: index.php?view=home');
            exit;
        }

        return [
            'view' => 'admin',
            'data' => [],
        ];
    }
}
