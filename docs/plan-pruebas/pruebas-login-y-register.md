# Pruebas: Login y Registro

## Descripción

Este documento contiene una propuesta de **36 casos de prueba** para el módulo **Login**, diseñados con las técnicas de:

- **PE**: Partición de Equivalencia
- **AVL**: Análisis de Valores Límite

Se presentan en formato tabular para facilitar su uso en documentación QA, pruebas funcionales, validación académica o preparación de casos en herramientas de testing.

---

## 1. Login (RF-LR-001 al RF-LR-019)

| ID | Requisito(s) | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|---|
| CP-L-001 | RF-LR-001 | Pantalla de login | Visualizar página de login sin autenticación | Usuario no autenticado accede a `account/login` | PE | Se muestra la página con campos E-Mail, Password, enlace a registro y enlace a Forgotten Password. |
| CP-L-002 | RF-LR-001 | Pantalla de login | Usuario ya autenticado intenta acceder al login | Usuario con sesión activa y `customer_token` válido accede a `account/login` | PE | El sistema redirige al usuario a `account/account` sin mostrar el formulario. |
| CP-L-003 | RF-LR-002 | Autenticación | Login exitoso con credenciales válidas | `email='cliente@test.com'`, `password='Pass1234'` | PE | Autenticación exitosa, sesión creada, redirige a `account/account` con `customer_token`. |
| CP-L-004 | RF-LR-002 | Autenticación | Login con contraseña incorrecta | `email='cliente@test.com'`, `password='incorrecta'` | PE | Se muestra mensaje `error_login` genérico sin especificar qué campo falló. |
| CP-L-005 | RF-LR-002 | Autenticación | Login con email no registrado | `email='noexiste@test.com'`, `password='Pass1234'` | PE | Se muestra mensaje `error_login`, se registra intento fallido. |
| CP-L-006 | RF-LR-002 | Autenticación | Login con contraseña vacía | `email='cliente@test.com'`, `password=''` | AVL | El sistema rechaza el intento y muestra mensaje de error de campo requerido. |
| CP-L-007 | RF-LR-002 | Autenticación | Login con email vacío | `email=''`, `password='Pass1234'` | AVL | El sistema rechaza el intento y muestra mensaje de error de campo requerido. |
| CP-L-008 | RF-LR-002 | Autenticación | Login con ambos campos vacíos | `email=''`, `password=''` | AVL | El sistema rechaza el intento y muestra mensajes de error en ambos campos. |
| CP-L-009 | RF-LR-003 | Token CSRF | Verificar generación de `login_token` al cargar página | GET a `account/login` | PE | Se genera `login_token` de exactamente 26 caracteres en sesión. |
| CP-L-010 | RF-LR-003 | Token CSRF | Verificar longitud exacta del `login_token` | Token generado por `oc_token(26)` | AVL | El token tiene exactamente 26 caracteres hexadecimales `[0-9a-f]`. |
| CP-L-011 | RF-LR-004 | Token CSRF | Login sin `login_token` en la URL | POST a login sin parámetro `login_token` | PE | Sistema rechaza el intento y redirige a `account/login` sin procesar credenciales. |
| CP-L-012 | RF-LR-004 | Token CSRF | Login con `login_token` incorrecto | POST con `login_token='tokenfalso123'` | PE | Sistema rechaza el intento y redirige a `account/login`. |
| CP-L-013 | RF-LR-005 | Intentos fallidos | Registrar intento fallido con email+IP | `email='test@test.com'`, password incorrecta, `IP=192.168.1.1` | PE | Se inserta o actualiza registro en `customer_login` con email, IP y total incrementado. |
| CP-L-014 | RF-LR-005 | Intentos fallidos | Intentos desde diferente IP se registran separados | Mismo email, `IP=192.168.1.2` diferente | PE | Se crea registro separado en `customer_login` para la nueva IP. |
| CP-L-015 | RF-LR-006 | Bloqueo de cuenta | Bloqueo al alcanzar el límite de intentos | `config_login_attempts=5`, total=5 intentos fallidos en la última hora | AVL | Se muestra mensaje `error_attempts`, el login es bloqueado. |
| CP-L-016 | RF-LR-006 | Bloqueo de cuenta | Sin bloqueo con intentos por debajo del límite | total=4 intentos fallidos (límite-1) | AVL | El sistema no bloquea, permite intentar login. |
| CP-L-017 | RF-LR-006 | Bloqueo de cuenta | Bloqueo no aplica si los intentos son de más de 1 hora | total=5 intentos, `date_modified` hace 61 minutos | AVL | El sistema NO aplica bloqueo, `strtotime('-1 hour') > strtotime(date_modified)`. |
| CP-L-018 | RF-LR-006 | Bloqueo de cuenta | Bloqueo aplica con intentos exactamente en el límite de 1 hora | total=5 intentos, `date_modified` hace exactamente 60 min | AVL | El sistema aplica bloqueo, el intento está dentro de la ventana de 1 hora. |
| CP-L-019 | RF-LR-007 | Estado de cuenta | Login rechazado por cuenta no aprobada | Cliente con `status=false` intenta login con credenciales correctas | PE | Se muestra mensaje `error_approved` antes de verificar la contraseña. |
| CP-L-020 | RF-LR-007 | Estado de cuenta | Login exitoso con cuenta activa | Cliente con `status=true`, credenciales correctas | PE | Login exitoso, sesión creada correctamente. |
| CP-L-021 | RF-LR-008 | Credenciales incorrectas | Contraseña incorrecta incrementa contador | Email registrado, `password='incorrecta'` | PE | `addLoginAttempt()` es invocado, total en `customer_login` incrementa en 1. |
| CP-L-022 | RF-LR-009 | Limpieza de intentos | Login exitoso elimina intentos previos | 3 intentos fallidos previos, luego login correcto | PE | `deleteLoginAttempts()` elimina todos los registros del email en `customer_login`. |
| CP-L-023 | RF-LR-010 | Sesión | Creación de sesión tras login exitoso | Login con credenciales válidas | PE | Session data contiene `customer_id`, `customer_group_id`, `firstname`, `lastname`, `email`, `telephone`, `custom_field`. |
| CP-L-024 | RF-LR-011 | Sesión | Datos del cliente guardados en sesión | Login exitoso | PE | Los datos del cliente en sesión coinciden exactamente con los datos de la BD. |
| CP-L-025 | RF-LR-012 | customer_token | Generación de `customer_token` tras login | Login exitoso | PE | Se genera `customer_token` de exactamente 26 caracteres en sesión. |
| CP-L-026 | RF-LR-012 | customer_token | Longitud exacta del `customer_token` | `customer_token` generado con `oc_token(26)` | AVL | El token tiene exactamente 26 caracteres. |
| CP-L-027 | RF-LR-013 | Registro de IP | IP registrada tras login exitoso | Login exitoso desde `IP=10.0.0.1` | PE | `addLogin()` inserta registro en `customer_ip` con `customer_id` e IP correctos. |
| CP-L-028 | RF-LR-014 | Wishlist | Fusión de wishlist anónima al hacer login | Sesión con `wishlist=[product_id=5]`, cliente autenticado | PE | `addWishlist()` migra `product_id=5` a la cuenta y la wishlist de sesión queda vacía. |
| CP-L-029 | RF-LR-014 | Wishlist | Login sin wishlist anónima no genera error | Sesión sin wishlist, cliente autenticado | PE | El login procede sin errores, sin intentar migrar productos inexistentes. |
| CP-L-030 | RF-LR-015 | Limpieza checkout | Datos de checkout previos se eliminan al hacer login | Sesión con `order_id=99`, `shipping_method='flat'`, `payment_method='cod'` | PE | `order_id`, `shipping_method`, `payment_method` y variantes eliminados de sesión. |
| CP-L-031 | RF-LR-016 | Redirección | Redirección a URL válida del sitio tras login | `redirect='http://mitienda.com/account/wishlist'` | PE | Sistema redirige a la URL + `customer_token` (`str_starts_with` verifica dominio). |
| CP-L-032 | RF-LR-016 | Redirección | URL de retorno de dominio externo es ignorada | `redirect='http://sitiomalicioso.com'` | PE | Sistema ignora el redirect externo y redirige a `account/account`. |
| CP-L-033 | RF-LR-017 | Redirección | Redirección por defecto a `account/account` | Login exitoso sin parámetro redirect | PE | Sistema redirige a `account/account` con language y `customer_token` en la URL. |
| CP-L-034 | RF-LR-018 | Auditoría | Actividad de login registrada con auditoría activa | Login exitoso, `event/activity` habilitado | PE | Se registra actividad de login en el sistema de auditoría. |
| CP-L-035 | RF-LR-019 | Cierre de sesión | Logout destruye la sesión del cliente | Cliente autenticado ejecuta logout | PE | Sesión destruida, `customer_token` invalidado, cliente redirigido. |
| CP-L-036 | RF-LR-019 | Cierre de sesión | Acceso a rutas protegidas tras logout es denegado | Cliente intenta acceder a `account/account` sin sesión | PE | Sistema redirige a `account/login` sin mostrar datos privados. |

---

## 2. Registro (RF-LR-020 al RF-LR-042)

| ID | Requisito(s) | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|---|
| CP-R-001 | RF-LR-020 | Formulario registro | Formulario visible para usuario no autenticado | Usuario sin sesión accede a `account/register` | PE | Se muestra el formulario con campos First Name, Last Name, E-Mail, Password, Newsletter y Privacy Policy. |
| CP-R-002 | RF-LR-021 | Formulario registro | Cliente autenticado es redirigido desde registro | Cliente con sesión activa accede a `account/register` | PE | Sistema redirige a `account/account` sin mostrar el formulario. |
| CP-R-003 | RF-LR-022 | Token CSRF | `register_token` generado al cargar formulario | GET a `account/register` | PE | Se genera `register_token` de exactamente 26 caracteres en sesión. |
| CP-R-004 | RF-LR-022 | Token CSRF | Registro sin `register_token` es rechazado | POST al endpoint de registro sin `register_token` | PE | Sistema redirige a `account/register` sin procesar los datos. |
| CP-R-005 | RF-LR-022 | Token CSRF | Registro con `register_token` incorrecto rechazado | POST con `register_token='invalido'` | PE | Sistema redirige a `account/register` sin crear la cuenta. |
| CP-R-006 | RF-LR-024 | Validación First Name | Nombre de exactamente 1 carácter (límite mínimo) | `firstname='A'` | AVL | `oc_validate_length()` retorna true, campo válido. |
| CP-R-007 | RF-LR-024 | Validación First Name | Nombre vacío (por debajo del mínimo) | `firstname=''` | AVL | Error en campo `firstname`, registro rechazado. |
| CP-R-008 | RF-LR-024 | Validación First Name | Nombre de 32 caracteres (límite máximo) | `firstname='A'*32` | AVL | `oc_validate_length()` retorna true, campo válido. |
| CP-R-009 | RF-LR-024 | Validación First Name | Nombre de 33 caracteres (máximo+1) | `firstname='A'*33` | AVL | Error en campo `firstname`, registro rechazado. |
| CP-R-010 | RF-LR-024 | Validación First Name | Nombre dentro del rango válido | `firstname='Juan'` | PE | `oc_validate_length()` retorna true, campo válido. |
| CP-R-011 | RF-LR-025 | Validación Last Name | Apellido vacío (por debajo del mínimo) | `lastname=''` | AVL | Error en campo `lastname`, registro rechazado. |
| CP-R-012 | RF-LR-025 | Validación Last Name | Apellido de 1 carácter (límite mínimo) | `lastname='P'` | AVL | `oc_validate_length()` retorna true, campo válido. |
| CP-R-013 | RF-LR-025 | Validación Last Name | Apellido de 32 caracteres (límite máximo) | `lastname='B'*32` | AVL | `oc_validate_length()` retorna true, campo válido. |
| CP-R-014 | RF-LR-025 | Validación Last Name | Apellido de 33 caracteres (máximo+1) | `lastname='B'*33` | AVL | Error en campo `lastname`, registro rechazado. |
| CP-R-015 | RF-LR-026 | Validación Email | Email con formato válido | `email='usuario@dominio.com'` | PE | `oc_validate_email()` retorna true, email aceptado. |
| CP-R-016 | RF-LR-026 | Validación Email | Email sin arroba | `email='usuariodominio.com'` | PE | `oc_validate_email()` retorna false, error en campo email. |
| CP-R-017 | RF-LR-026 | Validación Email | Email sin dominio | `email='usuario@'` | PE | `oc_validate_email()` retorna false, error en campo email. |
| CP-R-018 | RF-LR-026 | Validación Email | Email vacío | `email=''` | AVL | `oc_validate_email()` retorna false, error en campo email. |
| CP-R-019 | RF-LR-026 | Validación Email | Email mínimo válido | `email='a@b.co'` | AVL | `oc_validate_email()` retorna true, email aceptado. |
| CP-R-020 | RF-LR-027 | Unicidad Email | Registro con email ya registrado | Email existente en BD | PE | `getTotalCustomersByEmail()` retorna >0, se muestra `error_exists`, no se crea cuenta. |
| CP-R-021 | RF-LR-027 | Unicidad Email | Registro con email único | Email no existente en BD | PE | `getTotalCustomersByEmail()` retorna 0, registro procede. |
| CP-R-022 | RF-LR-027 | Unicidad Email | Email duplicado en mayúsculas (case-insensitive) | `email='CLIENTE@TEST.COM'` con `'cliente@test.com'` ya registrado | PE | `LCASE()` detecta el duplicado, muestra `error_exists`. |
| CP-R-023 | RF-LR-028 | Teléfono | Teléfono vacío con campo obligatorio | `telephone=''`, `config_telephone_required=true` | AVL | `oc_validate_length(3,32)` retorna false, error en campo telephone. |
| CP-R-024 | RF-LR-028 | Teléfono | Teléfono de 2 caracteres (mínimo-1) | `telephone='12'`, `config_telephone_required=true` | AVL | `oc_validate_length(3,32)` retorna false, error en campo telephone. |
| CP-R-025 | RF-LR-028 | Teléfono | Teléfono de 3 caracteres (mínimo exacto) | `telephone='123'`, `config_telephone_required=true` | AVL | `oc_validate_length(3,32)` retorna true, campo válido. |
| CP-R-026 | RF-LR-028 | Teléfono | Teléfono vacío con campo opcional | `telephone=''`, `config_telephone_required=false` | PE | No se genera error, registro puede continuar. |
| CP-R-027 | RF-LR-032 | Contraseña longitud | Contraseña en el mínimo exacto | Password de longitud = `config_password_length` | AVL | `oc_validate_length()` retorna true, contraseña válida. |
| CP-R-028 | RF-LR-032 | Contraseña longitud | Contraseña un carácter menos del mínimo | Password de longitud = `config_password_length - 1` | AVL | `oc_validate_length()` retorna false, error en campo password. |
| CP-R-029 | RF-LR-032 | Contraseña longitud | Contraseña de 40 caracteres (máximo exacto) | `password='A'*40` | AVL | `oc_validate_length()` retorna true, contraseña válida. |
| CP-R-030 | RF-LR-032 | Contraseña longitud | Contraseña de 41 caracteres (máximo+1) | `password='A'*41` | AVL | `oc_validate_length()` retorna false, error en campo password. |
| CP-R-031 | RF-LR-032 | Contraseña longitud | Contraseña vacía | `password=''` | AVL | `oc_validate_length()` retorna false, error en campo password. |
| CP-R-032 | RF-LR-033 | Contraseña complejidad | Sin mayúscula con regla activa | `password='minuscula1!'`, `config_password_uppercase=true` | PE | Error específico por falta de mayúscula. |
| CP-R-033 | RF-LR-033 | Contraseña complejidad | Sin minúscula con regla activa | `password='MAYUSCULA1!'`, `config_password_lowercase=true` | PE | Error específico por falta de minúscula. |
| CP-R-034 | RF-LR-033 | Contraseña complejidad | Sin número con regla activa | `password='SinNumero!'`, `config_password_number=true` | PE | Error específico por falta de número. |
| CP-R-035 | RF-LR-033 | Contraseña complejidad | Sin símbolo con regla activa | `password='SinSimbolo1'`, `config_password_symbol=true` | PE | Error específico por falta de símbolo. |
| CP-R-036 | RF-LR-033 | Contraseña complejidad | Contraseña cumple todas las reglas activas | `password='Pass1234!'`, todas las reglas activas | PE | Sin errores de complejidad, contraseña válida. |
| CP-R-037 | RF-LR-037 | Privacy Policy | Registro sin aceptar Privacy Policy | `agree=0`, página de términos configurada | PE | Error con nombre del documento, registro bloqueado. |
| CP-R-038 | RF-LR-037 | Privacy Policy | Registro aceptando Privacy Policy | `agree=1` | PE | Sin error de términos, registro puede continuar. |
| CP-R-039 | RF-LR-038 | Creación de cuenta | Registro exitoso con todos los datos válidos | Todos los campos válidos, `agree=1`, token válido | PE | Cliente creado en BD, email en minúsculas, redirige a `account/success`. |
| CP-R-040 | RF-LR-039 | Hash contraseña | Contraseña almacenada como hash | Registro exitoso con `password='MiPass123!'` | PE | BD almacena hash bcrypt, no el texto plano. `password_verify()` retorna true con la original. |
| CP-R-041 | RF-LR-039 | Hash contraseña | Hash es diferente al texto plano | `password='MiPass123!'` y su hash | AVL | `hash !== 'MiPass123!'`, el hash es una cadena bcrypt diferente. |
| CP-R-042 | RF-LR-040, RF-LR-041 | Aprobación grupo | Cuenta creada en estado pendiente con grupo que requiere aprobación | `customer_group` con `approval=true` | PE | `status=0` en BD, se genera registro de aprobación con `addApproval()`. |
| CP-R-043 | RF-LR-040 | Aprobación grupo | Cuenta creada activa con grupo sin aprobación | `customer_group` con `approval=false` | PE | `status=1` en BD, cliente inicia sesión automáticamente. |

---

## 3. Recuperación y Restablecimiento de Contraseña (RF-LR-043 al RF-LR-060)

| ID | Requisito(s) | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|---|
| CP-RC-001 | RF-LR-043 | Pantalla recuperación | Acceso a pantalla de recuperación desde login | Clic en enlace 'Forgotten Password' | PE | Sistema muestra pantalla de recuperación en `account/forgotten` con campo email. |
| CP-RC-002 | RF-LR-044 | Acceso recuperación | Cliente autenticado redirigido desde recuperación | Cliente con sesión activa accede a `account/forgotten` | PE | Sistema redirige a `account/account` sin mostrar el formulario. |
| CP-RC-003 | RF-LR-045, RF-LR-046 | Solicitud recuperación | Solicitar recuperación con email registrado | `email='cliente@test.com'` registrado en BD | PE | Sistema inicia proceso, genera token y redirige al login. |
| CP-RC-004 | RF-LR-046 | Validación email | Solicitar recuperación con email no registrado | `email='noexiste@test.com'` | PE | Sistema muestra mensaje de error, no genera token. |
| CP-RC-005 | RF-LR-046 | Validación email | Solicitar recuperación con email vacío | `email=''` | AVL | Sistema rechaza con error de campo requerido. |
| CP-RC-006 | RF-LR-047 | Token recuperación | Generación de token de recuperación único | Solicitud válida de recuperación | PE | `addToken()` genera token de tipo `'password'`, elimina token previo del mismo tipo. |
| CP-RC-007 | RF-LR-047 | Token recuperación | Longitud exacta del token de recuperación | Token generado por `oc_token(26)` | AVL | El token tiene exactamente 26 caracteres. |
| CP-RC-008 | RF-LR-047 | Token recuperación | Un solo token activo por cliente | Segunda solicitud de recuperación del mismo cliente | PE | `addToken()` elimina el token previo antes de insertar el nuevo, solo existe 1 token activo. |
| CP-RC-009 | RF-LR-048 | Redirección | Redirección al login tras solicitud exitosa | Solicitud de recuperación válida completada | PE | Sistema redirige a `account/login` tras generar el token. |
| CP-RC-010 | RF-LR-050 | Formulario reset | Abrir formulario reset con código válido | Email válido y código correcto en URL | PE | Sistema muestra formulario para ingresar nueva contraseña. |
| CP-RC-011 | RF-LR-051, RF-LR-052 | Validación código | Código de reset inválido rechazado | `código='codigoinvalido'` en URL | PE | Sistema invalida el token, muestra error y redirige al login. |
| CP-RC-012 | RF-LR-051, RF-LR-060 | Validación código | Código de reset vencido (más de 10 min) rechazado | Código generado hace 11 minutos | AVL | `getTokenByCode()` elimina el token expirado, sistema rechaza y redirige al login. |
| CP-RC-013 | RF-LR-050, RF-LR-060 | Validación código | Código válido dentro de los 10 minutos | Código generado hace 9 minutos | AVL | `getTokenByCode()` retorna el token, sistema muestra formulario de reset. |
| CP-RC-014 | RF-LR-060 | Expiración token | Token expira exactamente a los 10 minutos | Token con `date_added = ahora - 10 minutos exactos` | AVL | `DATE_ADD(date_added, INTERVAL 10 MINUTE) = NOW()`, token eliminado como expirado. |
| CP-RC-015 | RF-LR-060 | Expiración token | Token válido a los 9 minutos | Token con `date_added = ahora - 9 minutos` | AVL | Token no expirado, `getTokenByCode()` lo retorna correctamente. |
| CP-RC-016 | RF-LR-053 | Token reset sesión | Cambio de contraseña protegido con `reset_token` de sesión | POST de nueva contraseña sin `reset_token` en sesión | PE | Sistema rechaza el cambio de contraseña. |
| CP-RC-017 | RF-LR-054 | Nueva contraseña longitud | Nueva contraseña con longitud mínima exacta | Nueva password de longitud = `config_password_length` | AVL | `oc_validate_length()` retorna true, longitud válida. |
| CP-RC-018 | RF-LR-054 | Nueva contraseña longitud | Nueva contraseña por debajo del mínimo | Nueva password de longitud = `config_password_length - 1` | AVL | `oc_validate_length()` retorna false, error de longitud. |
| CP-RC-019 | RF-LR-055 | Nueva contraseña complejidad | Nueva contraseña cumple todas las reglas activas | `password='NuevaPass1!'`, todas las reglas activas | PE | Sin errores de complejidad, contraseña válida para el reset. |
| CP-RC-020 | RF-LR-055 | Nueva contraseña complejidad | Nueva contraseña sin símbolo con regla activa | `password='SinSimbolo1'`, `config_password_symbol=true` | PE | Error específico por falta de símbolo. |
| CP-RC-021 | RF-LR-056 | Confirmación contraseña | Confirmación coincide con nueva contraseña | `password='NuevaPass1!'`, `confirm='NuevaPass1!'` | PE | Sin error de confirmación, proceso puede continuar. |
| CP-RC-022 | RF-LR-056 | Confirmación contraseña | Confirmación no coincide con nueva contraseña | `password='NuevaPass1!'`, `confirm='OtraPass2!'` | PE | Error: la confirmación no coincide con la nueva contraseña. |
| CP-RC-023 | RF-LR-056 | Confirmación contraseña | Confirmación vacía | `password='NuevaPass1!'`, `confirm=''` | AVL | Error: campo de confirmación requerido. |
| CP-RC-024 | RF-LR-057 | Actualización contraseña | Contraseña actualizada correctamente tras reset válido | Reset válido con nueva contraseña que cumple reglas | PE | `editPassword()` actualiza el hash en BD, login con nueva contraseña es exitoso. |
| CP-RC-025 | RF-LR-058 | Limpieza token reset | Token de reset eliminado tras uso exitoso | Reset completado exitosamente | PE | `deleteTokenByCode()` elimina el token, no puede usarse nuevamente. |
| CP-RC-026 | RF-LR-058 | Limpieza token reset | Token ya usado no puede reutilizarse | Intento de usar el mismo código de reset por segunda vez | PE | `getTokenByCode()` no retorna el token (ya fue eliminado), sistema rechaza. |

---

## 4. Correos y Alertas (RF-LR-061 al RF-LR-065)

| ID | Requisito(s) | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|---|
| CP-E-001 | RF-LR-061 | Correo bienvenida | Envío de correo de bienvenida tras registro exitoso | Registro exitoso con motor de correo configurado | PE | Se envía correo al email del cliente registrado. |
| CP-E-002 | RF-LR-061 | Correo bienvenida | Sin correo si motor no está configurado | Registro exitoso sin motor de correo configurado | PE | No se genera error, el registro procede sin enviar correo. |
| CP-E-003 | RF-LR-062 | Correo bienvenida | Correo incluye enlace de login | Correo de bienvenida enviado | PE | El cuerpo del correo contiene un enlace funcional a la página de login. |
| CP-E-004 | RF-LR-063 | Correo bienvenida | Correo indica cuenta pendiente de aprobación | Registro en grupo con `approval=true` | PE | El correo indica explícitamente que la cuenta requiere aprobación del administrador. |
| CP-E-005 | RF-LR-063 | Correo bienvenida | Correo no indica aprobación para cuentas activas | Registro en grupo con `approval=false` | PE | El correo no menciona aprobación pendiente, indica acceso inmediato. |
| CP-E-006 | RF-LR-064 | Alerta admin | Alerta enviada al admin con alerta habilitada | Registro exitoso, alerta de admin habilitada en config | PE | Se envía correo de alerta al correo principal del administrador. |
| CP-E-007 | RF-LR-064 | Alerta admin | Sin alerta al admin cuando está deshabilitada | Registro exitoso, alerta de admin deshabilitada | PE | No se envía correo al administrador, sin errores. |
| CP-E-008 | RF-LR-065 | Alerta admin adicional | Alerta enviada a correos administrativos adicionales válidos | Correos adicionales configurados: `'admin2@test.com, admin3@test.com'` | PE | Se envía alerta a cada correo administrativo adicional válido. |
| CP-E-009 | RF-LR-065 | Alerta admin adicional | Correos adicionales inválidos no generan error crítico | Correo adicional con formato inválido configurado | PE | Sistema ignora correos inválidos y envía a los válidos sin generar error crítico. |

---

## Resumen por técnica

| Técnica | Login | Registro | Recuperación | Correos | Total |
|---|---:|---:|---:|---:|---:|
| Partición de Equivalencia (PE) | 24 | 22 | 14 | 9 | **69** |
| Análisis de Valores Límite (AVL) | 12 | 21 | 12 | 0 | **45** |
| **Total** | **36** | **43** | **26** | **9** | **114** |