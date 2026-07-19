# Diagrama: Flujo de Login y Registro

## Descripción

Este diagrama muestra el flujo completo de autenticación: inicio de sesión, protección CSRF,
bloqueo por intentos fallidos, registro de cuenta nueva y recuperación de contraseña.

---

## Flujo de Login y Registro

```mermaid
graph TD
    A["👁️ Usuario visita account/login"] --> B["🔑 Sistema genera login_token<br/>(CSRF, 26 caracteres)"]
    B --> C["📝 Usuario ingresa correo y contraseña"]
    C --> D{¿login_token válido?}

    D -->|No coincide o ausente| E["❌ Rechazar sin procesar<br/>Redirigir a account/login"]
    D -->|Válido| F{¿Intentos fallidos<br/>superan el límite?}

    F -->|Sí, dentro de la última hora| G["🔒 Bloquear acceso 1 hora<br/>Mostrar error_attempts"]
    F -->|No| H{¿Cuenta existe y<br/>status = aprobado?}

    H -->|No aprobada| I["❌ Mostrar error_approved<br/>(sin revisar contraseña)"]
    H -->|Aprobada| J{¿Correo y contraseña<br/>coinciden?}

    J -->|No| K["❌ Registrar intento fallido<br/>Mostrar error_login genérico"]
    J -->|Sí| L["✅ Eliminar intentos fallidos previos"]

    L --> M["🔓 Crear sesión autenticada<br/>customer->login()"]
    M --> N["🔑 Generar customer_token único"]
    N --> O["📋 Fusionar wishlist anónima"]
    O --> P["🧹 Limpiar datos previos de checkout"]
    P --> Q["➡️ Redirigir a account/account<br/>o URL de retorno válida"]

    R["👤 Usuario visita account/register"] --> S["🔑 Sistema genera register_token"]
    S --> T["📝 Usuario completa formulario"]
    T --> U{¿Validaciones de campos<br/>pasan? (nombre, email,<br/>password, agree)}

    U -->|No| V["❌ Rechazar con errores<br/>estructurados por campo"]
    U -->|Sí| W{¿Correo ya registrado?}

    W -->|Sí| X["❌ error_exists"]
    W -->|No| Y["✅ Crear cliente<br/>hash de contraseña seguro"]

    Y --> Z{¿Grupo requiere<br/>aprobación?}
    Z -->|Sí| AA["⏳ Cuenta pendiente<br/>Generar registro de aprobación"]
    Z -->|No| AB["✅ Cuenta activa"]

    AA --> AC["📧 Enviar correo de bienvenida<br/>+ alerta a administrador"]
    AB --> AC
    AC --> AD["➡️ Redirigir a account/success"]

    AE["🔁 Usuario olvidó contraseña"] --> AF["📧 Solicitar recuperación<br/>por correo"]
    AF --> AG{¿Correo existe?}
    AG -->|No| AH["❌ No revelar si existe<br/>(mensaje genérico)"]
    AG -->|Sí| AI["🔑 Generar token de recuperación<br/>26 caracteres"]

    AI --> AJ["📧 Enviar enlace de reset"]
    AJ --> AK["👤 Usuario abre enlace"]
    AK --> AL{¿Token válido y<br/>no expirado (10 min)?}

    AL -->|No| AM["❌ Invalidar y eliminar token<br/>Solicitar nuevo enlace"]
    AL -->|Sí| AN["📝 Usuario ingresa nueva contraseña"]

    AN --> AO{¿Contraseña cumple<br/>longitud/complejidad?}
    AO -->|No| AP["❌ Mostrar reglas incumplidas"]
    AO -->|Sí| AQ["✅ Actualizar contraseña<br/>Eliminar token de reset"]

    AQ --> AR["➡️ Redirigir a login"]
```

---

## Puntos Clave

1. **Protección CSRF**: todo formulario sensible (login, registro) genera un token de 26
   caracteres que se valida en el servidor antes de procesar cualquier dato.
2. **Bloqueo por intentos fallidos**: se cuenta por correo + IP dentro de la última hora; al
   superar `config_login_attempts`, se bloquea 1 hora completa.
3. **Mensajes de error genéricos en login**: no se distingue "correo no existe" de "contraseña
   incorrecta", para no facilitar enumeración de cuentas.
4. **Aprobación jerárquica de cuentas**: algunos grupos de cliente requieren aprobación manual
   del administrador antes de poder iniciar sesión.
5. **Expiración de tokens**: los tokens de recuperación expiran a los 10 minutos, forzando a
   solicitar uno nuevo si el usuario tarda demasiado.

---

## Escenarios Cubiertos

- ✅ Login exitoso con credenciales válidas
- ✅ Rechazo de login por token CSRF inválido o ausente
- ✅ Bloqueo tras exceder intentos fallidos
- ✅ Rechazo por cuenta pendiente de aprobación
- ✅ Registro exitoso con datos válidos
- ✅ Rechazo de registro por correo duplicado
- ✅ Cuenta pendiente de aprobación tras registro
- ✅ Recuperación de contraseña con token válido
- ✅ Rechazo de token de recuperación expirado o inválido

---

## Hallazgo relacionado

⚠️ Durante las pruebas de aceptación se documentó que el registro **no valida** que la
contraseña y su confirmación coincidan del lado del servidor
([INC-ACEPT-001](../../tests/aceptacion/incident-reports/INC-ACEPT-001-registro-sin-confirmar-password.md) /
[INC-USAB-001](../../tests/no-funcionales/usabilidad/incident-reports/INC-USAB-001-sin-validacion-confirmar-password.md)).
Este paso no aparece como una validación real en el flujo de arriba porque, en efecto, no existe
en el código actual — se documenta aquí como nota, no como comportamiento verificado.
