<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/PreferenceService.php';


class PreferenceController
{

    public function setLanguage(): array
    {
        $lang = $_POST['language'] ?? '';

        if (in_array($lang, PREFERENCE_LANGUAGES, true)) {
            pref_set_language($lang);
        }

        $return = $_POST['_return'] ?? ($_GET['view'] ?? 'home');

        return [ 'redirect' => $return ];
    }


}