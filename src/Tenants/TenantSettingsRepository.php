<?php

namespace App\Tenants;

use PDO;

final class TenantSettingsRepository
{
    private TenantConnectionFactory $connectionFactory;

    public function __construct(TenantConnectionFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * @return array<string, string>
     */
    public function listAll(int $tenantId): array
    {
        $pdo = $this->connectionFactory->createFromTenantId($tenantId);

        $stmt = $pdo->query('SELECT nombre_setting, valor_setting FROM configuraciones');
        $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['nombre_setting']] = (string) $row['valor_setting'];
        }

        return $settings;
    }

    public function get(int $tenantId, string $key): ?string
    {
        $pdo = $this->connectionFactory->createFromTenantId($tenantId);

        $stmt = $pdo->prepare('SELECT valor_setting FROM configuraciones WHERE nombre_setting = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $value = $stmt->fetchColumn();

        return $value === false ? null : (string) $value;
    }

    public function upsert(int $tenantId, string $key, string $value): void
    {
        $pdo = $this->connectionFactory->createFromTenantId($tenantId);

        $stmt = $pdo->prepare(
            'INSERT INTO configuraciones (nombre_setting, valor_setting) VALUES (:key, :value)
             ON DUPLICATE KEY UPDATE valor_setting = VALUES(valor_setting), actualizado_en = CURRENT_TIMESTAMP'
        );

        $stmt->execute([
            'key' => $key,
            'value' => $value,
        ]);
    }

    public function delete(int $tenantId, string $key): void
    {
        $pdo = $this->connectionFactory->createFromTenantId($tenantId);

        $stmt = $pdo->prepare('DELETE FROM configuraciones WHERE nombre_setting = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
    }
}
