Markdown
# Módulo: Login y Registro (Versión 1)

## Descripción general

El módulo **Login y Registro** gestiona la autenticación, creación de cuentas de cliente, recuperación de contraseña y las medidas de seguridad perimetral asociadas al alta y acceso de usuarios en el sistema.

Este módulo controla las especificaciones críticas del acceso al área privada del cliente mediante la validación estricta de credenciales en base de datos, protección de formularios contra ataques Cross-Site Request Forgery (CSRF), administración y bloqueo por intentos fallidos de inicio de sesión, reglas jerárquicas de aprobación de cuentas y el restablecimiento seguro de credenciales mediante tokens de recuperación.

---

## Alcance funcional

El módulo cubre las siguientes áreas:

- Login
- Registro
- Recuperación y restablecimiento de contraseña
- Correos y alertas

---

## Requisitos funcionales

## 1. Login

- **RF-LR-001** El sistema debe mostrar una pantalla de inicio de sesión para clientes no autenticados.
- **RF-LR-002** El sistema debe permitir al cliente autenticarse ingresando su E-Mail Address y Password registrados de forma válida.
- **RF-LR-003** El sistema debe generar un login_token de 26 caracteres mediante oc_token(26) al cargar la página de login para proteger la seguridad CSRF.
- **RF-LR-004** El sistema debe rechazar el intento de login si el token en la URL no coincide con el almacenado en sesión o está ausente, redirigiendo a account/login sin procesar las credenciales.
- **RF-LR-005** El sistema debe validar los intentos fallidos de acceso por correo e IP verificando el historial en la última hora (strtotime('-1 hour')).
- **RF-LR-006** El sistema debe verificar mediante getLoginAttempts() si el número de intentos fallidos supera config_login_attempts; de ser así, muestra el mensaje 'error_attempts' y bloquea el acceso durante 1 hora.
- **RF-LR-007** El sistema debe verificar mediante getCustomerByEmail() el estado de la cuenta e impedir el acceso mostrando el mensaje 'error_approved' si el campo 'status' del cliente es false (cuenta no aprobada por el administrador) antes de verificar la contraseña.
- **RF-LR-008** El sistema debe rechazar el acceso cuando el email no existe o la contraseña es incorrecta, registrando el intento mediante addLoginAttempt() y mostrando el mensaje de error genérico 'error_login' sin especificar cuál campo falló.
- **RF-LR-009** El sistema debe eliminar los intentos fallidos previos mediante deleteLoginAttempts() tras una autenticación exitosa.
- **RF-LR-010** El sistema debe crear una sesión autenticada del cliente tras la validación exitosa mediante customer->login().
- **RF-LR-011** El sistema debe guardar en sesión los datos principales del cliente autenticado y registrar su dirección IP a través de addLogin().
- **RF-LR-012** El sistema debe generar un customer_token único de 26 caracteres en la sesión para el cliente autenticado, redirigiéndolo a la ruta account/account.
- **RF-LR-013** El sistema debe registrar la IP de login del cliente en el historial del sistema para auditorías de seguridad.
- **RF-LR-014** El sistema debe fusionar la wishlist anónima con la cuenta del cliente al iniciar sesión.
- **RF-LR-015** El sistema debe limpiar datos previos de checkout/sesión al iniciar sesión correctamente.
- **RF-LR-016** El sistema debe redirigir al destino solicitado si la URL de retorno pertenece al sitio.
- **RF-LR-017** El sistema debe redirigir a la cuenta del cliente cuando no exista un destino válido.
- **RF-LR-018** El sistema debe registrar actividad de login cuando la auditoría de actividad esté habilitada.
- **RF-LR-019** El sistema debe permitir cerrar sesión e invalidar el customer_token correspondiente.

## 2. Registro

- **RF-LR-020** El sistema debe mostrar un formulario de registro para clientes no autenticados.
- **RF-LR-021** El sistema debe impedir el registro si el cliente ya inició sesión.
- **RF-LR-022** El sistema debe generar un register_token de 26 caracteres mediante oc_token(26) al cargar el formulario para mitigar ataques CSRF.
- **RF-LR-023** El sistema debe permitir seleccionar grupo de cliente solo entre los grupos habilitados por configuración.
- **RF-LR-024** El sistema debe validar mediante oc_validate_length() que el campo First Name tenga una longitud obligatoria de entre 1 y 32 caracteres; de lo contrario, el registro es rechazado con su respectivo mensaje de error.
- **RF-LR-025** El sistema debe validar mediante oc_validate_length() que el campo Last Name tenga una longitud obligatoria de entre 1 y 32 caracteres; de lo contrario, el registro es rechazado con su respectivo mensaje de error.
- **RF-LR-026** El sistema debe verificar el formato del correo electrónico mediante oc_validate_email(), rechazando strings vacíos o estructuras sin '@' o sin dominio.
- **RF-LR-027** El sistema debe verificar mediante getTotalCustomersByEmail() que el correo electrónico no esté previamente registrado, rechazando duplicados con el mensaje de error 'error_exists' antes de intentar insertar en la base de datos.
- **RF-LR-028** El sistema debe validar teléfono cuando sea obligatorio por configuración.
- **RF-LR-029** El sistema debe soportar campos personalizados en el registro.
- **RF-LR-030** El sistema debe validar campos personalizados obligatorios.
- **RF-LR-031** El sistema debe validar regex de campos personalizados tipo texto.
- **RF-LR-032** El sistema debe validar la contraseña mediante oc_validate_length() exigiendo una longitud mínima de 6 caracteres (definida por config_password_length) y un máximo de 40 caracteres.
- **RF-LR-033** El sistema debe validar la complejidad de la contraseña requiriendo de forma configurable: mayúsculas (config_password_uppercase), minúsculas (config_password_lowercase), números (config_password_number) y símbolos (config_password_symbol), mostrando mensajes específicos por regla incumplida.
- **RF-LR-034** El sistema debe soportar captcha en registro cuando esté habilitado.
- **RF-LR-035** El sistema debe soportar subida de archivos asociados al registro.
- **RF-LR-036** El sistema debe validar el tamaño máximo de archivo permitido.
- **RF-LR-037** El sistema debe verificar que el campo 'agree' sea verdadero y exigir la aceptación obligatoria de la Privacy Policy; si el usuario no activa el toggle, el registro se bloquea con un mensaje que indica el nombre del documento de términos.
- **RF-LR-038** El sistema debe crear el cliente de forma exitosa en la base de datos asociando tienda, idioma, grupo, datos personales, campos personalizados, newsletter e IP, redirigiendo al usuario a la página de éxito (account/success).
- **RF-LR-039** El sistema debe guardar la contraseña con hash seguro e invalidar o eliminar el register_token de la sesión tras el éxito.
- **RF-LR-040** El sistema debe crear la cuenta en estado pendiente cuando el grupo requiera aprobación.
- **RF-LR-041** El sistema debe generar un registro de aprobación cuando corresponda.
- **RF-LR-042** El sistema debe registrar actividad de alta de cliente cuando la auditoría esté habilitada.

## 3. Recuperación y restablecimiento de contraseña

- **RF-LR-043** El sistema debe mostrar una pantalla de recuperación de contraseña al pulsar el enlace 'Forgotten Password' que redirige a account/forgotten.
- **RF-LR-044** El sistema debe impedir acceder a recuperación si el cliente ya está autenticado.
- **RF-LR-045** El sistema debe permitir solicitar la recuperación mediante correo electrónico.
- **RF-LR-046** El sistema debe validar que el correo exista en la base de datos antes de iniciar el proceso de recuperación.
- **RF-LR-047** El sistema debe generar un token de recuperación único de exactamente 26 caracteres asociado al correo del cliente en la base de datos.
- **RF-LR-048** El sistema debe redirigir al login tras generar correctamente la solicitud de recuperación.
- **RF-LR-049** El sistema debe registrar actividad de recuperación cuando la auditoría esté habilitada.
- **RF-LR-050** El sistema debe permitir abrir el formulario de reset mediante email y código válidos.
- **RF-LR-051** El sistema debe invalidar códigos incorrectos, vencidos o inconsistentes.
- **RF-LR-052** El sistema debe eliminar tokens inválidos cuando detecte inconsistencias.
- **RF-LR-053** El sistema debe proteger el cambio de contraseña con un token de reset de sesión.
- **RF-LR-054** El sistema debe validar longitud mínima de la nueva contraseña.
- **RF-LR-055** El sistema debe validar complejidad de la nueva contraseña según configuración.
- **RF-LR-056** El sistema debe validar que la micro-confirmación coincida con la nueva contraseña ingresada.
- **RF-LR-057** El sistema debe actualizar la contraseña del cliente cuando el reset sea válido.
- **RF-LR-058** El sistema debe eliminar el token de reset después de usarlo.
- **RF-LR-059** El sistema debe registrar actividad de cambio o reset de contraseña cuando la auditoría esté habilitada.
- **RF-LR-060** El sistema debe expirar automáticamente los tokens de cliente después de 10 minutos.

## 4. Correos y alertas

- **RF-LR-061** El sistema debe enviar un correo de bienvenida al cliente registrado cuando el motor de correo esté configurado.
- **RF-LR-062** El sistema debe incluir un enlace de login en el correo de registro.
- **RF-LR-063** El sistema debe indicar explícitamente en el correo si la cuenta requiere aprobación previa del administrador.
- **RF-LR-064** El sistema debe enviar una alerta de nuevo registro al correo principal del administrador cuando esa alerta esté habilitada.
- **RF-LR-065** El sistema debe enviar la misma alerta a correos administrativos adicionales válidos configurados.

---

## Resumen cuantitativo

- Total de requisitos del módulo: **65**
- Login: **19**
- Registro: **23**
- Recuperación y restablecimiento: **18**
- Correos y alertas: **5**

---

## Observaciones

- Este README documenta el comportamiento funcional unificado a nivel de requisitos con especificaciones técnicas detalladas y flujos de lógica perimetral.