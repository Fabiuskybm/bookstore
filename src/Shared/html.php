<?php
declare(strict_types=1);

// ==========================
// |  HTML helpers (views)  |  
// ==========================


/**
 * Escapa texto para su impresión segura en HTML.
 * 
 * Convierte caracteres especiales en entidades para evitar
 * errores de renderizado o posibles inyecciones de código.
 * 
 * Se utiliza en las funciones de presentación que imprimen
 * contenido dinámico.
 */
function e(string|int|float|null $text): string {
    return htmlspecialchars((string) ($text ?? ''), ENT_QUOTES, 'UTF-8');
}

