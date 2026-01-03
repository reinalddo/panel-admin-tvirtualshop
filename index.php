<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/vendor/autoload.php';

$configFile = __DIR__ . '/config/app.php';

if (!is_file($configFile)) {
    $configFile = __DIR__ . '/config/app.example.php';
}

$config = require $configFile;

$app = new App\Application($config);

$dbConfig = $app->config('db');

if (!is_array($dbConfig)) {
    throw new \RuntimeException('Configura las credenciales de la base panel_master en config/app.php.');
}

$pdo = App\Infrastructure\Database\PdoConnectionFactory::fromConfig($dbConfig);
$authService = new App\Auth\AuthService(new App\Auth\AdminRepository($pdo));
$auditLogger = new App\Audit\AuditLogger($pdo);
$tenantRepository = new App\Tenants\TenantRepository($pdo);

$error = null;
$action = $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $error = 'Debes ingresar correo y contraseña.';
        } elseif (!$authService->attempt($email, $password)) {
            $error = 'Credenciales inválidas.';
        } else {
            $auditLogger->log($authService->userId(), null, 'login', ['email' => $email], $_SERVER['REMOTE_ADDR'] ?? null);
            header('Location: /');
            exit;
        }
    }

    if ($action === 'logout') {
        $adminId = $authService->userId();
        $authService->logout();
        $auditLogger->log($adminId, null, 'logout', [], $_SERVER['REMOTE_ADDR'] ?? null);
        header('Location: /');
        exit;
    }
}

if (!$authService->check()) {
    require __DIR__ . '/templates/login.php';
    return;
}

$tenants = $tenantRepository->all();
$selectedTenant = null;

if (isset($_GET['tenant'])) {
    $tenantId = (int) $_GET['tenant'];
    $selectedTenant = $tenantRepository->find($tenantId);

    if ($selectedTenant) {
        $auditLogger->log($authService->userId(), $tenantId, 'view_tenant', ['tenant' => $tenantId], $_SERVER['REMOTE_ADDR'] ?? null);
    }
}

require __DIR__ . '/templates/dashboard.php';
