<?php

namespace App;

use App\Security\MasterKeyLoader;
use App\Security\SecretCipher;

class Application
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;

        if (!empty($config['timezone'])) {
            date_default_timezone_set($config['timezone']);
        }
    }

    public function name(): string
    {
        return $this->config['name'] ?? 'Panel Master';
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function config(?string $key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return $this->config[$key] ?? null;
    }

    public function cipher(): SecretCipher
    {
        $loader = new MasterKeyLoader($this->config);

        return $loader->getCipher();
    }
}
