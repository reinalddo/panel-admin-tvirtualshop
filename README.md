# Panel Master

Panel de administración centralizado para gestionar configuraciones y características de múltiples tenants desde un entorno seguro.

## Requisitos

- PHP >= 8.2
- Composer
- Servidor web (Apache o Nginx)
- MySQL 8

## Estructura inicial

- `public/`: punto de entrada web del panel.
- `src/`: código fuente de la aplicación.
- `config/`: archivos de configuración.

## Próximos pasos

1. Configurar autoloading vía Composer.
2. Implementar autenticación de super admin.
3. Integrar lectura y escritura de flags de cada tenant.
