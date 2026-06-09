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

---

## Requisitos funcionales

## 1. Registro

- **RF-01** El sistema debe permitir al usuario registrarse ingresando: First Name (1-32 caracteres), Last Name (1-32 caracteres), E-Mail con formato válido y Password. Al completar el formulario correctamente y aceptar la Privacy Policy, el sistema crea la cuenta y redirige al usuario a la página de éxito (account/success).
- **RF-02** El sistema debe validar mediante oc_validate_length() que el campo First Name y Last Name tengan entre 1 y 32 caracteres. Si alguno está vacío o supera los 32 caracteres, el registro es rechazado con mensaje de error en el campo correspondiente.
- **RF-03** El sistema debe verificar el formato del email mediante oc_validate_email(). Si el email no tiene formato válido (por ejemplo, sin '@' o sin dominio), el sistema rechaza el registro y muestra error en el campo E-Mail.
- **RF-04** El sistema debe verificar mediante getTotalCustomersByEmail() que el email no esté previamente registrado. Si ya existe un cliente con ese email, el registro es rechazado con el mensaje de error 'error_exists' sin revelar información adicional.
- **RF-05** El sistema debe validar la contraseña mediante oc_validate_length() con longitud mínima de 6 definida por config_password_length y máximo de 40 caracteres. Adicionalmente puede requerir: mayúsculas (config_password_uppercase), minúsculas (config_password_lowercase), números (config_password_number) y símbolos (config_password_symbol) según la configuración del administrador.
- **RF-06** El sistema debe verificar que el campo 'agree' sea verdadero antes de procesar el registro. Si el usuario no activa el toggle de la Privacy Policy, el registro es bloqueado con mensaje de error indicando el nombre del documento de términos.
- **RF-07** El sistema debe generar un register_token de 26 caracteres mediante oc_token(26) al cargar el formulario y lo valida al procesar el registro. Si el token no coincide o no existe en la sesión, el sistema redirige al formulario de registro sin procesar los datos.

## 2. Login

- **RF-08** El sistema debe permitir al cliente autenticarse ingresando su E-Mail Address y Password registrados. Tras la validación exitosa mediante customer->login(), el sistema crea la sesión del cliente, registra la IP con addLogin() y genera un customer_token de 26 caracteres, redirigiendo al usuario a account/account.
- **RF-09** El sistema debe rechazar el acceso cuando el email no existe o la contraseña no corresponde al email ingresado. Por cada intento fallido el sistema registra el intento con addLoginAttempt(). Se muestra el mensaje de error genérico 'error_login' sin especificar cuál campo es incorrecto.
- **RF-10** El sistema debe verificar mediante getLoginAttempts() si el número de intentos fallidos supera config_login_attempts en la última hora (strtotime('-1 hour')). Si se supera el límite, el sistema muestra el mensaje 'error_attempts' y bloquea el intento sin procesar las credenciales.
- **RF-11** El sistema debe verificar mediante getCustomerByEmail() el estado de la cuenta. Si el campo 'status' del cliente es false (cuenta no aprobada por el administrador), el sistema rechaza el login mostrando el mensaje 'error_approved' antes de verificar la contraseña.
- **RF-12** El sistema debe generar un login_token de 26 caracteres mediante oc_token(26) al cargar la página de login y lo valida al procesar el intento. Si el token en la URL no coincide con el almacenado en sesión, el sistema redirige a la página de login sin procesar las credenciales.

## 3. Recuperación y restablecimiento de contraseña

- **RF-13** El sistema debe permitir al usuario recuperar el acceso mediante el enlace 'Forgotten Password' que redirige a account/forgotten. El sistema genera un token de 26 caracteres asociado a la cuenta del usuario para el proceso de recuperación.

---

## Resumen cuantitativo

- Total de requisitos del módulo: **13**
- Registro: **7**
- Login: **5**
- Recuperación y restablecimiento: **1**

---

## Observaciones

- Este README documenta el comportamiento funcional del módulo a nivel de requisitos con especificaciones técnicas detalladas de validación y control de sesiones.
- Puede reutilizarse como base para SRS, matriz de trazabilidad, criterios de aceptación o casos de prueba.