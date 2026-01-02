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
    }

    public function name(): string
    {
        return $this->config['name'] ?? 'Panel Master';
    }

    public function cipher(): SecretCipher
    {
        $loader = new MasterKeyLoader($this->config);

        return $loader->getCipher();
    }
}
