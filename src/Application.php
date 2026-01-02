<?php

namespace App;

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
}
