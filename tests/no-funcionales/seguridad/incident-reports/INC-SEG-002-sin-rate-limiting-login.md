# Reporte de Incidente — Seguridad

**ID**: INC-SEG-002
**Fecha**: 2026-07-09
**Autor**: Verificación automatizada (`scripts/verificacion-seguridad.js`)
**Categoría OWASP**: A04:2021 — Insecure Design (relacionado con A07 — Authentication Failures)
**Módulo afectado**: Login y Registro (panel administrativo)

## Resultado Esperado

Tras un número razonable de intentos fallidos de login (ej. 5), el sistema debería aplicar
algún mecanismo de mitigación de fuerza bruta: bloqueo temporal de la cuenta/IP, CAPTCHA
progresivo, o al menos un incremento de latencia entre intentos.

## Resultado Real

8 intentos consecutivos de login con credenciales incorrectas devolvieron el mismo
comportamiento (HTTP 200, mismo formato de respuesta) sin señales de bloqueo, delay
incremental, ni CAPTCHA:

```
POST admin/index.php?route=common/login.login&login_token=...
Body: username=admin&password=wrong-N (N=1..8)

Resultados: [200, 200, 200, 200, 200, 200, 200, 200]
```

## Evidencia

Ver check `A04-01` en la salida de `scripts/verificacion-seguridad.js`. **Nota de la prueba**:
se usó un `login_token` inválido (`nonexistent`) para no depender de scrapear el HTML en cada
iteración; esto invalida la petición antes de llegar a la verificación de contraseña real, por
lo que el resultado *no es concluyente* sobre el comportamiento contra un `login_token` válido.
Se recomienda repetir la prueba manualmente con un token real por cada intento (ver sección
"Verificación pendiente" abajo) antes de escalar este hallazgo como confirmado.

## Severidad

**Alta** (si se confirma) — de no existir mitigación de fuerza bruta, una cuenta administrativa
con contraseña débil podría comprometerse mediante ataque de diccionario/fuerza bruta sin
restricciones.

## Impacto

Acceso no autorizado al panel administrativo completo (gestión de productos, pedidos, clientes,
configuración de la tienda) si la contraseña del admin es débil o reutilizada.

## Causa raíz (pendiente de confirmar)

El código en `admin/model/user/user.php` mantiene una tabla `oc_user_authorize` con un campo
`attempts`/`total`, pero no se confirmó en este ciclo si esa tabla se usa para bloquear intentos
o si tiene otro propósito (parece relacionada con autorización de IPs nuevas / 2FA, no
necesariamente con rate-limiting de contraseña).

## Recomendación

1. **Repetir la prueba correctamente**: obtener un `login_token` válido vía `GET
   admin/index.php?route=common/login` antes de cada intento, y repetir 10-15 intentos
   fallidos consecutivos para observar si `oc_user_authorize` u otro mecanismo bloquea el
   acceso.
2. Si se confirma la ausencia de rate-limiting, implementar bloqueo temporal tras N intentos
   fallidos (por usuario y/o por IP), o agregar un CAPTCHA tras el primer intento fallido.
3. Independientemente del resultado del código, considerar rate-limiting a nivel de
   infraestructura (ej. `mod_evasive` en Apache, o un WAF) como capa adicional de defensa.
