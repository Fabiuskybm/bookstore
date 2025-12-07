<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Shared/CookieService.php';
require_once __DIR__ . '/../../Auth/services/AuthService.php';


const PREF_COOKIE_LIFETIME = 60 * 60 * 24 * 30; // 30 días
const PREF_DEFAULT_LANGUAGE = 'es';
const PREF_DEFAULT_THEME = 'dark';
const PREF_DEFAULT_ITEMS_PER_PAGE = 12;
const PREF_COOKIE_PREFIX = 'preferences_';
const PREF_GUEST_COOKIE_SUFFIX = 'guest';



/**
 * Valores por defecto de las preferencias.
 */
function pref_defaults(): array
{
    return [
        'language' => PREF_DEFAULT_LANGUAGE,
        'theme' => PREF_DEFAULT_THEME,
        'items_per_page' => PREF_DEFAULT_ITEMS_PER_PAGE
    ];
}


/**
 * Nombre de la cookie de preferencias para el usuario actual.
 * 
 *  - Usuario logueado: preferences_<username>
 *  - Invitado: preferences_guest
 */
function pref_cookie_name(): string
{
    $user = auth_user();

    if ($user === null) {
        return PREF_COOKIE_PREFIX . PREF_GUEST_COOKIE_SUFFIX;
    }

    $username = $user->getUsername();

    return PREF_COOKIE_PREFIX . $username;
}


/**
 * Devuelve todas las preferencias combinadas con los valores por defecto.
 */
function pref_get_all(): array
{
    $defaults = pref_defaults();
    $cookieName = pref_cookie_name();

    $raw = get_cookie_value($cookieName);
    if ($raw === null || $raw === '') { return $defaults; }

    $data = json_decode($raw, true);
    if (!is_array($data)) { return $defaults; }

    $filtered = array_intersect_key($data, $defaults);

    return array_merge($defaults, $filtered);
}


/**
 * Idioma actual de la interfaz.
 */
function pref_language(): string
{
    $prefs = pref_get_all();
    return $prefs['language'] ?? PREF_DEFAULT_LANGUAGE;
}


/**
 * Tema actual de la interfaz.
 */
function pref_theme(): string
{
    $prefs = pref_get_all();
    return $prefs['theme'] ?? PREF_DEFAULT_THEME;
}


/**
 * Número de items por página.
 */
function pref_items_per_page(): int
{
    $prefs = pref_get_all();
    $value = $prefs['items_per_page'] ?? PREF_DEFAULT_ITEMS_PER_PAGE;

    $value = (int) $value;

    if ($value < 1) $value = PREF_DEFAULT_ITEMS_PER_PAGE;
    
    return $value;
}


function apply_items_per_page(array $books): array
{
    $itemsPerPage = pref_items_per_page();
    return array_slice($books, 0, $itemsPerPage);
}



/**
 * Guarda todas las preferencias.
 */
function pref_set_all(array $prefs): void
{
    $current = pref_get_all();

    $newPrefs = [
        'language' => $prefs['language'] ?? $current['language'],
        'theme' => $prefs['theme'] ?? $current['theme'],

        'items_per_page' => isset($prefs['items_per_page'])
            ? max(1, (int) $prefs['items_per_page'])
            : ($current['items_per_page'] ?? PREF_DEFAULT_ITEMS_PER_PAGE),
    ];

    $json = json_encode($newPrefs, JSON_UNESCAPED_UNICODE);
    if ($json === false) return;

    set_cookie_value(
        pref_cookie_name(),
        $json,
        PREF_COOKIE_LIFETIME
    );
}


/**
 * Cambia solo el idioma.
 */
function pref_set_language(string $language): void
{
    pref_set_all(['language' => $language]);
}


/**
 * Cambia solo el tema.
 */
function pref_set_theme(string $theme): void
{
    pref_set_all(['theme' => $theme]);
}