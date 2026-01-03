<?php

namespace App\Tenants;

use App\Infrastructure\Database\PdoConnectionFactory;
use RuntimeException;
use PDO;

final class TenantConnectionFactory
{
    private PDO $panelPdo;
    private CredentialCipher $cipher;

    public function __construct(PDO $panelPdo, CredentialCipher $cipher)
    {
        $this->panelPdo = $panelPdo;
        $this->cipher = $cipher;
    }

    public function createFromTenantId(int $tenantId): PDO
    {
        $stmt = $this->panelPdo->prepare(
            'SELECT t.db_host, t.db_puerto, t.db_nombre, t.estado, t.credencial_id, tc.usuario, tc.contrasena
             FROM tenants t
             LEFT JOIN tenant_credentials tc ON tc.id = t.credencial_id
             WHERE t.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $tenantId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new RuntimeException('Tenant no encontrado.');
        }

        if (empty($row['credencial_id'])) {
            throw new RuntimeException('El tenant no tiene credenciales asociadas.');
        }

        if ($row['estado'] !== 'activo') {
            throw new RuntimeException('El tenant no está activo, no se puede abrir conexión.');
        }

        $username = $this->decryptColumn($row['usuario'] ?? null, 'usuario');
        $password = $this->decryptColumn($row['contrasena'] ?? null, 'contrasena');

        $config = [
            'host' => $row['db_host'],
            'port' => (int) $row['db_puerto'],
            'name' => $row['db_nombre'],
            'user' => $username,
            'password' => $password,
            'charset' => 'utf8mb4',
        ];

        return PdoConnectionFactory::fromConfig($config);
    }

    private function decryptColumn(?string $value, string $column): string
    {
        if ($value === null || $value === '') {
            throw new RuntimeException("La columna {$column} no contiene datos cifrados.");
        }

        try {
            return $this->cipher->decrypt($value);
        } catch (RuntimeException $e) {
            $encoded = base64_encode($value);

            return $this->cipher->decrypt($encoded);
        }
    }
}
