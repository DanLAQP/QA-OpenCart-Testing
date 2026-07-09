# Reporte de Incidente — Disponibilidad

**ID**: INC-DISP-001
**Fecha**: 2026-07-09
**Autor**: Verificación manual (detención controlada de MySQL en XAMPP local)
**Módulo afectado**: Todos (tienda, admin, API) — depende de la conexión a base de datos

## Resultado Esperado

Cuando la base de datos no está disponible, el sitio debería:
1. Responder con un código HTTP apropiado (**503 Service Unavailable**), no 200.
2. Mostrar una página de error genérica y amigable ("Estamos en mantenimiento, vuelve pronto"),
   sin detalles técnicos internos.
3. No exponer rutas absolutas del servidor, nombres de clases, ni números de línea del código.
4. Idealmente, registrar el evento en un log para alertar a un operador (relacionado con
   OWASP A09 — Security Logging and Monitoring Failures).

## Resultado Real

Con MySQL detenido, cualquier ruta (`common/home`, `product/product`,
`admin/index.php?route=common/login`, `api/order&call=cart`) devuelve:

```
HTTP 200
Tiempo de respuesta: ~2.05s (consistente en 3 intentos: 2.03s / 2.06s / 2.04s)

Error: Error: Could not make a database link using root@127.0.0.1!
Message: No se puede establecer una conexión ya que el equipo de destino denegó
expresamente dicha conexión
File: C:\xampp\htdocs\QA-OpenCart-Testing\upload\system\library\db\mysqli.php
Line: 86

Backtrace: 0
File: C:\xampp\htdocs\QA-OpenCart-Testing\upload\system\library\db.php
Line: 40
Class: Opencart\System\Library\DB\MySQLi
Function: __construct

Backtrace: 1
File: C:\xampp\htdocs\QA-OpenCart-Testing\upload\system\framework.php
Line: 151
Class: Opencart\System\Library\DB
Function: __construct

Backtrace: 2
File: C:\xampp\htdocs\QA-OpenCart-Testing\upload\index.php
...
```

## Evidencia

Capturado con `curl -w "%{http_code} %{time_total}"` contra 4 rutas distintas mientras MySQL
estaba detenido manualmente vía XAMPP Control Panel. Ver tabla completa en
[`../checklist-disponibilidad.md`](../checklist-disponibilidad.md).

## Severidad

**Alta** — combina dos problemas:
- **Disponibilidad**: un balanceador de carga, monitor de uptime, o CDN no puede distinguir
  esta respuesta de una página exitosa (ambas son HTTP 200), por lo que no failovea ni alerta
  correctamente ante una caída real de base de datos en producción.
- **Seguridad** (relacionado con OWASP A05): expone rutas absolutas del servidor
  (`C:\xampp\htdocs\...`), estructura interna de clases y archivos del framework, y detalles
  del driver de base de datos — información valiosa para un atacante en reconnaissance.

## Impacto

En producción, una caída de base de datos (mantenimiento, fallo de red, sobrecarga) dejaría el
sitio "funcionando" según cualquier health-check basado en código HTTP, mientras en realidad
ningún usuario puede completar ninguna operación. Esto retrasa la detección del incidente y la
respuesta del equipo de operaciones. Adicionalmente, cada request colgado ~2s mientras se
reintenta la conexión a MySQL puede agotar más rápido el pool de workers de PHP/Apache bajo
tráfico concurrente durante el incidente, agravando la caída en vez de degradarse con gracia.

## Causa raíz

`system/library/db.php:40` no captura la excepción de conexión fallida del driver
(`system/library/db/mysqli.php:86`) para convertirla en una respuesta HTTP controlada; el
framework deja que el error se propague y lo imprime directamente (aparenta estar en modo
debug/desarrollo, con `display_errors` efectivamente activo a este nivel).

## Recomendación

1. Envolver la construcción de la conexión a BD (`system/framework.php:151` y
   `system/library/db.php:40`) en un `try/catch` que, ante fallo, devuelva una respuesta HTTP
   503 con una vista de mantenimiento genérica (sin detalles del stack trace).
2. Verificar que `display_errors` esté deshabilitado en el `php.ini` usado por el entorno de
   producción (en local es aceptable para depuración, pero debe confirmarse que no ocurra igual
   fuera de XAMPP).
3. Registrar el evento de fallo de conexión en `system/storage/logs/` (o un sistema de
   monitoreo externo) para que un operador reciba alerta inmediata, en vez de depender de que
   un usuario reporte el sitio "raro".
4. Considerar un timeout de conexión a BD más corto (actualmente ~2s por intento) combinado con
   un circuit breaker, para no degradar la capacidad de respuesta del servidor bajo tráfico
   concurrente durante una caída prolongada de la base de datos.
