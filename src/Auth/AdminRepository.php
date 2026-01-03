<?php

namespace App\Auth;

use PDO;

final class AdminRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findActiveByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_users WHERE email = :email AND activo = 1 LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function touchLogin(int $userId): void
    {
        $stmt = $this->pdo->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $userId]);
    }
}
