# Diagrama: Flujo de Catálogo y Búsqueda

## Descripción

Este diagrama muestra cómo un cliente descubre productos: navegación por categorías, búsqueda
por texto, visualización del detalle de producto, y comparación entre varios productos.

---

## Flujo de Catálogo y Búsqueda

```mermaid
graph TD
    A["👁️ Usuario visita la tienda"] --> B{¿Cómo quiere<br/>encontrar productos?}

    B -->|Navegar categorías| C["📂 Sistema muestra categorías"]
    B -->|Buscar por texto| D["🔍 Usuario escribe término"]
    B -->|Ver marca específica| E["🏷️ Usuario navega por fabricante"]

    C --> F["📋 Sistema construye breadcrumbs"]
    F --> G{¿Categoría tiene<br/>subcategorías?}
    G -->|Sí| H["📁 Mostrar subcategorías<br/>+ cantidad de productos"]
    G -->|No| I["📦 Listar productos<br/>de la categoría"]
    H --> I

    D --> J{¿Buscar también<br/>en descripción/etiquetas?}
    J -->|Configurado| K["🔍 Ampliar búsqueda"]
    J -->|No| L["🔍 Buscar solo en nombre"]
    K --> M["📦 Listar productos<br/>que coinciden"]
    L --> M

    E --> N["📋 Breadcrumbs de fabricante"]
    N --> O["📦 Listar productos<br/>del fabricante"]

    I --> P["🎛️ Aplicar filtro/orden/paginación"]
    M --> P
    O --> P

    P --> Q["🖼️ Mostrar imagen, precio,<br/>disponibilidad por producto"]
    Q --> R{¿Usuario hace clic<br/>en un producto?}

    R -->|Sí| S["📄 Cargar detalle de producto<br/>por product_id"]
    R -->|Agregar a comparación| T["⚖️ Agregar a lista<br/>de comparación (máx. 4)"]

    S --> U["🖼️ Mostrar imágenes, precio,<br/>stock, opciones, fabricante"]
    U --> V{¿Producto requiere<br/>autenticación para ver precio?}

    V -->|Sí, no autenticado| W["🔑 Mostrar invitación<br/>a login/registro"]
    V -->|No, o ya autenticado| X["💰 Mostrar precio y<br/>botón de compra"]

    U --> Y["⭐ Mostrar reseñas<br/>y calificación"]
    U --> Z["🔗 Mostrar productos<br/>relacionados"]

    X --> AA["🛒 Usuario agrega al carrito<br/>desde el detalle"]
    T --> AB["⚖️ Ver comparación<br/>lado a lado"]
    AB --> AC{¿Más de 4 productos<br/>en comparación?}
    AC -->|Sí| AD["🔄 Reemplazar el más antiguo"]
    AC -->|No| AE["✅ Mostrar comparación completa"]
```

---

## Puntos Clave

1. **Navegación jerárquica**: categorías pueden tener subcategorías, con breadcrumbs que
   reflejan la ruta completa hasta el usuario.
2. **Búsqueda configurable**: el alcance de la búsqueda (solo nombre, o también descripción y
   etiquetas) depende de la configuración de la tienda.
3. **Precios condicionados a autenticación**: si la política de la tienda lo requiere, el precio
   se oculta hasta que el usuario inicia sesión.
4. **Comparación limitada a 4 productos**: al superar el límite, se descarta el producto más
   antiguo de la lista automáticamente.
5. **Consistencia con reglas de visualización**: solo se muestran productos activos, vigentes
   (fecha de disponibilidad) y habilitados para la tienda/idioma actual.

---

## Escenarios Cubiertos

- ✅ Navegación por categoría con subcategorías
- ✅ Búsqueda por texto con y sin resultados
- ✅ Búsqueda ampliada a descripción/etiquetas
- ✅ Visualización de detalle de producto completo
- ✅ Ocultar precio para usuarios no autenticados
- ✅ Agregar y ver productos en comparación
- ✅ Reemplazo automático al superar el límite de comparación
- ✅ Navegación por fabricante/marca
