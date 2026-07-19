# Diagrama: Flujo del Carrito de Compras

## Descripción

Este diagrama muestra cómo un cliente agrega, modifica y elimina productos del carrito, y cómo
persiste ese carrito entre sesiones y autenticaciones.

---

## Flujo del Carrito

```mermaid
graph TD
    A["👁️ Usuario en detalle de producto"] --> B["🛒 Agregar al carrito"]
    B --> C{¿Producto existe?}

    C -->|No| D["❌ Rechazar<br/>Redirigir a detalle de producto"]
    C -->|Sí| E{¿Es variante?}

    E -->|Sí| F["🔍 Resolver producto maestro"]
    E -->|No| G["🔍 Validar opciones obligatorias"]
    F --> G

    G --> H{¿Opciones obligatorias<br/>completas y válidas?}
    H -->|No| D
    H -->|Sí| I{¿Requiere plan de<br/>suscripción?}

    I -->|Sí, sin seleccionar| D
    I -->|No, o seleccionado| J["✅ Agregar producto al carrito"]

    J --> K["🧹 Limpiar métodos de<br/>envío/pago previamente calculados"]
    K --> L["✅ Mostrar mensaje de éxito<br/>+ enlaces a producto y carrito"]

    L --> M["🛒 Usuario ve su carrito"]
    M --> N["📋 Listar productos<br/>imagen, opciones, cantidad, precio"]
    N --> O["🧮 Calcular totales<br/>+ impuestos"]

    O --> P{¿Usuario modifica<br/>cantidad?}
    P -->|Sí, a 0| Q["🗑️ Eliminar producto<br/>del carrito"]
    P -->|Sí, mayor a 0| R["🔄 Actualizar cantidad"]
    P -->|No| S["➡️ Continuar"]

    Q --> T["🧹 Limpiar envío/pago/recompensas"]
    R --> T
    T --> U{¿Carrito quedó vacío?}
    U -->|Sí| V["🔁 Redirigir a carrito vacío"]
    U -->|No| W["✅ Confirmación de actualización"]

    S --> X{¿Usuario elimina<br/>un producto?}
    X -->|Sí| Q
    X -->|No| Y["➡️ Continuar comprando<br/>o ir a checkout"]

    W --> Y
    V --> Y

    Y --> Z{¿Cliente autenticado?}
    Z -->|No| AA["👤 Carrito asociado a sesión<br/>de invitado"]
    Z -->|Sí| AB["👤 Carrito asociado<br/>a cliente"]

    AA --> AC{¿Cliente inicia sesión<br/>en la misma sesión?}
    AC -->|Sí| AD["🔀 Transferir productos<br/>de invitado a cuenta"]
    AC -->|No| AE["⏳ Carrito de invitado<br/>expira automáticamente"]
```

---

## Puntos Clave

1. **Validación previa a agregar**: existencia del producto, resolución de variantes, opciones
   obligatorias y plan de suscripción, todo antes de insertar en el carrito.
2. **Limpieza de estado en cascada**: agregar, editar o eliminar un producto limpia
   automáticamente métodos de envío/pago y recompensas ya calculados, para evitar
   inconsistencias con el nuevo contenido del carrito.
3. **Persistencia dual**: el carrito se asocia a la sesión (invitado) o al cliente
   (autenticado), y se transfiere automáticamente al iniciar sesión dentro de la misma sesión de
   navegador.
4. **Carritos de invitado expiran**: se limpian automáticamente tras un tiempo configurado, sin
   intervención del usuario.

---

## Escenarios Cubiertos

- ✅ Agregar producto con stock disponible
- ✅ Rechazo al no seleccionar opción obligatoria
- ✅ Ver carrito con totales calculados
- ✅ Actualizar cantidad (aumentar y reducir)
- ✅ Reducir cantidad a 0 elimina el producto
- ✅ Eliminar producto, con y sin productos restantes
- ✅ Transferencia de carrito de invitado a cuenta autenticada (⏳ pendiente de verificación
  manual completa, ver [Historia de Usuario 5](../../tests/aceptacion/3-Carrito-de-Compras.md#historia-de-usuario-5-mi-carrito-persiste-aunque-cierre-sesión-o-cambie-de-dispositivo))
