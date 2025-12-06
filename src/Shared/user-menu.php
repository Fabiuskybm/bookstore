<?php
declare(strict_types=1);

require_once __DIR__ . '/../Auth/models/User.php';


/**
 * Devuelve los elementos del menú de usuario del header.
 *
 * Cada elemento puede ser:
 *  - type: 'link' -> enlace normal a una vista (?view=...)
 *  - type: 'logout' -> acción de cerrar sesión (form POST)
 *
 * Campos comunes:
 *  - label: texto visible en el menú
 *  - view: nombre de la vista (solo para type 'link')
 */
function get_user_menu_items(User $user): array
{
    return [
        [
            'type' => 'link',
            'view' => 'wishlist',
            'key'  => 'wishlist',
        ],
        [
            'type' => 'logout',
            'key'  => 'logout',
        ],
    ];
}

