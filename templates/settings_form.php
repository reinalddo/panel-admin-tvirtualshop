<?php
/** @var App\Application $app */
/** @var array<string, array<int, array<string, mixed>>> $definitionGroups */
/** @var array<string, string> $tenantSettings */
/** @var int $tenantId */
/** @var string|null $message */
/** @var string|null $error */
?>
<section style="margin-top:2rem;">
    <h2>Configuraciones</h2>
    <p>Actualiza los valores y guarda para aplicarlos al tenant.</p>

    <?php if ($message): ?>
        <div style="background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.4); color: #bbf7d0; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.4); color: #fecaca; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="update_settings">
        <input type="hidden" name="tenant" value="<?= (int) $tenantId ?>">

        <?php foreach ($definitionGroups as $groupSlug => $definitions): ?>
            <div style="margin-bottom:2rem; background: rgba(15,23,42,0.85); padding: 1.5rem; border-radius: 8px;">
                <h3 style="margin-top:0; text-transform:uppercase; letter-spacing:0.08em; font-size:0.85rem; color:#94a3b8;">
                    <?= htmlspecialchars($definitions[0]['group_name'] ?? strtoupper($groupSlug), ENT_QUOTES, 'UTF-8') ?>
                </h3>

                <?php foreach ($definitions as $definition): ?>
                    <?php
                        $key = $definition['setting_key'];
                        $value = $tenantSettings[$key] ?? ($definition['default_value'] ?? '');
                        $inputType = $definition['input_type'] ?? 'text';
                        $isSensitive = (int) ($definition['is_sensitive'] ?? 0) === 1;
                        $fieldName = "settings[{$key}]";
                    ?>
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.25rem; font-weight:600;">
                            <?= htmlspecialchars($definition['label'] ?? $key, ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($definition['description'])): ?>
                                <span style="display:block; font-size:0.8rem; color:#94a3b8;">
                                    <?= htmlspecialchars($definition['description'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php endif; ?>
                        </label>

                        <?php if ($inputType === 'textarea'): ?>
                            <textarea name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" rows="4" style="width:100%; padding:0.6rem; border-radius:4px; border:1px solid #1e293b; background:#0f172a; color:#f8fafc;"><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php elseif ($inputType === 'toggle'): ?>
                            <input type="hidden" name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" value="0">
                            <label style="display:flex; align-items:center; gap:0.5rem;">
                                <input type="checkbox" name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" value="1" <?= $value === '1' ? 'checked' : '' ?>>
                                <span><?= $value === '1' ? 'Activado' : 'Desactivado' ?></span>
                            </label>
                        <?php elseif ($inputType === 'color'): ?>
                            <input type="color" name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>">
                        <?php elseif ($inputType === 'number'): ?>
                            <input type="number" step="any" name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" style="width:100%; padding:0.6rem; border-radius:4px; border:1px solid #1e293b; background:#0f172a; color:#f8fafc;">
                        <?php elseif ($inputType === 'password'): ?>
                            <input type="password" name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" value="<?= $isSensitive ? '' : htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= $isSensitive ? '••••••••' : '' ?>" style="width:100%; padding:0.6rem; border-radius:4px; border:1px solid #1e293b; background:#0f172a; color:#f8fafc;">
                        <?php else: ?>
                            <input type="text" name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" style="width:100%; padding:0.6rem; border-radius:4px; border:1px solid #1e293b; background:#0f172a; color:#f8fafc;">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" style="padding:0.75rem 1.5rem; background:#2563eb; border:none; color:#fff; font-weight:600; border-radius:4px; cursor:pointer;">Guardar cambios</button>
    </form>
</section>
