# Diagrama: Arquitectura del Módulo - Catálogo y Búsqueda

## Descripción

Este diagrama muestra la arquitectura del módulo de Catálogo y Búsqueda, sus componentes,
entidades de base de datos y relaciones.

---

## Arquitectura de Componentes

```mermaid
graph TB
    subgraph "🎨 Capa de Presentación (Frontend)"
        CATV["📂 Vista de Categoría"]
        SEARCHV["🔍 Vista de Búsqueda"]
        PRODV["📄 Vista de Detalle"]
        COMPV["⚖️ Vista de Comparación"]
        MANUFV["🏷️ Vista de Fabricante"]
    end

    subgraph "🧠 Capa de Lógica de Negocio"
        CATMAN["📂 Catalog Manager"]
        SEARCHENG["🔍 Search Engine"]
        COMPMAN["⚖️ Comparison Manager"]
        PRICECALC["🧮 Price Calculator"]
        DISPLAYRULES["🎛️ Display Rules Engine"]
    end

    subgraph "💾 Capa de Datos"
        PRODDB["📊 product<br/>id, model, price, status"]
        DESCDB["📊 product_description<br/>name, description, tag"]
        CATDB["📊 category<br/>id, parent_id, status"]
        MANUFDB["📊 manufacturer<br/>id, name"]
        IMGDB["📊 product_image<br/>product_id, image"]
        RELDB["📊 product_related<br/>product_id, related_id"]
    end

    CATV --> CATMAN
    SEARCHV --> SEARCHENG
    PRODV --> CATMAN
    PRODV --> DISPLAYRULES
    COMPV --> COMPMAN
    MANUFV --> CATMAN

    CATMAN --> DISPLAYRULES
    SEARCHENG --> DISPLAYRULES
    DISPLAYRULES --> PRICECALC

    CATMAN --> PRODDB
    CATMAN --> CATDB
    CATMAN --> MANUFDB
    SEARCHENG --> DESCDB
    CATMAN --> IMGDB
    CATMAN --> RELDB
    COMPMAN --> PRODDB
```

---

## Flujo de Datos

```mermaid
graph LR
    A["👁️ Usuario navega/busca"]
    B["📂 Catalog Manager /<br/>🔍 Search Engine"]
    C["🎛️ Display Rules Engine<br/>filtra activos/vigentes"]
    D["💾 product, category,<br/>manufacturer"]
    E["🧮 Price Calculator<br/>aplica impuestos/política"]
    F["📱 Frontend renderiza<br/>listado o detalle"]

    A --> B
    B --> D
    B --> C
    C --> D
    C --> E
    E --> F

    G["⚖️ Usuario agrega a comparación"]
    F --> G
    G --> H["⚖️ Comparison Manager<br/>gestiona límite de 4"]
    H --> I["✅ Vista comparativa actualizada"]
```

---

## Componentes Clave

### 📂 Catalog Manager
**Responsabilidad**: Navegación y listado de productos
- Construir jerarquía de categorías y breadcrumbs
- Listar productos por categoría o fabricante
- Aplicar filtros, orden y paginación

### 🔍 Search Engine
**Responsabilidad**: Búsqueda de productos por texto
- Buscar en nombre, y opcionalmente descripción/etiquetas
- Soportar limitación por categoría y subcategorías
- Mantener filtros al cambiar orden/página/límite

### ⚖️ Comparison Manager
**Responsabilidad**: Gestión de la lista de comparación en sesión
- Agregar/quitar productos (máximo 4)
- Reemplazar el más antiguo al superar el límite (FIFO)
- Limpiar productos que ya no existen

### 🎛️ Display Rules Engine
**Responsabilidad**: Reglas de visualización consistentes
- Filtrar productos inactivos, no vigentes o no habilitados
- Decidir si mostrar precio según autenticación
- Truncar descripciones largas, usar imagen placeholder

### 🧮 Price Calculator
**Responsabilidad**: Cálculo de precio final mostrado
- Aplicar precio especial/descuento si corresponde
- Sumar impuestos según configuración fiscal
- Ocultar el precio si la política de cliente lo requiere

---

## Integraciones

```mermaid
graph TD
    CAT["Catálogo y Búsqueda"]

    CAT --> INV["Gestión de Inventario<br/>Consulta stock y disponibilidad"]
    CAT --> CART["Carrito<br/>Agregar producto desde detalle"]
    CAT --> REVIEWS["Reseñas<br/>Muestra calificación y total"]
    CAT --> AUTH["Login y Registro<br/>Oculta precio sin autenticación"]
    CAT --> ADMIN["Administración<br/>Edición de productos/categorías"]
```

---

## Configuraciones del Módulo

```
config_catalog:
  ├── config_limit (int) — Productos por página por defecto
  ├── config_product_description_length (int) — Longitud antes de truncar
  ├── config_customer_price (bool) — Ocultar precios sin autenticación
  ├── config_tax (bool) — Mostrar impuestos en listados/detalle
  └── config_compare (int) — Máximo de productos en comparación (default 4)

config_search:
  ├── search_description (bool) — Incluir descripción en la búsqueda
  └── search_tag (bool) — Incluir etiquetas en la búsqueda
```

---

## Seguridad y Validación

- ✅ **Filtrado consistente**: solo productos activos, vigentes y habilitados aparecen en
  cualquier vista pública
- ✅ **Resistencia a inyección SQL**: verificado en `CatalogTest.php::testSearchProductsSqlInjection`
  y en el checklist de seguridad no funcional (ver
  [`tests/no-funcionales/seguridad/checklist-owasp.md`](../../tests/no-funcionales/seguridad/checklist-owasp.md))
- ✅ **Búsqueda case-insensitive**: verificado en `testSearchProductsCaseFold`
- ⚠️ **Marcas/Fabricantes sin pruebas unitarias dedicadas**: ver observación en
  [4.2.4 — Resultado de pruebas de Catálogo y Búsqueda](https://github.com/DanLAQP/QA-OpenCart-Testing/wiki/4.2.4-Resultado-de-pruebas-Catalogo-Busqueda)
  de la wiki del proyecto
