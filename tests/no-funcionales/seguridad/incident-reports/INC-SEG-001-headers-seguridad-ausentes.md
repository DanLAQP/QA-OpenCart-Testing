# Reporte de Incidente — Seguridad

**ID**: INC-SEG-001
**Fecha**: 2026-07-09
**Autor**: Verificación automatizada (`scripts/verificacion-seguridad.js`)
**Categoría OWASP**: A05:2021 — Security Misconfiguration
**Módulo afectado**: Todos (respuesta HTTP global del servidor)

## Resultado Esperado

El servidor debería incluir headers de seguridad estándar en todas las respuestas HTTP:
`X-Frame-Options` (previene clickjacking), `X-Content-Type-Options: nosniff` (previene MIME
sniffing), `Content-Security-Policy` (mitiga XSS/inyección de recursos externos), y
`Strict-Transport-Security` (fuerza HTTPS en producción). Además, el header `Server` no debería
revelar la versión exacta del software subyacente.

## Resultado Real

```
GET http://localhost/QA-OpenCart-Testing/upload/index.php?route=common/home

Response headers:
Server: Apache/2.4.58 (Win64) OpenSSL/3.1.3 PHP/8.2.12
Set-Cookie: OCSESSID=...; HttpOnly; SameSite=Strict
Set-Cookie: currency=USD; ...

(ausentes: X-Frame-Options, X-Content-Type-Options, Content-Security-Policy,
Strict-Transport-Security)
```

## Evidencia

Confirmado con `curl -D -` y con el script `verificacion-seguridad.js` (checks A05-01, A05-02).

## Severidad

**Media** — no es explotable por sí solo, pero incrementa la superficie de ataque:
sin `X-Frame-Options`/CSP, el sitio es vulnerable a clickjacking; sin `X-Content-Type-Options`,
un navegador podría malinterpretar el tipo de un recurso subido; el header `Server` detallado
facilita a un atacante identificar CVEs conocidos para esa versión exacta de Apache/PHP/OpenSSL.

## Impacto

Bajo en aislamiento, pero combinado con otras vulnerabilidades (ej. una futura XSS) reduce las
capas de defensa disponibles. La exposición de versiones facilita reconnaissance dirigido.

## Causa raíz

Configuración por defecto de Apache/XAMPP y de OpenCart: ninguno de los dos agrega estos
headers de forma nativa. No es un bug del código de OpenCart, sino una configuración pendiente
de endurecimiento (*hardening*) del servidor.

## Recomendación

1. Agregar en `.htaccess` (o en la config de VirtualHost en producción):
   ```apache
   Header always set X-Frame-Options "SAMEORIGIN"
   Header always set X-Content-Type-Options "nosniff"
   Header always set Content-Security-Policy "default-src 'self'"
   Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
   ```
2. Ocultar la versión del servidor en `httpd.conf`: `ServerTokens Prod` y `ServerSignature Off`.
3. Ocultar la versión de PHP: `expose_php = Off` en `php.ini`.
