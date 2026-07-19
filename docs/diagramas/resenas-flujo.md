# Diagrama: Flujo del Sistema de Reseñas

## Descripción

Este diagrama muestra cómo un cliente visualiza y escribe reseñas de un producto, y cómo el
administrador modera esas reseñas antes de que se publiquen.

---

## Flujo del Sistema de Reseñas

```mermaid
graph TD
    A["👁️ Usuario visita detalle de producto"] --> B["⭐ Sistema muestra bloque<br/>de reseñas asociado al product_id"]

    B --> C{¿Producto tiene<br/>reseñas publicadas?}
    C -->|No| D["📭 Mostrar mensaje vacío<br/>sin errores"]
    C -->|Sí| E["📋 Listar reseñas paginadas<br/>(5 por página, más recientes primero)"]

    E --> F["👤 Mostrar autor, texto,<br/>rating y fecha de cada una"]

    D --> G{¿Usuario quiere<br/>escribir una reseña?}
    F --> G

    G -->|Sí| H{¿Cliente autenticado?}
    H -->|No| I{¿Reseña de invitado<br/>habilitada?}
    H -->|Sí| J["📝 Prellenar nombre<br/>del cliente"]

    I -->|No| K["🔑 Mostrar invitación<br/>a login/registro"]
    I -->|Sí| L["📝 Formulario abierto<br/>para invitado"]

    J --> M["🎫 Generar review_token<br/>(antifraude, por sesión)"]
    L --> M

    M --> N["✍️ Usuario completa<br/>autor, texto, calificación"]
    N --> O{¿Módulo de reseñas<br/>habilitado?}

    O -->|No| P["❌ Rechazar envío"]
    O -->|Sí| Q{¿Producto existe?}

    Q -->|No| P
    Q -->|Sí| R{¿Compra previa<br/>requerida y cumplida?}

    R -->|No, requerida<br/>y no cumplida| S["❌ Rechazar<br/>Exigir compra previa"]
    R -->|No requerida, o cumplida| T{¿Longitud de autor<br/>y texto válidas?<br/>¿Rating entre 1-5?}

    T -->|No| U["❌ Errores estructurados<br/>por campo"]
    T -->|Sí| V{¿Captcha requerido<br/>y resuelto?}

    V -->|No resuelto| U
    V -->|No requerido, o resuelto| W["✅ Guardar reseña<br/>en estado pendiente"]

    W --> X["✅ Confirmar al usuario:<br/>'Pendiente de aprobación'"]

    X --> Y["👨‍💼 Administrador revisa<br/>reseñas pendientes"]
    Y --> Z{¿Aprobar o rechazar?}

    Z -->|Aprobar| AA["✅ Cambiar estado a publicado"]
    Z -->|Rechazar/Eliminar| AB["🗑️ Eliminar o mantener oculta"]

    AA --> AC["📢 Reseña visible<br/>en el frontend del producto"]
```

---

## Puntos Clave

1. **Moderación obligatoria**: ninguna reseña aparece públicamente de inmediato — siempre queda
   en estado pendiente hasta que un administrador la aprueba.
2. **Protección antifraude**: cada envío requiere un `review_token` generado por sesión, similar
   al patrón de CSRF usado en login/registro.
3. **Reseña condicionada a compra**: configurable — la tienda puede exigir que el cliente haya
   comprado el producto antes de poder reseñarlo.
4. **Invitados opcionales**: la tienda decide si permite reseñas sin autenticación.
5. **Paginación fija**: 5 reseñas por página, ordenadas de más reciente a más antigua.

---

## Escenarios Cubiertos

- ✅ Ver reseñas publicadas de un producto (verificado contra `upload/`)
- ✅ Producto sin reseñas muestra estado vacío sin error (verificado contra `upload/`)
- ✅ Envío de reseña válida con confirmación de "pendiente de aprobación" (verificado contra `upload/`)
- ✅ Rechazo por calificación fuera de rango (1-5)
- ⏳ Aprobación desde el panel admin y verificación de visibilidad posterior — pendiente de
  prueba manual (ver
  [Historia de Usuario 3](../../tests/aceptacion/6-Sistema-de-Resenas.md#historia-de-usuario-3-las-reseñas-no-aparecen-públicamente-hasta-ser-aprobadas))
- ⏳ Moderación completa (filtrar, editar, eliminar desde admin) — pendiente de prueba manual
