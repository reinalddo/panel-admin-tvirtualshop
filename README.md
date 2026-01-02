# Panel Master

Panel de administración centralizado para gestionar configuraciones y características de múltiples tenants desde un entorno seguro.

## Requisitos

- PHP >= 8.2
- Composer
- Servidor web (Apache o Nginx)
- MySQL 8
- Extensión sodium habilitada en PHP

## Estructura inicial

- `index.php`: punto de entrada web del panel.
- `assets/`: recursos estáticos (css, imágenes, js).
- `src/`: código fuente de la aplicación.
- `config/`: archivos de configuración.

## Próximos pasos

1. Configurar autoloading vía Composer.
2. Generar la clave maestra con `php -r "echo base64_encode(random_bytes(32));"` y guardarla en `config/keys/master.key` (fuera de git) o en la variable `PANEL_MASTER_KEY`.
3. Implementar autenticación de super admin.
4. Integrar lectura y escritura de flags de cada tenant.
