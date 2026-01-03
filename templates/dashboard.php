<?php
/** @var App\Application $app */
/** @var array<int, array<string, mixed>> $tenants */
/** @var array<string, mixed>|null $selectedTenant */
/** @var array<string, array<int, array<string, mixed>>> $definitionGroups */
/** @var array<string, string> $tenantSettings */
/** @var string|null $message */
/** @var string|null $error */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($app->name(), ENT_QUOTES, 'UTF-8') ?> · Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/main.css">
    <style>
        body { flex-direction: column; padding: 2rem; align-items: stretch; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        table { width: 100%; border-collapse: collapse; background: rgba(15, 23, 42, 0.85); }
        th, td { padding: 0.75rem 1rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2); }
        th { text-align: left; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #94a3b8; }
        td { color: #e2e8f0; }
        a.button, button.link { padding: 0.5rem 0.75rem; border-radius: 4px; background: #2563eb; color: #fff; text-decoration: none; border: none; cursor: pointer; }
        form.inline { display: inline; }
        .status { padding: 0.25rem 0.5rem; border-radius: 999px; font-size: 0.75rem; text-transform: uppercase; }
        .status.activo { background: rgba(34,197,94,0.15); color: #bbf7d0; }
        .status.inactivo { background: rgba(248,113,113,0.15); color: #fecaca; }
        .status.suspendido { background: rgba(250,204,21,0.15); color: #fef08a; }
    </style>
</head>
<body>
<header>
    <div>
        <h1><?= htmlspecialchars($app->name(), ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Selecciona un tenant para gestionar sus configuraciones.</p>
    </div>
    <form class="inline" method="post">
        <button class="link" type="submit" name="action" value="logout">Cerrar sesión</button>
    </form>
</header>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Dominio</th>
        <th>Estado</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tenants as $tenant): ?>
        <tr>
            <td><?= (int) $tenant['id'] ?></td>
            <td><?= htmlspecialchars($tenant['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($tenant['dominio'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>
                <span class="status <?= htmlspecialchars($tenant['estado'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($tenant['estado'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </td>
            <td>
                <form class="inline" method="get">
                    <input type="hidden" name="tenant" value="<?= (int) $tenant['id'] ?>">
                    <button class="link" type="submit">Abrir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php if ($selectedTenant): ?>
    <section style="margin-top:2rem;">
        <h2>Tenant seleccionado: <?= htmlspecialchars($selectedTenant['nombre'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p>Dominio: <?= htmlspecialchars($selectedTenant['dominio'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php
            $tenantId = (int) $selectedTenant['id'];
            require __DIR__ . '/settings_form.php';
        ?>
    </section>
<?php endif; ?>

</body>
</html>
