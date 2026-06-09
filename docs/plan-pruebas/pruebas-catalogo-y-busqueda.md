# README de Pruebas: Catálogo y Búsqueda

## Descripción

Este documento contiene una propuesta de **30 casos de prueba** para el módulo **Catálogo y Búsqueda**, diseñados con las técnicas de:

- **PE**: Partición de Equivalencia
- **AVL**: Análisis de Valores Límite

Se presentan en formato tabular para facilitar su uso en documentación QA, pruebas funcionales, validación académica o preparación de casos en herramientas de testing.

---

## Tabla de casos de prueba

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|
| CP-01 | Navegación por categorías | Mostrar categoría existente | `category_id` válido | PE | Se muestra nombre, descripción, imagen y productos de la categoría. |
| CP-02 | Navegación por categorías | Categoría inexistente | `category_id` no existente | PE | El sistema no muestra una categoría válida y deriva a error o no encontrado. |
| CP-03 | Navegación por categorías | Categoría sin subcategorías | Categoría hoja | PE | Se muestra la categoría sin subcategorías listadas. |
| CP-04 | Navegación por categorías | Categoría con múltiples subcategorías | Categoría padre con hijos | PE | Se listan correctamente todas las subcategorías asociadas. |
| CP-05 | Navegación por categorías | Breadcrumb de un nivel | Ruta simple | PE | Se construye el breadcrumb correcto para una ruta de un nivel. |
| CP-06 | Navegación por categorías | Breadcrumb multinivel | Ruta jerárquica | PE | Se construye el breadcrumb completo respetando la jerarquía. |
| CP-07 | Navegación por categorías | Primera página de categoría | `page=1` | AVL | Se muestran los primeros productos de la categoría. |
| CP-08 | Navegación por categorías | Última página de categoría | `page=última` | AVL | Se muestran los últimos productos sin desbordes ni error. |
| CP-09 | Búsqueda | Búsqueda por texto con coincidencias | `search='laptop'` | PE | Se muestran productos relacionados con el texto buscado. |
| CP-10 | Búsqueda | Búsqueda sin coincidencias | `search='xyz_no_existe'` | PE | El sistema muestra resultados vacíos. |
| CP-11 | Búsqueda | Búsqueda vacía | `search=''` | PE | El sistema no devuelve resultados útiles o mantiene comportamiento controlado. |
| CP-12 | Búsqueda | Búsqueda por descripción habilitada | `search='intel'`, `description=1` | PE | Se encuentran productos cuyo texto está en la descripción. |
| CP-13 | Búsqueda | Búsqueda por descripción deshabilitada | `search='intel'`, `description=0` | PE | No se incluyen coincidencias que existan solo en descripción. |
| CP-14 | Búsqueda | Búsqueda por etiquetas | `tag='gaming'` | PE | Se muestran productos relacionados con la etiqueta. |
| CP-15 | Búsqueda | Búsqueda filtrada por categoría | `search='mouse'`, `category_id` válido | PE | Solo se muestran productos de la categoría indicada. |
| CP-16 | Búsqueda | Búsqueda incluyendo subcategorías | `category_id` válido, `sub_category=1` | PE | Se incluyen resultados de la categoría y sus subcategorías. |
| CP-17 | Búsqueda | Búsqueda con longitud mínima | Texto de 1 carácter | AVL | El sistema procesa la búsqueda de forma controlada. |
| CP-18 | Búsqueda | Búsqueda con longitud alta | Texto largo | AVL | El sistema responde sin error y mantiene consistencia en resultados. |
| CP-19 | Ordenamiento | Ordenamiento por defecto | Sin `sort` | PE | Se aplica el orden por defecto del módulo. |
| CP-20 | Ordenamiento | Orden por nombre ascendente | `sort=name`, `order=ASC` | PE | Los productos se muestran ordenados A-Z. |
| CP-21 | Ordenamiento | Orden por nombre descendente | `sort=name`, `order=DESC` | PE | Los productos se muestran ordenados Z-A. |
| CP-22 | Ordenamiento | Orden por precio | `sort=price` | PE | Los productos se ordenan correctamente por precio. |
| CP-23 | Paginación | Límite mínimo por página | `limit=1` | AVL | Se muestra un producto por página. |
| CP-24 | Paginación | Límite máximo configurado | `limit=max` | AVL | El sistema muestra el máximo configurado sin error. |
| CP-25 | Detalle de producto | Visualizar producto existente | `product_id` válido | PE | Se muestra la ficha completa del producto. |
| CP-26 | Detalle de producto | Producto inexistente | `product_id` inválido | PE | El sistema muestra error o no encontrado. |
| CP-27 | Detalle de producto | Producto sin imagen | Producto sin imagen principal | PE | Se utiliza imagen placeholder. |
| CP-28 | Detalle de producto | Precio restringido a cliente autenticado | Usuario no autenticado con política de ocultar precio | PE | El precio no se muestra. |
| CP-29 | Detalle de producto | Cantidad mínima en límite inferior | Producto con `minimum=1` | AVL | El sistema muestra correctamente la cantidad mínima. |
| CP-30 | Detalle de producto | Producto con y sin reseñas | Producto con reseñas / sin reseñas | PE | El sistema muestra el total correcto de reseñas en ambos casos. |

---

## Resumen por técnica

| Técnica | Cantidad |
|---|---:|
| Partición de Equivalencia (PE) | 24 |
| Análisis de Valores Límite (AVL) | 6 |
| **Total** | **30** |

---

## Resumen por componente

| Componente | Cantidad de pruebas |
|---|---:|
| Navegación por categorías | 8 |
| Búsqueda | 10 |
| Ordenamiento y paginación | 6 |
| Detalle de producto | 6 |
| **Total** | **30** |

---

## Observaciones

- Estas pruebas pueden adaptarse como **casos de prueba funcionales**, **pruebas unitarias de controlador/modelo** o **pruebas de aceptación**, según el nivel de implementación.
- Si lo necesitas, el siguiente paso recomendable es convertir esta tabla en una versión con columnas adicionales de:
  - precondiciones
  - pasos
  - datos de prueba
  - resultado obtenido
  - estado
- También puede transformarse a formato para **Excel**, **CSV** o **matriz de QA**.
