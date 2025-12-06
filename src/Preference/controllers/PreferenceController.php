<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/PreferenceService.php';


class PreferenceController
{

    public function setLanguage(): array
    {
        $lang = $_POST['language'] ?? '';

        if (in_array($lang, SUPPORTED_LANGUAGES, true)) {
            pref_set_language($lang);
        }

        $return = $_POST['_return'] ?? ($_GET['view'] ?? 'home');

        return [ 'redirect' => $return ];
    }


    /**
     * Actualiza las preferencias desde el formulario de la vista.
     * 
     *  - Tema (light/dark)
     *  - Items por página
     */
    public function update(): array 
    {
        $theme = $_POST['theme'] ?? null;
        $itemsPerPage = $_POST['items_per_page'] ?? null;

        $prefs = [];

        if ($theme !== null && $theme !== '') {
            $prefs['theme'] = $theme;
        }

        if ($itemsPerPage !== null && $itemsPerPage !== '') {
            $prefs['items_per_page'] = (int) $itemsPerPage;
        }

        if (!empty($prefs)) { 
            pref_set_all($prefs); 
        }

        $return = $_POST['_return'] ?? 'preferences';

        return ['redirect' => $return];
    }

}