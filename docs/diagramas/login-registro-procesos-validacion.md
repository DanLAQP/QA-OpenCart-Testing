# Diagrama: Procesos de Validación - Login y Registro

## Descripción

Árbol de decisiones completo para las validaciones de autenticación, registro y recuperación de
contraseña en los distintos puntos del sistema.

---

## Árbol de Decisiones de Validación

```mermaid
graph TD
    START["🚀 Usuario interactúa con Login/Registro"]

    START --> A{¿Qué acción realiza?}

    A -->|Login| LOGIN["🔑 VALIDACIÓN DE LOGIN"]
    A -->|Registro| REG["📝 VALIDACIÓN DE REGISTRO"]
    A -->|Recuperación| RESET["🔁 VALIDACIÓN DE RECUPERACIÓN"]

    %% LOGIN
    LOGIN --> L1{¿login_token<br/>coincide con sesión?}
    L1 -->|No| L2["❌ Rechazar sin procesar<br/>Redirigir a login"]
    L1 -->|Sí| L3{¿Intentos fallidos<br/>(correo+IP, última hora)<br/>>= config_login_attempts?}

    L3 -->|Sí| L4["🔒 error_attempts<br/>Bloqueo de 1 hora"]
    L3 -->|No| L5{¿Cuenta existe?}

    L5 -->|No| L6["❌ Registrar intento fallido<br/>error_login genérico"]
    L5 -->|Sí| L7{¿status = aprobado?}

    L7 -->|No| L8["❌ error_approved<br/>(sin revisar password)"]
    L7 -->|Sí| L9{¿password coincide<br/>con el hash?}

    L9 -->|No| L6
    L9 -->|Sí| L10["✅ Login exitoso<br/>Limpiar intentos fallidos"]

    %% REGISTRO
    REG --> R1{¿register_token<br/>válido?}
    R1 -->|No| R2["❌ Rechazar"]
    R1 -->|Sí| R3{¿Cliente ya<br/>autenticado?}

    R3 -->|Sí| R4["❌ Impedir registro"]
    R3 -->|No| R5{¿firstname/lastname<br/>entre 1-32 caracteres?}

    R5 -->|No| R6["❌ Error específico por campo"]
    R5 -->|Sí| R7{¿Email formato válido<br/>y no duplicado?}

    R7 -->|Duplicado| R8["❌ error_exists"]
    R7 -->|Inválido| R6
    R7 -->|Válido y único| R9{¿Password cumple<br/>longitud 6-40 y<br/>complejidad configurada?}

    R9 -->|No| R10["❌ Mensajes específicos<br/>por regla incumplida"]
    R9 -->|Sí| R11{¿agree = true<br/>(Política de Privacidad)?}

    R11 -->|No| R12["❌ Exigir aceptación"]
    R11 -->|Sí| R13["✅ Crear cliente<br/>hash de contraseña"]

    R13 --> R14{¿customer_group<br/>requiere aprobación?}
    R14 -->|Sí| R15["⏳ Estado pendiente<br/>Generar customer_approval"]
    R14 -->|No| R16["✅ Estado activo"]

    R15 --> R17["📧 Correo de bienvenida<br/>+ alerta a administrador"]
    R16 --> R17

    %% RECUPERACION
    RESET --> P1{¿Correo existe<br/>en base de datos?}
    P1 -->|No| P2["❌ No revelar existencia<br/>(mensaje genérico)"]
    P1 -->|Sí| P3["🔑 Generar token 26 caracteres<br/>Enviar por correo"]

    P3 --> P4["👤 Usuario abre enlace<br/>con email + código"]
    P4 --> P5{¿Código correcto,<br/>no vencido y<br/>consistente?}

    P5 -->|No| P6["❌ Eliminar token inválido<br/>Solicitar nuevo enlace"]
    P5 -->|Sí| P7["📝 Formulario de nueva contraseña<br/>protegido con token de reset"]

    P7 --> P8{¿Password cumple<br/>longitud/complejidad?}
    P8 -->|No| P9["❌ Mostrar reglas incumplidas"]
    P8 -->|Sí| P10{¿Confirmación coincide<br/>con nueva contraseña?}

    P10 -->|No| P11["❌ Rechazar"]
    P10 -->|Sí| P12["✅ Actualizar contraseña<br/>Eliminar token de reset"]

    style LOGIN fill:#e1f5ff
    style REG fill:#fff3e0
    style RESET fill:#f3e5f5
```

---

## Matriz de Validación por Punto

| Punto | Valida | CSRF | Rate Limiting | Mensaje específico | Resultado |
|---|---|---|---|---|---|
| **Login** | Token, intentos fallidos, aprobación, credenciales | ✅ | ✅ (1h) | ❌ (genérico) | Sesión creada / Rechazo |
| **Registro** | Token, campos, email único, password, términos | ✅ | ❌ | ✅ (por campo) | Cuenta creada (activa/pendiente) |
| **Recuperación (solicitud)** | Existencia de correo | N/A | ❌ | ❌ (no revela existencia) | Correo enviado |
| **Recuperación (reset)** | Token de reset, expiración, password, confirmación | ✅ | ❌ | ✅ (por campo) | Contraseña actualizada |

---

## Flujos Críticos

### 🔴 Flujo: Fuerza bruta bloqueada
```
Intento 1 fallido → Intento 2 fallido → ... → Intento N (supera config_login_attempts) →
Bloqueo de 1 hora → Intentos posteriores rechazados sin validar contraseña
```

### 🟡 Flujo: Registro con aprobación pendiente
```
Registro válido → Grupo requiere aprobación → Cuenta creada en estado pendiente →
customer_approval generado → Correo indica "requiere aprobación" →
Administrador aprueba → Cliente puede iniciar sesión
```

### 🟢 Flujo: Recuperación exitosa
```
Solicitar recuperación → Correo existe → Token generado y enviado →
Usuario abre enlace dentro de 10 min → Nueva contraseña válida y confirmada →
Contraseña actualizada → Token eliminado → Login con nueva contraseña
```

---

## Puntos de Tolerancia y Seguridad

### Mensajes genéricos para no facilitar enumeración
- El login no distingue "correo no existe" de "contraseña incorrecta".
- La solicitud de recuperación no confirma si el correo existe o no.

### Expiración estricta de tokens
- `customer_token`: 10 minutos.
- `customer_reset`: se invalida ante cualquier inconsistencia (código incorrecto, vencido).

### Auditoría opcional
- Login, registro y cambio de contraseña pueden registrar actividad si la auditoría está
  habilitada (`config_customer_activity` o equivalente).

### Hallazgo conocido: confirmación de contraseña en registro
⚠️ El árbol de arriba **no incluye** una validación de "confirmación de contraseña coincide con
contraseña" en el flujo de Registro, porque esa validación **no existe actualmente del lado del
servidor** — ver
[INC-ACEPT-001](../../tests/aceptacion/incident-reports/INC-ACEPT-001-registro-sin-confirmar-password.md).
