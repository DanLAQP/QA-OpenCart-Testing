# Diagrama: Flujo de Stock - Gestión de Inventario

## Descripción

Este diagrama muestra cómo se valida y actualiza el stock desde que el usuario visualiza un producto en el catálogo hasta la confirmación de la compra.

---

## Flujo de Stock

```mermaid
graph TD
    A["👁️ Usuario visualiza Catálogo"] --> B["📋 Sistema carga Producto"]
    B --> C{Validar Stock Disponible}
    
    C -->|quantity > 0 & status=1| D["✅ Producto Habilitado"]
    C -->|quantity = 0 & allow_out_of_stock=true| E["⚠️ Sin stock pero Permitido"]
    C -->|quantity = 0 & allow_out_of_stock=false| F["❌ Producto Deshabilitado"]
    
    D --> G["🛒 Usuario Agrega al Carrito"]
    E --> G
    F --> H["❌ Mostrar Botón Deshabilitado"]
    
    G --> I["🔍 Validar Stock en Carrito"]
    I --> J{Hay suficiente stock?}
    
    J -->|Sí| K["✅ Agregar al Carrito"]
    J -->|No| L["❌ Rechazar + Mostrar Error"]
    
    L --> M["📱 Mostrar Cantidad Máxima Disponible"]
    
    K --> N["📦 Usuario Procede al Checkout"]
    N --> O["🔍 Recalcular Stock"]
    
    O --> P{¿Stock cambió?}
    P -->|Sí, menos stock| Q["⚠️ Notificar usuario de cambios"]
    P -->|No| R["✅ Stock válido"]
    P -->|Sí, sin stock| S["❌ Eliminar item del carrito"]
    
    Q --> T["💳 Usuario Confirma Orden"]
    R --> T
    S --> U["⚠️ Carrito actualizado"]
    
    U --> T
    T --> V["🔐 Validar Stock Final"]
    
    V --> W{Stock disponible?}
    W -->|Sí| X["✅ Crear Orden"]
    W -->|No| Y["❌ Cancelar Transacción"]
    
    X --> Z["📊 Actualizar Inventario"]
    Z --> AA["📧 Confirmación a Usuario"]
    
    Y --> AB["❌ Mostrar Error"]
    AB --> AC["🔄 Usuario vuelve al carrito"]
    
    AA --> AD["✅ Orden Completada"]
    H --> AE["⏳ Mostrar Fecha de Disponibilidad"]
```

---

## Puntos Clave

1. **Validación en Catálogo**: Se verifica `quantity` y `status` antes de permitir compra
2. **Validación en Carrito**: Se valida cantidad total de items del mismo producto
3. **Validación en Checkout**: Se recalcula stock considerando cambios durante la sesión
4. **Validación en Confirmación**: Último control antes de restar inventario
5. **Actualización**: Stock se resta solo tras confirmación exitosa
6. **Configuración**: `allow_out_of_stock` determina si se permite venta sin stock

---

## Escenarios Cubiertos

- ✅ Producto con stock disponible
- ✅ Producto sin stock pero venta permitida
- ✅ Producto sin stock y venta no permitida
- ✅ Stock insuficiente en carrito
- ✅ Cambios de stock durante checkout
- ✅ Validación final antes de confirmación
- ✅ Producto con fecha de disponibilidad futura
