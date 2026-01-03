<?php

namespace App\Audit;

use PDO;

final class AuditLogger
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param array<string, mixed> $detail
     */
    public function log(?int $adminId, ?int $tenantId, string $action, array $detail = [], ?string $ip = null): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO audit_logs (admin_id, tenant_id, accion, detalle, ip) VALUES (:admin_id, :tenant_id, :accion, :detalle, :ip)'
        );

        $stmt->execute([
            'admin_id' => $adminId,
            'tenant_id' => $tenantId,
            'accion' => $action,
            'detalle' => json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'ip' => $ip,
        ]);
    }
}
