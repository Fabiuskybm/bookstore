<?php
declare(strict_types=1);


class User {

    public function __construct(
        private string $username,
        private string $role
    ) {}


    public function getUsername(): string { return $this->username; }
    public function getRole(): string { return $this->role; }
    public function isAdmin(): bool { return $this->role === ROLE_ADMIN; }

}