<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Shared/Database.php';


final class UserRepository
{
    public function __construct(private PDO $pdo) {}


    public function findByUsernameOrEmail(string $login): ?array
    {
        $sql = "
            SELECT id, username, email, password_hash
            FROM users
            WHERE username = :u OR email = :e
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            'u' => $login,
            'e' => $login,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    public function getRolesByUserId(int $userId): array
    {
        $sql = "
            SELECT r.name
            FROM user_roles ur
            JOIN roles r ON r.id = ur.role_id
            WHERE ur.user_id = :user_id
            ORDER BY r.name
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $roles ?: [];
    }


    public function createUser(string $username, string $email, string $passwordHash): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password_hash)
                VALUES (:username, :email, :password_hash)
            ");
            
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash,
            ]);

            $userId = (int) $this->pdo->lastInsertId();

            $roleIdStmt = $this->pdo->prepare("SELECT id FROM roles WHERE name = 'user' LIMIT 1");
            $roleIdStmt->execute();
            $roleId = (int) $roleIdStmt->fetchColumn();

            $linkStmt = $this->pdo->prepare("
                INSERT INTO user_roles (user_id, role_id)
                    VALUES (:user_id, :role_id)
            ");

            $linkStmt->execute([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);

            $this->pdo->commit();
            return $userId;

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
