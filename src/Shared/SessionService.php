<?php
declare(strict_types=1);


function session_start_safe(): void {
    if (session_status() === PHP_SESSION_NONE)
        session_start();
}


function session_set(string $key, mixed $value): void {
    $_SESSION[$key] = $value;
}


function session_get(string $key, mixed $default = null): mixed {
    return $_SESSION[$key] ?? $default;
}


function session_has(string $key): bool {
    return array_key_exists($key, $_SESSION);
}


function session_destroy_all(): void {
    $_SESSION = [];

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
}