<?php
declare(strict_types=1);

require_once __DIR__ . '/Role.php';


final class User
{
    public function __construct(
        private int $id,
        private string $username,
        private string $email,
        private array $roles
    ) {}

    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getRoles(): array { return $this->roles; }


    public function hasRole(Role $role): bool
    {
        return in_array($role->value, $this->roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::Admin);
    }
}
