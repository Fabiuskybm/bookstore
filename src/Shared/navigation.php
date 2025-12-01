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
    return [
        [
            'view' => 'home',
            'label' => 'Inicio'
        ],
    ];
}