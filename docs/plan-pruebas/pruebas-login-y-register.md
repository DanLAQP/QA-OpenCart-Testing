# Pruebas: Login y Registro

## Descripción

Este documento contiene una propuesta de **36 casos de prueba** para el módulo **Login y Registro**, diseñados con las técnicas de:

- **PE**: Partición de Equivalencia
- **AVL**: Análisis de Valores Límite

Se presentan en formato tabular para facilitar su uso en documentación QA, pruebas funcionales, validación académica o preparación de casos en herramientas de testing.

---

## 1. Login (RF-LR-001 al RF-LR-019)

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|
| CP-L-001 | Pantalla de login | Visualizar página de login sin autenticación | Usuario no autenticado accede a `account/login` | PE | Se muestra la página con campos E-Mail, Password, enlace a registro y enlace a Forgotten Password. |
| CP-L-002 | Pantalla de login | Usuario ya autenticado intenta acceder al login | Usuario con sesión activa y `customer_token` válido accede a `account/login` | PE | El sistema redirige al usuario a `account/account` sin mostrar el formulario. |
| CP-L-003 | Autenticación | Login exitoso con credenciales válidas | `email='cliente@test.com'`, `password='Pass1234'` | PE | Autenticación exitosa, sesión creada, redirige a `account/account` con `customer_token`. |
| CP-L-004 | Autenticación | Login con contraseña incorrecta | `email='cliente@test.com'`, `password='incorrecta'` | PE | Se muestra mensaje `error_login` genérico sin especificar qué campo falló. |
| CP-L-005 | Autenticación | Login con email no registrado | `email='noexiste@test.com'`, `password='Pass1234'` | PE | Se muestra mensaje `error_login`, se registra intento fallido. |
| CP-L-006 | Autenticación | Login con contraseña vacía | `email='cliente@test.com'`, `password=''` | AVL | El sistema rechaza el intento y muestra mensaje de error de campo requerido. |
| CP-L-007 | Autenticación | Login con email vacío | `email=''`, `password='Pass1234'` | AVL | El sistema rechaza el intento y muestra mensaje de error de campo requerido. |
| CP-L-008 | Autenticación | Login con ambos campos vacíos | `email=''`, `password=''` | AVL | El sistema rechaza el intento y muestra mensajes de error en ambos campos. |
| CP-L-009 | Token CSRF | Verificar generación de `login_token` al cargar página | GET a `account/login` | PE | Se genera `login_token` de exactamente 26 caracteres en sesión. |
| CP-L-010 | Token CSRF | Verificar longitud exacta del `login_token` | Token generado por `oc_token(26)` | AVL | El token tiene exactamente 26 caracteres hexadecimales `[0-9a-f]`. |
| CP-L-011 | Token CSRF | Login sin `login_token` en la URL | POST a login sin parámetro `login_token` | PE | Sistema rechaza el intento y redirige a `account/login` sin procesar credenciales. |
| CP-L-012 | Token CSRF | Login con `login_token` incorrecto | POST con `login_token='tokenfalso123'` | PE | Sistema rechaza el intento y redirige a `account/login`. |
| CP-L-013 | Intentos fallidos | Registrar intento fallido con email+IP | `email='test@test.com'`, password incorrecta, `IP=192.168.1.1` | PE | Se inserta o actualiza registro en `customer_login` con email, IP y total incrementado. |
| CP-L-014 | Intentos fallidos | Intentos desde diferente IP se registran separados | Mismo email, `IP=192.168.1.2` diferente | PE | Se crea registro separado en `customer_login` para la nueva IP. |
| CP-L-015 | Bloqueo de cuenta | Bloqueo al alcanzar el límite de intentos | `config_login_attempts=5`, total=5 intentos fallidos en la última hora | AVL | Se muestra mensaje `error_attempts`, el login es bloqueado. |
| CP-L-016 | Bloqueo de cuenta | Sin bloqueo con intentos por debajo del límite | total=4 intentos fallidos (límite-1) | AVL | El sistema no bloquea, permite intentar login. |
| CP-L-017 | Bloqueo de cuenta | Bloqueo no aplica si los intentos son de más de 1 hora | total=5 intentos, `date_modified` hace 61 minutos | AVL | El sistema NO aplica bloqueo, `strtotime('-1 hour') > strtotime(date_modified)`. |
| CP-L-018 | Bloqueo de cuenta | Bloqueo aplica con intentos exactamente en el límite de 1 hora | total=5 intentos, `date_modified` hace exactamente 60 min | AVL | El sistema aplica bloqueo, el intento está dentro de la ventana de 1 hora. |
| CP-L-019 | Estado de cuenta | Login rechazado por cuenta no aprobada | Cliente con `status=false` intenta login con credenciales correctas | PE | Se muestra mensaje `error_approved` antes de verificar la contraseña. |
| CP-L-020 | Estado de cuenta | Login exitoso con cuenta activa | Cliente con `status=true`, credenciales correctas | PE | Login exitoso, sesión creada correctamente. |
| CP-L-021 | Credenciales incorrectas | Contraseña incorrecta incrementa contador | Email registrado, `password='incorrecta'` | PE | `addLoginAttempt()` es invocado, total en `customer_login` incrementa en 1. |
| CP-L-022 | Limpieza de intentos | Login exitoso elimina intentos previos | 3 intentos fallidos previos, luego login correcto | PE | `deleteLoginAttempts()` elimina todos los registros del email en `customer_login`. |
| CP-L-023 | Sesión | Creación de sesión tras login exitoso | Login con credenciales válidas | PE | Session data contiene `customer_id`, `customer_group_id`, `firstname`, `lastname`, `email`, `telephone`, `custom_field`. |
| CP-L-024 | Sesión | Datos del cliente guardados en sesión | Login exitoso | PE | Los datos del cliente en sesión coinciden exactamente con los datos de la BD. |
| CP-L-025 | customer_token | Generación de `customer_token` tras login | Login exitoso | PE | Se genera `customer_token` de exactamente 26 caracteres en sesión. |
| CP-L-026 | customer_token | Longitud exacta del `customer_token` | `customer_token` generado con `oc_token(26)` | AVL | El token tiene exactamente 26 caracteres. |
| CP-L-027 | Registro de IP | IP registrada tras login exitoso | Login exitoso desde `IP=10.0.0.1` | PE | `addLogin()` inserta registro en `customer_ip` con `customer_id` e IP correctos. |
| CP-L-028 | Wishlist | Fusión de wishlist anónima al hacer login | Sesión con `wishlist=[product_id=5]`, cliente autenticado | PE | `addWishlist()` migra `product_id=5` a la cuenta y la wishlist de sesión queda vacía. |
| CP-L-029 | Wishlist | Login sin wishlist anónima no genera error | Sesión sin wishlist, cliente autenticado | PE | El login procede sin errores, sin intentar migrar productos inexistentes. |
| CP-L-030 | Limpieza checkout | Datos de checkout previos se eliminan al hacer login | Sesión con `order_id=99`, `shipping_method='flat'`, `payment_method='cod'` | PE | `order_id`, `shipping_method`, `payment_method` y variantes eliminados de sesión. |
| CP-L-031 | Redirección | Redirección a URL válida del sitio tras login | `redirect='http://mitienda.com/account/wishlist'` | PE | Sistema redirige a la URL + `customer_token` (`str_starts_with` verifica dominio). |
| CP-L-032 | Redirección | URL de retorno de dominio externo es ignorada | `redirect='http://sitiomalicioso.com'` | PE | Sistema ignora el redirect externo y redirige a `account/account`. |
| CP-L-033 | Redirección | Redirección por defecto a `account/account` | Login exitoso sin parámetro redirect | PE | Sistema redirige a `account/account` con language y `customer_token` en la URL. |
| CP-L-034 | Auditoría | Actividad de login registrada con auditoría activa | Login exitoso, `event/activity` habilitado | PE | Se registra actividad de login en el sistema de auditoría. |
| CP-L-035 | Cierre de sesión | Logout destruye la sesión del cliente | Cliente autenticado ejecuta logout | PE | Sesión destruida, `customer_token` invalidado, cliente redirigido. |
| CP-L-036 | Cierre de sesión | Acceso a rutas protegidas tras logout es denegado | Cliente intenta acceder a `account/account` sin sesión | PE | Sistema redirige a `account/login` sin mostrar datos privados. |

---

## Resumen por técnica

| Técnica | Cantidad |
|---|---:|
| Partición de Equivalencia (PE) | 24 |
| Análisis de Valores Límite (AVL) | 12 |
| **Total** | **36** |
