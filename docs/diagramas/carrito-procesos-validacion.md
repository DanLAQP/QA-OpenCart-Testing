# Diagrama: Procesos de Validación - Carrito de Compras

## Descripción

Árbol de decisiones para agregar, editar y eliminar productos del carrito, incluyendo
validaciones de stock, opciones y límites de cantidad.

---

## Árbol de Decisiones de Validación

```mermaid
graph TD
    START["🚀 Usuario interactúa con el carrito"]

    START --> A{¿Qué acción realiza?}

    A -->|Agregar| ADD["➕ AGREGAR PRODUCTO"]
    A -->|Editar cantidad| EDIT["✏️ EDITAR CANTIDAD"]
    A -->|Eliminar| DEL["🗑️ ELIMINAR PRODUCTO"]

    %% AGREGAR
    ADD --> AD1{¿Producto existe<br/>y está activo?}
    AD1 -->|No| AD2["❌ Rechazar"]
    AD1 -->|Sí| AD3{¿Es variante?}

    AD3 -->|Sí| AD4["🔍 Resolver producto<br/>maestro + variante"]
    AD3 -->|No| AD5{¿Tiene opciones<br/>obligatorias?}
    AD4 --> AD5

    AD5 -->|Sí, no seleccionadas| AD6["❌ Rechazar<br/>Indicar opción faltante"]
    AD5 -->|Completas| AD7{¿Opciones con<br/>descuento de stock<br/>tienen disponibilidad?}

    AD7 -->|No| AD6
    AD7 -->|Sí| AD8{¿quantity_solicitada +<br/>quantity_ya_en_carrito <=<br/>stock_disponible?}

    AD8 -->|No| AD9["❌ Rechazar<br/>Mostrar cantidad máxima disponible"]
    AD8 -->|Sí| AD10{¿quantity_total >=<br/>minimum del producto?}

    AD10 -->|No| AD11["❌ Rechazar<br/>Mostrar cantidad mínima requerida"]
    AD10 -->|Sí| AD12["✅ Agregar al carrito<br/>Limpiar envío/pago previos"]

    %% EDITAR
    EDIT --> ED1{¿Nueva cantidad = 0?}
    ED1 -->|Sí| ED2["🗑️ Tratar como eliminación"]
    ED1 -->|No| ED3{¿Nueva cantidad <=<br/>stock disponible?}

    ED3 -->|No| ED4["❌ Rechazar<br/>Mostrar máximo disponible"]
    ED3 -->|Sí| ED5["✅ Actualizar cantidad<br/>Limpiar envío/pago/recompensas"]

    ED5 --> ED6{¿Carrito quedó<br/>sin productos?}
    ED6 -->|Sí| ED7["🔁 Redirigir a carrito vacío"]
    ED6 -->|No| ED8["✅ Confirmar actualización"]

    %% ELIMINAR
    DEL --> DL1["🗑️ Quitar producto del carrito"]
    DL1 --> DL2["🧹 Limpiar envío/pago/recompensas"]
    DL2 --> DL3{¿Carrito quedó<br/>sin productos?}

    DL3 -->|Sí| DL4["🔁 Redirigir a carrito vacío"]
    DL3 -->|No| DL5["✅ Confirmar eliminación"]

    style ADD fill:#e1f5ff
    style EDIT fill:#fff3e0
    style DEL fill:#fce4ec
```

---

## Matriz de Validación por Acción

| Acción | Valida existencia | Valida stock | Valida mínimo | Valida opciones | Limpia envío/pago |
|---|---|---|---|---|---|
| **Agregar** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Editar cantidad** | N/A (ya en carrito) | ✅ | ❌ (no revalida mínimo al editar) | ❌ | ✅ |
| **Eliminar** | N/A | N/A | N/A | N/A | ✅ |

---

## Flujos Críticos

### 🔴 Flujo: Cantidad acumulada excede el stock
```
Producto ya tiene 8 unidades en el carrito → stock disponible = 10 →
Usuario intenta agregar 5 más (total 13) → Sistema rechaza →
Indica que solo puede agregar 2 unidades adicionales
```

### 🟡 Flujo: Opción sin stock al agregar
```
Usuario selecciona una opción con descuento de inventario activado →
Opción tiene 0 unidades disponibles → Sistema rechaza el agregado completo →
Indica qué opción específica no tiene stock
```

### 🟢 Flujo: Edición exitosa que vacía el carrito
```
Carrito tiene 1 producto → Usuario reduce cantidad a 0 →
Sistema trata la operación como eliminación → Carrito queda vacío →
Se limpia toda información de checkout previamente calculada
```

---

## Puntos de Tolerancia

### Recalculo delegado a Inventario
El carrito no mantiene su propia lógica de validación de stock — delega completamente en el
módulo de Gestión de Inventario (`InventoryManager`), evitando duplicar reglas de negocio en dos
lugares distintos.

### Limpieza en cascada
Cualquier modificación del carrito (agregar, editar, eliminar) dispara la limpieza de métodos de
envío, pago y recompensas ya calculados, forzando su recálculo en el siguiente paso de checkout.
Esto previene que un cliente pague un costo de envío calculado sobre un carrito distinto al que
finalmente confirma.
