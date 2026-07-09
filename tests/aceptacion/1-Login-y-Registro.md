# Pruebas de Aceptación: Login y Registro

Trazable a [`docs/Requisitos-funcionales/Login_registro.md`](../../docs/Requisitos-funcionales/Login_registro.md).

---

## Historia de Usuario 1: Iniciar sesión con credenciales válidas

**Como** cliente registrado,
**quiero** iniciar sesión con mi correo y contraseña,
**para** acceder a mi cuenta y continuar una compra.

**Prioridad**: Alta
**Requisitos relacionados**: RF-LR-001, RF-LR-002, RF-LR-010, RF-LR-012

```gherkin
Escenario: Login exitoso con credenciales correctas
  Dado que tengo una cuenta registrada con correo y contraseña válidos
  Cuando ingreso mi correo y contraseña correctos en la pantalla de login
  Entonces el sistema me autentica y me redirige a mi cuenta
  Y puedo ver mis datos personales en la sección "Mi Cuenta"

Escenario: Rechazo de credenciales incorrectas sin revelar cuál campo falló
  Dado que intento iniciar sesión con una contraseña incorrecta
  Cuando envío el formulario de login
  Entonces el sistema muestra un mensaje de error genérico
  Y el mensaje no indica si el correo o la contraseña fue el campo incorrecto
```

**Criterio de aceptación**: el mensaje de error debe ser genérico (no debe distinguir "correo no existe" de "contraseña incorrecta"), para no facilitar a un atacante enumerar cuentas válidas.

---

## Historia de Usuario 2: Bloqueo tras múltiples intentos fallidos

**Como** administrador del sitio,
**quiero** que se bloquee temporalmente el acceso tras varios intentos fallidos de login,
**para** proteger las cuentas de mis clientes contra ataques de fuerza bruta.

**Prioridad**: Alta
**Requisitos relacionados**: RF-LR-005, RF-LR-006, RF-LR-009

```gherkin
Escenario: Bloqueo temporal tras exceder el límite de intentos fallidos
  Dado que un usuario ha fallado el login más veces de lo permitido por la configuración
  Cuando intenta iniciar sesión nuevamente dentro de la última hora
  Entonces el sistema muestra un mensaje indicando que el acceso está bloqueado temporalmente
  Y no permite continuar aunque las credenciales sean correctas

Escenario: Los intentos fallidos se olvidan tras un login exitoso
  Dado que un usuario tuvo intentos fallidos previos
  Cuando finalmente inicia sesión correctamente
  Entonces el historial de intentos fallidos se limpia
  Y un futuro intento fallido no cuenta los intentos anteriores
```

**Estado de este criterio**: ⚠️ **Verificado parcialmente** — durante las pruebas no
funcionales de seguridad ([INC-SEG-002](../no-funcionales/seguridad/incident-reports/INC-SEG-002-sin-rate-limiting-login.md))
se observó que 8 intentos fallidos consecutivos contra el **login de administrador** no
mostraron bloqueo perceptible. Ese hallazgo usó un `login_token` inválido en cada intento, lo
cual no es representativo del flujo real de un atacante (que sí obtendría un token válido cada
vez). Se recomienda repetir la prueba con tokens válidos sobre el login de **cliente**
(`account/login`, distinto del login de admin) antes de dar este criterio por incumplido.

---

## Historia de Usuario 3: Registrar una cuenta nueva con datos válidos

**Como** visitante de la tienda,
**quiero** crear una cuenta con mis datos personales,
**para** poder realizar compras y hacer seguimiento de mis pedidos.

**Prioridad**: Alta
**Requisitos relacionados**: RF-LR-024, RF-LR-025, RF-LR-026, RF-LR-032, RF-LR-037, RF-LR-038

```gherkin
Escenario: Registro exitoso con todos los datos válidos
  Dado que completo el formulario de registro con nombre, apellido, correo y contraseña válidos
  Y acepto la Política de Privacidad
  Cuando envío el formulario
  Entonces mi cuenta se crea exitosamente
  Y soy redirigido a una página de confirmación de éxito

Escenario: Rechazo por no aceptar la Política de Privacidad
  Dado que completo el formulario de registro correctamente
  Pero no marco la casilla de aceptación de la Política de Privacidad
  Cuando envío el formulario
  Entonces el sistema rechaza el registro
  Y me indica que debo aceptar el documento de términos para continuar

Escenario: Rechazo por correo ya registrado
  Dado que el correo que quiero usar ya pertenece a otra cuenta
  Cuando envío el formulario de registro con ese correo
  Entonces el sistema rechaza el registro
  Y me informa que ese correo ya está en uso
```

**Verificado**: ✅ confirmado directamente contra `upload/` — el registro exitoso redirige
correctamente a `account/success`, y la validación de campos (nombre, correo, contraseña)
devuelve mensajes específicos por campo.

---

## Historia de Usuario 4: Confirmación de contraseña al registrarse

**Como** usuario que se registra,
**quiero** que el sistema verifique que escribí mi contraseña correctamente dos veces,
**para** no quedar con una contraseña distinta a la que creo haber puesto por un error de tipeo.

**Prioridad**: Alta
**Requisitos relacionados**: RF-LR-032 (longitud), *implícito en el formulario: confirmación de contraseña*

```gherkin
Escenario: Rechazo cuando la confirmación de contraseña no coincide
  Dado que completo el formulario de registro
  Y escribo una contraseña distinta en el campo de confirmación
  Cuando envío el formulario
  Entonces el sistema debe rechazar el registro
  Y debe indicarme que las contraseñas no coinciden
```

**Estado de este criterio**: 🔴 **No cumple** — ver
[INC-ACEPT-001](incident-reports/INC-ACEPT-001-registro-sin-confirmar-password.md).
Se verificó directamente contra `upload/` que el registro se completa exitosamente aunque
`password` y `confirm` tengan valores distintos, sin ninguna advertencia. Este hallazgo ya
había sido identificado desde la perspectiva de usabilidad
([INC-USAB-001](../no-funcionales/usabilidad/incident-reports/INC-USAB-001-sin-validacion-confirmar-password.md));
aquí se documenta además como un criterio de aceptación de negocio incumplido, porque afecta
directamente la capacidad del cliente de acceder a su propia cuenta después de registrarse.

---

## Historia de Usuario 5: Recuperar contraseña olvidada

**Como** cliente que olvidó su contraseña,
**quiero** solicitar un enlace de recuperación a mi correo,
**para** poder restablecer mi acceso sin necesidad de crear una cuenta nueva.

**Prioridad**: Media
**Requisitos relacionados**: RF-LR-043 a RF-LR-060

```gherkin
Escenario: Solicitar recuperación con un correo registrado
  Dado que tengo una cuenta con correo "cliente@example.com"
  Cuando solicito la recuperación de contraseña con ese correo
  Entonces el sistema genera un enlace de recuperación válido
  Y me redirige a la pantalla de login con una confirmación

Escenario: El token de recuperación expira
  Dado que solicité un enlace de recuperación hace más de 10 minutos
  Cuando intento usar ese enlace para cambiar mi contraseña
  Entonces el sistema rechaza el token por estar expirado
  Y me pide solicitar un nuevo enlace de recuperación
```

**Estado de este criterio**: ⏳ Pendiente de verificación manual (requiere acceso a un
servidor de correo configurado para confirmar el envío real del enlace).

---

## Registro de Ejecución

| Historia | Prioridad | Resultado | Evidencia / Incidente |
|---|---|---|---|
| 1. Login con credenciales válidas | Alta | ✅ Cumple | Verificado contra `upload/` |
| 2. Bloqueo tras intentos fallidos | Alta | ⚠️ Verificación parcial | [INC-SEG-002](../no-funcionales/seguridad/incident-reports/INC-SEG-002-sin-rate-limiting-login.md) |
| 3. Registro con datos válidos | Alta | ✅ Cumple | Verificado contra `upload/` |
| 4. Confirmación de contraseña | Alta | 🔴 No cumple | [INC-ACEPT-001](incident-reports/INC-ACEPT-001-registro-sin-confirmar-password.md) |
| 5. Recuperar contraseña olvidada | Media | ⏳ Pendiente | Requiere servidor de correo |
