#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Application;
use App\Tenants\CredentialCipher;

require __DIR__ . '/../vendor/autoload.php';

$configPath = __DIR__ . '/../config/app.php';

if (!is_file($configPath)) {
    $configPath = __DIR__ . '/../config/app.example.php';
}

$config = require $configPath;

$app = new Application($config);
$cipher = new CredentialCipher($app->cipher());

$stdin = trim(stream_get_contents(STDIN));

if ($stdin === '') {
    fwrite(STDERR, "Proporciona el valor a cifrar por STDIN.\nEjemplo: echo valor | php bin/encrypt-tenant.php\n");
    exit(1);
}

try {
    $encrypted = $cipher->encrypt($stdin);
} catch (Throwable $e) {
    fwrite(STDERR, 'No se pudo cifrar: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

echo $encrypted, PHP_EOL;
