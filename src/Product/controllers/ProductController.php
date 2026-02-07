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
        if ($productId <= 0) {
            return ['redirect' => 'home'];
        }

        $lang = pref_language();
        $repo = $this->bookRepo();

        // 1) Intento normal: id + idioma actual
        $detail = $repo->findDetailByIdAndLang($productId, $lang);
        if ($detail !== null) {
            return $this->renderDetail($detail);
        }

        // 2) Si no existe en el idioma actual: ir a la edición equivalente (work_id) en ese idioma
        $equivalentProductId = $repo->findEquivalentProductIdByLang($productId, $lang);
        if ($equivalentProductId !== null) {
            return ['redirect' => 'view=product&id=' . $equivalentProductId];
        }

        // 3) Último fallback: mostrar el mismo id en el otro idioma (evita romper wishlist mezclada)
        $altLang = $this->altLang($lang);
        $detail = $repo->findDetailByIdAndLang($productId, $altLang);
        if ($detail !== null) {
            return $this->renderDetail($detail);
        }

        return ['redirect' => 'home'];
    }


    private function bookRepo(): BookRepository
    {
        $pdo = Database::getInstance()->pdo();
        return new BookRepository($pdo);
    }

    private function altLang(string $lang): string
    {
        return $lang === 'es' ? 'en' : 'es';
    }


    private function renderDetail(array $detail): array
    {
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
