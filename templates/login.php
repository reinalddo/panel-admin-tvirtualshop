<?php
/** @var App\Application $app */
/** @var string|null $error */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($app->name(), ENT_QUOTES, 'UTF-8') ?> · Acceso</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/main.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; }
        form { background: rgba(15, 23, 42, 0.85); padding: 2rem; border-radius: 8px; min-width: 320px; }
        label { display: block; margin-bottom: 0.25rem; font-size: 0.9rem; }
        input { width: 100%; padding: 0.6rem; margin-bottom: 1rem; border: 1px solid #1e293b; border-radius: 4px; }
        button { width: 100%; padding: 0.75rem; background: #2563eb; border: none; color: #fff; font-weight: 600; border-radius: 4px; cursor: pointer; }
        .error { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fecaca; padding: 0.75rem; margin-bottom: 1rem; border-radius: 4px; }
    </style>
</head>
<body>
    <form method="post" autocomplete="off">
        <h1 style="text-align:center; margin-bottom:1.5rem;">Panel Master</h1>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <label for="email">Correo</label>
        <input type="email" name="email" id="email" required autofocus>
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" required>
        <button type="submit" name="action" value="login">Ingresar</button>
    </form>
</body>
</html>
