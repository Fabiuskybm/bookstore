<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Shared/SessionService.php';
require_once __DIR__ . '/../../Shared/config.php';
require_once __DIR__ . '/../../Shared/Database.php';

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../repositories/UserRepository.php';


function auth_repo(): UserRepository
{
    $pdo = Database::getInstance()->pdo();
    return new UserRepository($pdo);
}


/**
 * Login real contra BD.
 */
function auth_authenticate(string $login, string $password): ?User
{
    $login = trim($login);
    if ($login === '') return null;

    $repo = auth_repo();

    $row = $repo->findByUsernameOrEmail($login);
    if (!$row) return null;

    if (!password_verify($password, $row['password_hash'])) {
        return null;
    }

    $userId = (int) $row['id'];
    $roles = $repo->getRolesByUserId($userId);

    // Si por lo que sea no tiene roles asignados, caemos a ROLE_USER
    if (!$roles) $roles = [Role::User->value];

    return new User(
        $userId,
        (string) $row['username'],
        (string) $row['email'],
        $roles
    );
}

/**
 * Registro real contra BD.
 * Devuelve User si va bien; null si falla por duplicado u otro error controlable.
 */
function auth_register(string $username, string $email, string $password): ?User
{
    $repo = auth_repo();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $userId = $repo->createUser($username, $email, $passwordHash);

    } catch (PDOException $e) {

        if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
            return null;
        }
        throw $e;
    }

    return new User($userId, $username, $email, [Role::User->value]);
}


// Guarda el usuario autenticado en la sesión.
function auth_login(User $user): void
{
    session_set('user', [
        'id' => $user->getId(),
        'username' => $user->getUsername(),
        'email' => $user->getEmail(),
        'roles' => $user->getRoles(),
    ]);
}

// Cierra la sesión
function auth_logout(): void
{
    session_destroy_all();
}

// Devuelve el usuario actual o null si no hay sesión
function auth_user(): ?User
{
    if (!session_has('user')) return null;

    $data = session_get('user');

    if (!is_array($data)) return null;
    if (!isset($data['id'], $data['username'], $data['email'], $data['roles'])) return null;
    if (!is_array($data['roles'])) return null;

    return new User(
        (int) $data['id'],
        (string) $data['username'],
        (string) $data['email'],
        $data['roles']
    );
}

function auth_is_admin(): bool
{
    $user = auth_user();
    return $user !== null && $user->isAdmin();
}
