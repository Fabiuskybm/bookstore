<?php
declare(strict_types=1);


// ===============================
// |  OPCIONES DE CONFIGURACIÓN  |
// ===============================

function cookieOptions(
    int $lifetimeInSeconds,
    array $overrides = []
): array {

    $defaults = [
        'expires' => time() + $lifetimeInSeconds,
        'path' => '/',
        'secure' => false,
        'httponly' => false,
        'samesite' => 'Lax'
    ];

    return array_merge($defaults, $overrides);
}



// =============================
// |  OPERACIONES CON COOKIES  |
// =============================


function set_cookie_value(
    string $name,
    string $value,
    int $lifeTime,
    array $overrides = []
) : void {

    $options = cookieOptions($lifeTime, $overrides);
    setcookie($name, $value, $options);
}


function get_cookie_value(string $name): ?string {
    return $_COOKIE[$name] ?? null;
}


function delete_cookie(string $name): void {
    setcookie($name, '', cookieOptions(-3600));
}


