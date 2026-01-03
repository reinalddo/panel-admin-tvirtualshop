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
$settingDefinitions = new App\Settings\SettingDefinitionRepository($pdo);
$tenantCipher = new App\Tenants\CredentialCipher($app->cipher());
$tenantConnectionFactory = new App\Tenants\TenantConnectionFactory($pdo, $tenantCipher);
$tenantSettingsRepository = new App\Tenants\TenantSettingsRepository($tenantConnectionFactory);

$error = null;
$message = null;
$action = $_POST['action'] ?? null;
$currentTenantId = isset($_GET['tenant']) ? (int) $_GET['tenant'] : 0;

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

    if ($action === 'update_settings' && $authService->check()) {
        $tenantId = (int) ($_POST['tenant'] ?? 0);
        $settings = $_POST['settings'] ?? [];

        if ($tenantId > 0 && is_array($settings)) {
            $currentTenantId = $tenantId;
            try {
                foreach ($settings as $key => $value) {
                    $tenantSettingsRepository->upsert($tenantId, (string) $key, normalizeSettingValue($value));
                }

                $auditLogger->log($authService->userId(), $tenantId, 'update_settings', ['keys' => array_keys($settings)], $_SERVER['REMOTE_ADDR'] ?? null);
                $message = 'Configuraciones guardadas correctamente.';
            } catch (Throwable $e) {
                $error = 'No se pudo guardar las configuraciones: ' . $e->getMessage();
            }
        }
    }
}

if (!$authService->check()) {
    require __DIR__ . '/templates/login.php';
    return;
}

$tenants = $tenantRepository->all();
$selectedTenant = null;
$definitionGroups = [];
$tenantSettings = [];

if ($currentTenantId > 0) {
    $selectedTenant = $tenantRepository->find($currentTenantId);

    if ($selectedTenant) {
        if ($action !== 'update_settings') {
            $auditLogger->log($authService->userId(), $currentTenantId, 'view_tenant', ['tenant' => $currentTenantId], $_SERVER['REMOTE_ADDR'] ?? null);
        }
        $definitions = $settingDefinitions->allWithGroups();
        $definitionGroups = App\Settings\GroupedSettings::group($definitions);
        $tenantSettings = $tenantSettingsRepository->listAll($currentTenantId);
    }
}

require __DIR__ . '/templates/dashboard.php';

/**
 * @param mixed $value
 */
function normalizeSettingValue($value): string
{
    if (is_array($value)) {
        return (string) end($value);
    }

    return (string) $value;
}
