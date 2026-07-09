# Reporte de Incidente — Seguridad

**ID**: INC-SEG-003
**Fecha**: 2026-07-09
**Autor**: Verificación automatizada (`scripts/verificacion-seguridad.js`)
**Categoría OWASP**: A05:2021 — Security Misconfiguration
**Módulo afectado**: Todos (infraestructura — carpeta `system/storage/`)

## Resultado Esperado

Las carpetas internas del sistema (`system/`, `system/storage/`, y sus subcarpetas `backup/`,
`logs/`, `session/`, `upload/`, `download/`) no deberían ser navegables por HTTP. Un `GET`
directo a esas rutas debería devolver 403 Forbidden o 404 Not Found.

## Resultado Real

```
GET http://localhost/QA-OpenCart-Testing/upload/system/storage/
→ HTTP 200

<h1>Index of /QA-OpenCart-Testing/upload/system/storage</h1>
  ...
  [DIR] backup/
  [DIR] cache/
  [DIR] download/
  [DIR] logs/
  [DIR] marketplace/
  [DIR] modification/
  [DIR] session/
  [DIR] upload/
  [DIR] vendor/

GET http://localhost/QA-OpenCart-Testing/upload/system/
→ HTTP 200 (mismo comportamiento, listado de directorio)
```

## Evidencia

Confirmado con `curl` directo y con el check `A05-03` de `scripts/verificacion-seguridad.js`.
Nota: `admin/` sí devuelve 200 pero corresponde a la página de login real (comportamiento
esperado), no a un listado de directorio — se diferencia correctamente en el script por la
ausencia del patrón `Index of /`.

## Severidad

**Alta** — la carpeta `session/` puede contener archivos de sesión con tokens activos, `logs/`
puede exponer rutas internas o datos sensibles en mensajes de error, y `backup/` puede contener
volcados completos de la base de datos si el administrador alguna vez generó un backup desde el
panel.

## Impacto

Un atacante que enumere `system/storage/backup/` podría descargar un volcado SQL completo de
la tienda (clientes, pedidos, hashes de contraseña) si existe algún backup generado. La carpeta
`logs/` podría revelar rutas absolutas del servidor o trazas de errores con información interna.

## Causa raíz

Directory listing habilitado por configuración por defecto de Apache/XAMPP (`Options +Indexes`
en la configuración global o ausencia de un `.htaccess` con `Options -Indexes` en esas rutas).
OpenCart normalmente incluye archivos `index.html` vacíos en `storage/` para bloquear el
listado (se observó uno en `opencart/system/storage/cache/index.html` en la otra copia del
proyecto), pero la instalación en `upload/` no los tiene o Apache no los está honrando.

## Recomendación

1. Agregar `Options -Indexes` en el `.htaccess` raíz del proyecto (`upload/.htaccess`) y/o en
   la configuración del `VirtualHost`/`Directory` de Apache para `system/storage/`.
2. Verificar que existan archivos `index.html` vacíos (o un `.htaccess` con `deny from all`)
   dentro de cada subcarpeta de `system/storage/` como defensa en profundidad.
3. En producción, mover `system/storage/` fuera del webroot público si la estructura de
   despliegue lo permite (recomendación oficial de OpenCart para instalaciones nuevas).
