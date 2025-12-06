<?php
declare(strict_types=1);

require_once __DIR__ . '/../Preference/services/PreferenceService.php';



/**
 * Devuelve el valor de fallback apropiado.
 */
function i18n_fallback(string $key, string $fallback): string
{
    return $fallback !== '' ? $fallback : $key;
}


/**
 * Carga en memoria en JSON de un idioma y lo cachea.
 */
function i18n_get_lang_data(string $lang): ?array 
{
    static $cache = [];
    if (isset($cache[$lang])) return $cache[$lang];

    $file = __DIR__ . "/../i18n/{$lang}.json";
    if (!file_exists($file)) return null;

    $json = file_get_contents($file);
    if ($json === false) return null;

    $data = json_decode($json, true);
    if (!is_array($data)) return null;

    $cache[$lang] = $data;
    return $data;
}




/**
 * Devuelve un texto traducido según la clave dada.
 */
function t(string $key, string $fallback = ''): string
{
    $lang = pref_language();
    $data = i18n_get_lang_data($lang);

    if ($data === null) 
        return i18n_fallback($key, $fallback);


    $segments = explode('.', $key);
    $value = $data;

    foreach ($segments as $seg) {
        if (!is_array($value) || !array_key_exists($seg, $value)) {
            return i18n_fallback($key, $fallback);
        }

        $value = $value[$seg];
    }


    if (!is_string($value))
        return i18n_fallback($key, $fallback);


    return $value;
}