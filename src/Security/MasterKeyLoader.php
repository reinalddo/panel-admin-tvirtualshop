<?php

namespace App\Security;

use RuntimeException;

class MasterKeyLoader
{
    /** @var array<string, mixed> */
    private array $config;

    /** @param array<string, mixed> $config */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getCipher(): SecretCipher
    {
        $envVar = $this->config['encryption_key_env'] ?? null;

        if (is_string($envVar)) {
            $envValue = getenv($envVar);

            if (is_string($envValue) && $envValue !== '') {
                return SecretCipher::fromBase64($envValue);
            }
        }

        $path = $this->config['encryption_key_path'] ?? null;

        if (is_string($path) && $path !== '') {
            return SecretCipher::fromKeyFile($path);
        }

        throw new RuntimeException('No se pudo localizar la clave maestra para cifrar credenciales.');
    }
}
