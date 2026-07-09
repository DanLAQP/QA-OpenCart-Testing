# Script de Verificación de Seguridad

`verificacion-seguridad.js` automatiza un subconjunto de los checks del
[`checklist-owasp.md`](../checklist-owasp.md) — específicamente los que se pueden verificar de
forma determinística vía HTTP, sin necesitar sesiones de cliente autenticadas ni datos previos.

## Requisitos

Solo Node.js (usa el módulo `http` nativo, sin dependencias de npm).

## Ejecución

```bash
cd tests/no-funcionales/seguridad/scripts
node verificacion-seguridad.js
```

Variables configurables:

```bash
BASE_URL=http://localhost/QA-OpenCart-Testing/upload node verificacion-seguridad.js
```

## Qué cubre

- **A05-01/A05-02**: presencia de headers de seguridad HTTP y exposición de versión del servidor.
- **A02-02**: flags `HttpOnly`/`SameSite` en la cookie de sesión.
- **A05-03**: directory listing en carpetas sensibles (`system/storage/`, `system/`, `.git/`, `admin/`).
- **A03-01**: inyección SQL básica sobre `product_id` (no expone errores de BD).
- **A03-02**: XSS reflejado básico sobre el parámetro `search`.
- **A01-01**: acceso a `catalog/product.list` sin `user_token` no expone datos.
- **A05-05**: manejo de errores no expone stack traces de PHP.
- **A04-01**: comportamiento del login admin ante intentos fallidos repetidos (marcado como
  `WARN`, no `FAIL`/`PASS`, porque requiere verificación manual adicional — ver
  [INC-SEG-002](../incident-reports/INC-SEG-002-sin-rate-limiting-login.md)).

## Qué NO cubre (queda como verificación manual, marcado ⏳ en el checklist)

- IDOR sobre `checkout/cart.edit` o `account/order.info` (requiere dos sesiones/cuentas distintas).
- XSS almacenado en el campo `text`/`author` de una reseña (requiere aprobar la reseña en admin y ver el render).
- Complejidad mínima de contraseña en registro.
- Invalidación de sesión tras logout / session fixation.
- `composer audit` de dependencias con CVEs conocidos.

## Salida

El script imprime `PASS` (verde), `WARN` (amarillo) o `FAIL` (rojo) por cada check, y termina
con un resumen. Sale con código `1` si hay algún `FAIL`, útil para integrarlo en CI.
