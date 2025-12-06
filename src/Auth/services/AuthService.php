<?php
declare(strict_types=1);


require_once __DIR__ . '/../../Shared/SessionService.php';
require_once __DIR__ . '/../../Shared/config.php';
require_once __DIR__ . '/../models/User.php';


/**
 * Autentica al usuario.
 * 
 * Devuelve un objeto User si las credenciales son correctas.
 * Devuelve null si falla.
 */
function auth_authenticate(string $username, string $password): ?User 
{
    $cleanUser = trim(mb_strtolower($username));

    // Admin
    if (
        $cleanUser === mb_strtolower(AUTH_ADMIN_USER) &&
        $password === AUTH_PASS
    ) {
        return new User(AUTH_ADMIN_USER, ROLE_ADMIN);
    }


    // Usuario normal
    if ($password === AUTH_PASS) {
        $displayName = trim($username);
        if ($displayName === '') $displayName = 'Anónimo';
        return new User($displayName, ROLE_USER);
    }

    return null;
}



// Guarda el usuario autenticado en la sesión.
function auth_login(User $user): void {
    session_set('user', [
        'username' => $user->getUsername(),
        'role' => $user->getRole()
    ]);
}


// Cierra la sesión
function auth_logout(): void {
    session_destroy_all();
}


// Devuelve el usuario actual o null si no hay sesión
function auth_user(): ?User 
{
    if (!session_has('user')) return null;

    $data = session_get('user');

    if (!is_array($data) || !isset($data['username'], $data['role'])) {
        return null;
    }

    return new User($data['username'], $data['role']);
}


// Comprobar si el usuario actual es admin.
function auth_is_admin(): bool {
    $user = auth_user();
    return $user !== null && $user->isAdmin();
}