# Diagrama: Flujo de Checkout y Pago

## Descripción

Este diagrama muestra el flujo completo de compra desde que el cliente entra a checkout hasta
la confirmación final del pedido: direcciones, métodos de envío/pago, y estados de éxito/fallo.

---

## Flujo de Checkout y Pago

```mermaid
graph TD
    A["🛒 Usuario hace clic en Checkout"] --> B{¿Carrito válido?<br/>(productos, stock, mínimos)}

    B -->|No| C["❌ Redirigir al carrito<br/>con explicación"]
    B -->|Sí| D["📋 Mostrar pantalla<br/>principal de checkout"]

    D --> E{¿Cliente autenticado?}
    E -->|No| F["👤 Ofrecer registro rápido<br/>o continuar como invitado"]
    E -->|Sí| G["📍 Paso: Dirección de pago"]
    F --> G

    G --> H{¿Tiene direcciones<br/>guardadas?}
    H -->|Sí| I["📋 Seleccionar existente"]
    H -->|No| J["📝 Registrar nueva dirección"]

    I --> K["📍 Paso: Dirección de envío<br/>(si el carrito requiere envío)"]
    J --> K

    K --> L{¿Carrito requiere envío?}
    L -->|No| M["⏭️ Omitir paso de envío"]
    L -->|Sí| N["🚚 Cotizar métodos de envío<br/>según dirección"]

    N --> O{¿Hay métodos<br/>disponibles?}
    O -->|No| P["❌ Informar sin opciones<br/>de envío disponibles"]
    O -->|Sí| Q["✅ Usuario selecciona<br/>método de envío"]

    Q --> M
    M --> R["💳 Paso: Método de pago"]

    R --> S["💰 Obtener métodos de pago<br/>según dirección/envío"]
    S --> T{¿Hay métodos<br/>disponibles?}

    T -->|No| U["❌ Informar sin opciones<br/>de pago disponibles"]
    T -->|Sí| V["✅ Usuario selecciona método<br/>+ comentario + acepta términos"]

    V --> W["📝 Paso: Confirmación"]
    W --> X["🧮 Calcular totales finales<br/>impuestos + envío"]

    X --> Y{¿Revalidación final<br/>de stock/mínimos OK?}
    Y -->|No| Z["❌ Informar producto<br/>ya no disponible"]
    Y -->|Sí| AA["✅ Usuario confirma pedido"]

    AA --> AB["📦 Generar orden<br/>con todos los datos"]
    AB --> AC{¿Pago procesado<br/>exitosamente?}

    AC -->|Sí| AD["🎉 Pantalla de éxito<br/>Vaciar carrito"]
    AC -->|No| AE["⚠️ Pantalla de fallo<br/>+ información de contacto"]

    AD --> AF["🧹 Limpiar sesión de checkout<br/>(order_id, envío, pago, cupón)"]
    AE --> AG["🔄 Permitir volver al inicio"]
```

---

## Puntos Clave

1. **Validación de entrada al checkout**: no se puede acceder si el carrito está vacío, sin
   stock suficiente o sin cumplir cantidades mínimas.
2. **Pasos condicionales**: la dirección y método de envío se omiten completamente si el
   carrito no contiene productos que requieran envío.
3. **Recalculo en cascada**: cambiar la dirección de pago limpia los métodos de envío/pago ya
   calculados, forzando su recotización.
4. **Revalidación final antes de confirmar**: se vuelve a verificar stock y cantidades mínimas
   justo antes de generar la orden, incluso si ya se validó al agregar al carrito.
5. **Limpieza total tras éxito**: al completar la compra, se vacía el carrito y se limpia toda
   la sesión de checkout (envío, pago, cupón, recompensa).

---

## Escenarios Cubiertos

- ✅ Entrada a checkout con carrito válido
- ✅ Rechazo de entrada con carrito inválido
- ✅ Checkout sin necesidad de envío (productos digitales)
- ✅ Selección de dirección existente vs. nueva
- ✅ Rechazo cuando no hay métodos de envío/pago disponibles
- ✅ Revalidación de stock antes de confirmar (ya verificado en RF-INV-018)
- ⚠️ Pantalla de fallo con error técnico si la base de datos falla durante el proceso — ver
  [INC-DISP-001](../../tests/no-funcionales/disponibilidad/incident-reports/INC-DISP-001-error-no-controlado-sin-bd.md)
