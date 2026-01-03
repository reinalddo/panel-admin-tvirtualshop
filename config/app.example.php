<?php

return [
    'name' => 'Panel Master',
    'debug' => false,
    'timezone' => 'America/Mexico_City',
    'encryption_key_path' => __DIR__ . '/keys/master.key',
    'encryption_key_env' => 'PANEL_MASTER_KEY',
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'panel_master',
        'user' => 'panel_master_user',
        'password' => 'cambia-esta-clave',
        'charset' => 'utf8mb4',
    ],
];
