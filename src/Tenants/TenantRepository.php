<?php

namespace App\Tenants;

use PDO;

final class TenantRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, nombre, dominio, estado FROM tenants ORDER BY nombre');

        return $stmt->fetchAll() ?: [];
    }

    public function find(int $tenantId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tenants WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $tenantId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }
}
