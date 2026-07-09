# Reporte de Incidente — Aceptación

**ID**: INC-ACEPT-001
**Fecha**: 2026-07-09
**Autor**: Verificación manual (petición directa a `account/register.register`)
**Módulo**: Login y Registro
**Historia de usuario**: [Historia 4 — Confirmación de contraseña al registrarse](../1-Login-y-Registro.md#historia-de-usuario-4-confirmación-de-contraseña-al-registrarse)
**Criterio de aceptación incumplido**: "El sistema debe rechazar el registro si la contraseña y su confirmación no coinciden"

## Resultado Esperado (criterio de aceptación)

Como usuario final, si escribo mi contraseña de forma distinta en el campo "Confirmar
contraseña" (por un error de tipeo, algo común especialmente en móvil), el sistema debe
avisarme claramente antes de crear la cuenta, para que pueda corregirlo.

## Resultado Real

Se registró una cuenta con `password="Passw0rd!"` y `confirm="Distinta!"` (valores distintos)
directamente contra `upload/`, y el sistema la creó exitosamente sin ninguna advertencia,
usando la contraseña del campo `password`.

Desde la perspectiva de un usuario final: si el usuario cree que su contraseña es la que
escribió en el campo de confirmación (por ejemplo, si corrigió un error de tipeo justo ahí sin
darse cuenta de que el primer campo quedó con el valor viejo), **quedará con una cuenta cuya
contraseña real no es la que él espera**, y no podrá iniciar sesión hasta usar "Forgotten
Password".

## Evidencia

Ver detalle técnico completo en
[INC-USAB-001](../../no-funcionales/usabilidad/incident-reports/INC-USAB-001-sin-validacion-confirmar-password.md),
donde se documentó originalmente este hallazgo desde la perspectiva de usabilidad.

## Severidad

**Alta** — afecta directamente la capacidad de un cliente nuevo de acceder a la cuenta que
acaba de crear.

## Impacto en el negocio

Cada registro afectado por este problema se traduce en un cliente que no puede iniciar sesión
en su primer intento, generando frustración, posible abandono del sitio, y tickets de soporte
evitables ("no puedo entrar a mi cuenta recién creada"). En un negocio de e-commerce, la
primera impresión del proceso de registro es crítica para la retención.

## Causa raíz

Ver sección "Causa raíz" de
[INC-USAB-001](../../no-funcionales/usabilidad/incident-reports/INC-USAB-001-sin-validacion-confirmar-password.md):
`catalog/controller/account/register.php::register()` no compara `$data['password']` contra
`$data['confirm']` del lado del servidor.

## Recomendación

Igual que en INC-USAB-001: agregar la validación server-side
`$data['password'] !== $data['confirm']` antes de crear la cuenta, devolviendo un mensaje de
error claro asociado al campo de confirmación.
