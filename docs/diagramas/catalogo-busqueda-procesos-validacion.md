# Diagrama: Procesos de Validación - Catálogo y Búsqueda

## Descripción

Árbol de decisiones para las reglas de visualización de productos: qué se muestra, cuándo se
oculta un precio, y cómo se gestiona el límite de comparación.

---

## Árbol de Decisiones de Validación

```mermaid
graph TD
    START["🚀 Sistema debe mostrar un producto"]

    START --> A{¿status = 1?}
    A -->|No| B["❌ Ocultar del catálogo por completo"]
    A -->|Sí| C{¿date_available <= hoy?}

    C -->|No| D["⏳ Ocultar de listados generales<br/>(no aparece en catálogo/búsqueda)"]
    C -->|Sí| E{¿Habilitado para<br/>la tienda/idioma actual?}

    E -->|No| B
    E -->|Sí| F{¿Tiene imagen válida?}

    F -->|No| G["🖼️ Usar imagen placeholder"]
    F -->|Sí| H["🖼️ Usar imagen real"]

    G --> I{¿Política de cliente<br/>oculta precios sin login?}
    H --> I

    I -->|Sí, no autenticado| J["🔒 Ocultar precio<br/>Mostrar invitación a login"]
    I -->|No, o autenticado| K{¿Configuración fiscal<br/>requiere mostrar impuestos?}

    K -->|Sí| L["💰 Calcular precio + impuestos"]
    K -->|No| M["💰 Mostrar precio base"]

    L --> N{¿Descripción excede<br/>longitud configurada?}
    M --> N

    N -->|Sí| O["✂️ Truncar descripción"]
    N -->|No| P["📄 Mostrar descripción completa"]

    O --> Q{¿Usuario agrega a<br/>comparación?}
    P --> Q

    Q -->|Sí| R{¿Ya hay 4 productos<br/>en comparación?}
    R -->|Sí| S["🔄 Eliminar el más antiguo<br/>Agregar el nuevo al final"]
    R -->|No| T["✅ Agregar a la lista"]

    S --> U{¿Reagregar un producto<br/>ya en la lista?}
    T --> U
    U -->|Sí| V["🔝 Enviar al final de la lista<br/>(reordenar, no duplicar)"]
    U -->|No| W["✅ Comparación actualizada"]

    style B fill:#fce4ec
    style D fill:#fff3e0
    style J fill:#fce4ec
```

---

## Matriz de Validación por Punto

| Punto | Valida | Resultado si falla | Resultado si pasa |
|---|---|---|---|
| **Visibilidad general** | `status`, `date_available`, tienda/idioma | Ocultar del catálogo | Continuar evaluando |
| **Imagen** | Existencia de imagen válida | Usar placeholder | Usar imagen real |
| **Precio** | Política de autenticación | Ocultar + invitar a login | Mostrar precio (con o sin impuestos) |
| **Descripción** | Longitud configurada | Truncar | Mostrar completa |
| **Comparación** | Límite de 4 productos | Eliminar el más antiguo (FIFO) | Agregar directamente |
| **Comparación (reagregar)** | Producto ya en la lista | Reordenar al final | No aplica (ya no está duplicado) |

---

## Flujos Críticos

### 🔴 Flujo: Producto con fecha futura
```
Producto tiene date_available en el futuro → No aparece en catálogo/búsqueda →
Fecha llega → Producto aparece automáticamente en el siguiente listado
```

### 🟡 Flujo: Precio oculto por política de cliente
```
Usuario no autenticado visita detalle de producto → config_customer_price=true →
Precio oculto → Se muestra invitación a login/registro →
Usuario inicia sesión → Precio visible
```

### 🟢 Flujo: Comparación con reemplazo
```
Usuario tiene 4 productos en comparación → Agrega un 5to producto →
Sistema elimina el más antiguo → Nuevo producto se agrega al final →
Comparación sigue mostrando exactamente 4 productos
```

---

## Puntos de Tolerancia

### Productos sin imagen
- Se usa una imagen placeholder configurable, sin bloquear la visualización del producto.

### Búsqueda sin resultados
- No se considera un error: se muestra un mensaje amigable, manteniendo la estructura de la
  página (breadcrumbs, filtros) para que el usuario pueda ajustar su búsqueda.

### Productos eliminados que siguen en comparación
- Si un producto deja de existir mientras está en la lista de comparación de un usuario, se
  limpia automáticamente de la lista en la siguiente consulta.
