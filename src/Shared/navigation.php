<?php
declare(strict_types=1);


/**
 * Devuelve la configuración del menú principal.
 * 
 * Cada elemento tiene:
 *  - view: nombre de la vista (?view=...)
 *  - label: texto que se muestra en el menú
 */
function get_navigation_items(): array {

    $user = auth_user();
    $isLogged = $user !== null;

    $items = [
        [ 'view' => 'home' ],
        [ 'view' => 'packs' ],
    ];

    // Si hay usuario logueado se ve la wishlist en el menú
    if ($isLogged) {
        $items[] = [ 'view' => 'wishlist' ];
    }

    $items[] = [ 'view' => 'preferences' ];
    $items[] = [ 'view' => 'where' ];
    
    if ($isLogged && $user->isAdmin()) {
        $items[] = [ 'view' => 'admin' ];
    }

    return $items;
}
