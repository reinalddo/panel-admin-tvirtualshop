<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$configFile = __DIR__ . '/config/app.php';

if (!is_file($configFile)) {
    $configFile = __DIR__ . '/config/app.example.php';
}

$config = require $configFile;

$app = new App\Application($config);

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($app->name(), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <h1><?= htmlspecialchars($app->name(), ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Configurador centralizado en construcción.</p>
</body>
</html>
