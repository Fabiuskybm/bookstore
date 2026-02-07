<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Shared/Database.php';
require_once __DIR__ . '/../../Preference/services/PreferenceService.php';
require_once __DIR__ . '/../../Book/repositories/BookRepository.php';

final class ProductController
{
    public function show(): array
    {
        $productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $lang = pref_language();

        if ($productId <= 0) {
            // Si el ID no es válido redirigimos a home para mantener un flujo simple.
            return ['redirect' => 'home'];
        }

        $pdo = Database::getInstance()->pdo();
        $repo = new BookRepository($pdo);
        $detail = $repo->findDetailByIdAndLang($productId, $lang);

        if ($detail === null) {
            // Mantenemos el mismo patrón de la app: intentamos resolver la edición equivalente.
            $equivalentProductId = $repo->findEquivalentProductIdByLang($productId, $lang);

            if ($equivalentProductId !== null) {
                return ['redirect' => 'view=product&id=' . $equivalentProductId];
            }

            // Si no existe equivalente en el idioma solicitado, volvemos al catálogo.
            return ['redirect' => 'home'];
        }

        return [
            'view' => 'product',
            'data' => [
                'book' => $detail['book'],
                'synopsis' => $detail['synopsis'],
                'publishedYear' => $detail['publishedYear'],
                'pages' => $detail['pages'],
            ],
        ];
    }
}
