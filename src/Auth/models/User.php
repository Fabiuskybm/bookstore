<?php
declare(strict_types=1);

final class User
{
    public function __construct(
        private int $id,
        private string $username,
        private string $email,
        private array $roles = [ROLE_USER]
    ) {}

    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getRoles(): array { return $this->roles; }

    public function isAdmin(): bool
    {
        return in_array(ROLE_ADMIN, $this->roles, true);
    }
}
