# Checklist de Seguridad — OWASP Top 10 (2021) aplicado a los 6 módulos

Evaluación manual + automatizada de seguridad sobre OpenCart (`upload/`), cubriendo los 6
módulos funcionales del proyecto. No reemplaza un pentest profesional — es una primera pasada
de verificación básica orientada a hallazgos rápidos y de alto impacto.

**Ambiente evaluado**: `http://localhost/QA-OpenCart-Testing/upload` (instalación local XAMPP)
**Fecha**: 2026-07-09
**Alcance**: Login/Registro, Catálogo/Búsqueda, Carrito, Checkout, Gestión de Inventario, Reseñas

---

## A01:2021 — Broken Access Control

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A01-01 | Acceder a `admin/index.php?route=catalog/product.list` sin `user_token` | Inventario (admin) | ✅ Pass — devuelve la vista de login, no la data | `curl` sin sesión responde con HTML de `common/login`, no expone el listado |
| A01-02 | Acceder a rutas `api/*` sin autenticación (aparte de `api/order`/`api/subscription`) | Inventario (API) | ✅ Pass (ver hallazgo previo en `tests/integracion/`) | `catalog/controller/startup/api.php` bloquea con 403 |
| A01-03 | Modificar `product_id` ajeno en `checkout/cart.edit` (IDOR sobre `key` de otro carrito) | Carrito | ⏳ Pendiente de prueba | Requiere dos sesiones distintas; ver `scripts/verificacion-seguridad.js` |
| A01-04 | Ver pedido de otro cliente cambiando `order_id` en `account/order.info` | Checkout | ⏳ Pendiente de prueba | Requiere dos cuentas registradas |

## A02:2021 — Cryptographic Failures

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A02-01 | Contraseña de usuario almacenada con hash fuerte (no MD5/SHA1 plano) | Login/Registro | ✅ Pass | `oc_user.password` usa `password_hash()` (bcrypt), confirmado en `admin/model/user/user.php` |
| A02-02 | Cookie de sesión con flags `HttpOnly` / `SameSite` | Login/Registro | ✅ Pass | `Set-Cookie: OCSESSID=...; HttpOnly; SameSite=Strict` |
| A02-03 | Sitio servido solo por HTTP en local (sin HTTPS) | Todos | ⚠️ Esperado en local | No aplica corregir en ambiente de pruebas; **debe forzarse HTTPS en producción** (`Strict-Transport-Security` ausente) |

## A03:2021 — Injection

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A03-01 | SQL Injection en `product_id` (`' OR '1'='1`) | Catálogo | ✅ Pass | Responde 200 normal / 404 controlado, sin error SQL expuesto ni bypass de datos |
| A03-02 | XSS reflejado en parámetro `search` (`<script>alert(1)</script>`) | Catálogo/Búsqueda | ✅ Pass | El payload no se refleja sin escapar en la respuesta |
| A03-03 | XSS almacenado vía campo `text`/`author` de una reseña | Reseñas | ⏳ Pendiente de prueba | Enviar reseña con `<script>` en `text` y verificar cómo se renderiza tras aprobación |
| A03-04 | Inyección en campos de registro (`firstname`, `lastname`) | Login/Registro | ⏳ Pendiente de prueba | Ver `scripts/verificacion-seguridad.js` |

## A04:2021 — Insecure Design

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A04-01 | Límite de intentos fallidos de login (rate limiting / bloqueo) | Login/Registro | ⚠️ Hallazgo — ver INC-SEG-002 | 10 intentos fallidos consecutivos no mostraron bloqueo ni delay perceptible |
| A04-02 | Cantidad mínima/máxima de compra no puede evadirse manipulando el POST directo | Inventario/Carrito | ✅ Pass | Ya validado en pruebas de sistema/funcionales (RF-INV-005, RF-INV-017) |

## A05:2021 — Security Misconfiguration

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A05-01 | Header `Server` no debería exponer versiones exactas | Todos | ⚠️ Hallazgo — ver INC-SEG-001 | `Server: Apache/2.4.58 (Win64) OpenSSL/3.1.3 PHP/8.2.12` |
| A05-02 | Headers de seguridad (`X-Frame-Options`, `X-Content-Type-Options`, `Content-Security-Policy`) | Todos | ⚠️ Hallazgo — ver INC-SEG-001 | Ninguno presente en la respuesta de `common/home` |
| A05-03 | Directory listing deshabilitado en carpetas sensibles (`system/storage/`) | Todos | ⚠️ Hallazgo — ver INC-SEG-003 | `GET /upload/system/storage/` devuelve **200** con listado completo de subcarpetas (backup, logs, session, upload) |
| A05-04 | Archivos de configuración (`config.php`) no accesibles ni descargables por HTTP | Todos | ✅ Pass | PHP se ejecuta en vez de descargarse; no expone contenido en texto plano |
| A05-05 | Mensajes de error no exponen rutas de servidor ni stack traces al usuario final | Todos | ✅ Pass (en `upload/` ya instalado) | Rutas inválidas devuelven 404 controlado, no un stack trace PHP |

## A06:2021 — Vulnerable and Outdated Components

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A06-01 | Versión de PHP soportada (no EOL) | Todos | ✅ Pass | PHP 8.2.12, dentro de soporte activo |
| A06-02 | Dependencias de Composer sin CVEs conocidos | Todos | ⏳ Pendiente | Ejecutar `composer audit` desde la raíz del repo |

## A07:2021 — Identification and Authentication Failures

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A07-01 | Requisitos mínimos de complejidad de contraseña en registro | Login/Registro | ⏳ Pendiente de prueba | Ver `config_password_length`; probar registro con contraseña de 1 carácter |
| A07-02 | Token de sesión (`OCSESSID`) cambia tras login exitoso (previene session fixation) | Login/Registro | ⏳ Pendiente de prueba | Comparar cookie antes/después de autenticar |
| A07-03 | Cierre de sesión invalida el token del lado servidor | Login/Registro | ⏳ Pendiente de prueba | Verificar que la misma cookie no funcione tras logout |

## A08:2021 — Software and Data Integrity Failures

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A08-01 | Firma HMAC en llamadas API previene manipulación de payload | Inventario (API) | ✅ Pass | Ya documentado en `tests/integracion/README.md` (firma HMAC-SHA1 sobre la request) |

## A09:2021 — Security Logging and Monitoring Failures

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A09-01 | Intentos fallidos de login quedan registrados (`oc_user_authorize` o logs) | Login/Registro | ⏳ Pendiente de prueba | Revisar tabla `oc_user_authorize` tras intentos fallidos |

## A10:2021 — Server-Side Request Forgery (SSRF)

| # | Verificación | Módulo | Resultado | Evidencia |
|---|---|---|---|---|
| A10-01 | No hay endpoints que acepten URLs externas para fetch server-side (imports, webhooks) | Todos | ✅ Pass (no aplica) | Los 6 módulos evaluados no exponen funcionalidad de fetch de URL arbitraria al usuario final |

---

## Resumen de hallazgos

| ID | Severidad | Resumen |
|---|---|---|
| [INC-SEG-001](incident-reports/INC-SEG-001-headers-seguridad-ausentes.md) | Media | Faltan headers de seguridad HTTP (`X-Frame-Options`, `CSP`, etc.) y el header `Server` expone versiones exactas |
| [INC-SEG-002](incident-reports/INC-SEG-002-sin-rate-limiting-login.md) | Alta | No se observó bloqueo ni retraso tras múltiples intentos fallidos de login admin |
| [INC-SEG-003](incident-reports/INC-SEG-003-directory-listing-storage.md) | Alta | Directory listing habilitado en `system/storage/`, exponiendo backups, logs y sesiones |

## Cómo ejecutar la verificación automatizada

```bash
cd tests/no-funcionales/seguridad/scripts
node verificacion-seguridad.js
```

Ver [`scripts/README.md`](scripts/README.md) para detalle de qué automatiza el script y qué
queda pendiente de verificación manual (marcado como ⏳ en las tablas de arriba).
