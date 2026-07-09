# Reporte de Incidente — Usabilidad

**ID**: INC-USAB-001
**Fecha**: 2026-07-09
**Autor**: Verificación manual (petición directa a `account/register.register`)
**Heurística de Nielsen**: H5 — Prevención de errores
**Módulo afectado**: Login y Registro

## Resultado Esperado

Al registrar una cuenta nueva, si el campo `confirm` (confirmación de contraseña) no coincide
con el campo `password`, el sistema debería rechazar el registro con un mensaje claro
(ej. "Password confirmation does not match") **antes** de crear la cuenta.

## Resultado Real

El registro se completó exitosamente aunque `password` y `confirm` tenían valores distintos:

```
POST index.php?route=account/register.register&register_token=...
Body:
  firstname=Juan
  lastname=Perez
  email=juan.test@example.com
  telephone=5555555
  password=Passw0rd!
  confirm=Distinta!
  agree=1

Response:
{"redirect":"http:\/\/localhost\/QA-OpenCart-Testing\/upload\/index.php?route=account\/success&language=en-gb&customer_token=5bc262b4ac2fcb2ab573478514"}
```

La cuenta se creó con la contraseña de `password` (`Passw0rd!`), sin ninguna advertencia sobre
la discrepancia con `confirm`.

## Evidencia

Se confirmó adicionalmente que el controlador `catalog/controller/account/register.php` no
contiene ninguna referencia a `confirm` en su lógica de validación (`grep -n "confirm"` sin
resultados), lo que indica que el campo se recibe del formulario pero nunca se compara contra
`password` del lado del servidor.

## Severidad

**Alta** — es una falla de prevención de errores con consecuencia directa para el usuario: si
comete un error de tipeo en el campo de confirmación (algo común, especialmente en móvil), su
cuenta queda creada con una contraseña que él cree que es otra, y no podrá iniciar sesión hasta
usar "Forgotten Password". Esto genera fricción y posible abandono en el flujo de registro.

## Impacto

Aumenta la tasa de usuarios que no pueden loguearse tras registrarse por primera vez,
incrementando tickets de soporte y solicitudes de recuperación de contraseña evitables. No es
una vulnerabilidad de seguridad (la contraseña se guarda correctamente con el hash de
`password`), sino un defecto de experiencia de usuario.

## Causa raíz

`catalog/controller/account/register.php::register()` no implementa la comparación
`$data['password'] === $data['confirm']` antes de proceder con `addCustomer()`. Es posible que
esta validación exista solo en JavaScript del lado del cliente (validación en el navegador), lo
cual explicaría por qué no se detecta al enviar la petición HTTP directamente sin pasar por el
formulario — pero eso implica que la validación es evadible y no es una garantía real del
sistema.

## Recomendación

1. Agregar validación server-side en `register()`:
   ```php
   if (($data['password'] ?? '') !== ($data['confirm'] ?? '')) {
       $json['error']['confirm'] = $this->language->get('error_confirm');
   }
   ```
2. Verificar si existe una cadena de idioma `error_confirm` ya definida (es un patrón común en
   OpenCart) y usarla para mantener consistencia con el resto de mensajes de validación.
3. Confirmar que la validación del lado del cliente (JS) también exista como primera capa de
   feedback inmediato, pero sin depender únicamente de ella.
