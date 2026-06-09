# Pruebas: Catálogo y Búsqueda

## Descripción

Este documento contiene una propuesta de **50 casos de prueba** para el módulo **Catálogo y Búsqueda**, diseñados con las técnicas de:

- **PE**: Partición de Equivalencia
- **AVL**: Análisis de Valores Límite

Se presentan en formato tabular para facilitar su uso en documentación QA, pruebas funcionales, validación académica o preparación de casos en herramientas de testing.

---


## Tabla de casos de prueba

| ID | Requisito(s) | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|---|
| CP-001 | RF-CAT-001 | Navegación por catálogo | Mostrar categorías existentes | Catálogo con categorías activas | PE | Se muestran todas las categorías activas esperadas. |
| CP-002 | RF-CAT-001 | Navegación por catálogo | Catálogo sin categorías | No existen categorías activas | PE | No se muestran categorías y el sistema responde sin error. |
| CP-003 | RF-CAT-002 | Navegación por catálogo | Navegación a subcategoría válida | Categoría padre con subcategoría | PE | Se accede correctamente a la subcategoría. |
| CP-004 | RF-CAT-002 | Navegación por catálogo | Navegación jerárquica profunda | Ruta con varios niveles | PE | Se respeta la jerarquía completa. |
| CP-005 | RF-CAT-003 | Navegación por catálogo | Breadcrumb de categoría raíz | Categoría de primer nivel | PE | Se construye el breadcrumb correcto desde inicio a categoría. |
| CP-006 | RF-CAT-003 | Navegación por catálogo | Breadcrumb multinivel | Ruta de 3 o más niveles | PE | Se construye el breadcrumb completo respetando la jerarquía. |
| CP-007 | RF-CAT-004 | Navegación por catálogo | Categoría con datos completos | Nombre, descripción e imagen cargados | PE | Se visualizan correctamente todos los campos de la categoría. |
| CP-008 | RF-CAT-004, RF-CAT-077 | Navegación por catálogo | Categoría sin imagen | Imagen nula o faltante | PE | El sistema mantiene el flujo visual esperado sin error. |
| CP-009 | RF-CAT-005 | Navegación por catálogo | Meta SEO configurado | Meta title, description y keywords válidos | PE | Se renderizan correctamente los metadatos SEO. |
| CP-010 | RF-CAT-005 | Navegación por catálogo | Meta SEO ausente | Metadatos vacíos | PE | La categoría carga sin error aunque no existan metadatos. |
| CP-011 | RF-CAT-006 | Navegación por catálogo | Categoría con subcategorías | Varias subcategorías asociadas | PE | Se listan todas las subcategorías esperadas. |
| CP-012 | RF-CAT-006 | Navegación por catálogo | Categoría hoja | Sin subcategorías | PE | La lista de subcategorías aparece vacía. |
| CP-013 | RF-CAT-007 | Navegación por catálogo | Conteo habilitado | Configuración activa + subcategorías con productos | PE | Se muestra el conteo de productos por subcategoría. |
| CP-014 | RF-CAT-007 | Navegación por catálogo | Conteo deshabilitado | Configuración inactiva | PE | No se muestra el conteo de productos. |
| CP-015 | RF-CAT-008 | Navegación por catálogo | Filtrar productos en categoría | `filter` válido | PE | Solo aparecen productos que cumplen el filtro aplicado. |
| CP-016 | RF-CAT-008 | Navegación por catálogo | Filtro sin coincidencias | `filter` sin coincidencias | PE | La lista de productos queda vacía sin error. |
| CP-017 | RF-CAT-009 | Navegación por catálogo | Ordenamiento por defecto | Sin `sort` | PE | Se aplica el orden por defecto del módulo. |
| CP-018 | RF-CAT-009 | Navegación por catálogo | Ordenar por nombre ascendente | `sort=name`, `order=ASC` | PE | Los productos se muestran ordenados A-Z. |
| CP-019 | RF-CAT-009 | Navegación por catálogo | Ordenar por precio descendente | `sort=price`, `order=DESC` | PE | Los productos se muestran ordenados por precio descendente. |
| CP-020 | RF-CAT-010 | Navegación por catálogo | Primera página de categoría | `page=1` | AVL | Se muestran los primeros productos de la categoría. |
| CP-021 | RF-CAT-010 | Navegación por catálogo | Última página de categoría | `page=última` | AVL | Se muestran los últimos productos sin error. |
| CP-022 | RF-CAT-010 | Navegación por catálogo | Página fuera de rango superior | `page=última+1` | AVL | El sistema responde sin fallar y mantiene comportamiento controlado. |
| CP-023 | RF-CAT-011 | Navegación por catálogo | Límite mínimo por página | `limit=1` | AVL | Se muestra un solo producto por página. |
| CP-024 | RF-CAT-011 | Navegación por catálogo | Límite alto por página | `limit=max permitido` | AVL | Se muestran los productos hasta el máximo permitido sin error. |
| CP-025 | RF-CAT-012 | Navegación por catálogo | Enlace a comparación visible | Vista de categoría estándar | PE | Se muestra el enlace a comparación de productos. |
| CP-026 | RF-CAT-013 | Búsqueda de productos | Búsqueda por texto con coincidencias | `search='laptop'` | PE | Se retornan productos coincidentes con el texto. |
| CP-027 | RF-CAT-013 | Búsqueda de productos | Búsqueda sin coincidencias | `search='xyz_no_existe'` | PE | No se devuelven resultados. |
| CP-028 | RF-CAT-013 | Búsqueda de productos | Búsqueda vacía | `search=''` | PE | El sistema responde de forma controlada sin romper el flujo. |
| CP-029 | RF-CAT-013 | Búsqueda de productos | Búsqueda con longitud mínima | Texto de 1 carácter | AVL | El sistema procesa la búsqueda sin error. |
| CP-030 | RF-CAT-013 | Búsqueda de productos | Búsqueda con longitud extensa | Texto muy largo | AVL | El sistema procesa la búsqueda sin error y mantiene consistencia. |
| CP-031 | RF-CAT-014 | Búsqueda de productos | Búsqueda en descripción habilitada | `search='intel'`, `description=1` | PE | Se encuentran productos cuyo texto aparece en descripción. |
| CP-032 | RF-CAT-014 | Búsqueda de productos | Búsqueda en descripción deshabilitada | `search='intel'`, `description=0` | PE | No se incluyen coincidencias que existan solo en descripción. |
| CP-033 | RF-CAT-015 | Búsqueda de productos | Búsqueda por etiquetas válida | `tag='gaming'` | PE | Se muestran productos relacionados con la etiqueta. |
| CP-034 | RF-CAT-015 | Búsqueda de productos | Búsqueda por etiqueta inexistente | `tag='noexiste'` | PE | El sistema retorna lista vacía. |
| CP-035 | RF-CAT-016 | Búsqueda de productos | Búsqueda limitada a categoría válida | `search='mouse'`, `category_id` válido | PE | Solo aparecen productos de la categoría indicada. |
| CP-036 | RF-CAT-016 | Búsqueda de productos | Búsqueda con categoría inexistente | `category_id` inválido | PE | No hay resultados o el sistema responde de forma controlada. |
| CP-037 | RF-CAT-017 | Búsqueda de productos | Búsqueda incluyendo subcategorías | `sub_category=1` | PE | Se incluyen productos de la categoría y sus subcategorías. |
| CP-038 | RF-CAT-017 | Búsqueda de productos | Búsqueda sin subcategorías | `sub_category=0` | PE | Solo se incluyen productos de la categoría principal. |
| CP-039 | RF-CAT-018 | Búsqueda de productos | Mostrar término en título | `search='mouse'` | PE | El título incluye el término buscado. |
| CP-040 | RF-CAT-018 | Búsqueda de productos | Mostrar término en encabezado | `search='mouse'` | PE | El encabezado incluye el término buscado. |
| CP-041 | RF-CAT-019 | Búsqueda de productos | Breadcrumb del flujo de búsqueda | Búsqueda simple | PE | Se construye correctamente el breadcrumb de búsqueda. |
| CP-042 | RF-CAT-020 | Búsqueda de productos | Mostrar árbol de categorías hasta 3 niveles | Estructura con 3 niveles | PE | Se muestran correctamente los tres niveles. |
| CP-043 | RF-CAT-020 | Búsqueda de productos | Estructura superior al límite visible | Estructura mayor a 3 niveles | AVL | El sistema mantiene el límite implementado de apoyo visual. |
| CP-044 | RF-CAT-021 | Búsqueda de productos | Listar productos coincidentes | Búsqueda con múltiples coincidencias | PE | Se listan todos los productos esperados. |
| CP-045 | RF-CAT-022, RF-CAT-077 | Búsqueda de productos | Mostrar imagen en resultados | Producto con imagen válida | PE | Se visualiza la imagen del producto. |
| CP-046 | RF-CAT-022, RF-CAT-077 | Búsqueda de productos | Producto sin imagen en resultados | Imagen faltante | PE | Se usa placeholder o comportamiento visual definido. |
| CP-047 | RF-CAT-023, RF-CAT-078 | Búsqueda de productos | Descripción resumida | Descripción larga | PE | La descripción aparece truncada según la configuración. |
| CP-048 | RF-CAT-024, RF-CAT-025, RF-CAT-079, RF-CAT-080 | Búsqueda de productos | Mostrar precio, especial e impuestos | Producto con precio especial e impuestos habilitados | PE | Se muestran correctamente precio, precio especial e impuestos. |
| CP-049 | RF-CAT-026, RF-CAT-027, RF-CAT-028 | Búsqueda de productos | Ordenar, paginar y limitar resultados | `sort`, `page`, `limit` válidos | PE | Los resultados responden correctamente a orden, página y límite. |
| CP-050 | RF-CAT-029, RF-CAT-030 | Búsqueda de productos | Mantener filtros y acceso a comparación | Navegación entre páginas/ordenamientos con filtros activos | PE | Se conservan filtros y se mantiene visible el acceso a comparación. |

---

## Resumen por técnica

| Técnica | Cantidad |
|---|---:|
| Partición de Equivalencia (PE) | 42 |
| Análisis de Valores Límite (AVL) | 8 |
| **Total** | **50** |

---
