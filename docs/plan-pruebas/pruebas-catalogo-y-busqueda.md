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


| ID | Requisito(s) | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|---|
| CP-001 | RF-CAT-031, RF-CAT-032 | Detalle de producto | Mostrar producto válido | `product_id` existente | PE | Se muestra la ficha completa del producto |
| CP-002 | RF-CAT-031, RF-CAT-032 | Detalle de producto | Producto inexistente | `product_id` no existente | PE | El sistema muestra no encontrado o error controlado |
| CP-003 | RF-CAT-032 | Detalle de producto | `product_id` nulo | Sin `product_id` | PE | No se carga un producto válido |
| CP-004 | RF-CAT-033 | Detalle de producto | SEO configurado | Producto con meta title/description/keywords | PE | Se muestran los metadatos SEO |
| CP-005 | RF-CAT-033 | Detalle de producto | SEO vacío | Producto sin meta SEO | PE | La ficha carga sin error |
| CP-006 | RF-CAT-034 | Detalle de producto | URL canónica presente | Producto válido | PE | Se publica link canónico correcto |
| CP-007 | RF-CAT-035 | Detalle de producto | Breadcrumb desde categoría | `path` válido | PE | Breadcrumb correcto desde categoría al producto |
| CP-008 | RF-CAT-035 | Detalle de producto | Breadcrumb desde fabricante | `manufacturer_id` válido | PE | Breadcrumb correcto desde fabricante |
| CP-009 | RF-CAT-035 | Detalle de producto | Breadcrumb desde búsqueda | `search` presente | PE | Breadcrumb correcto desde búsqueda |
| CP-010 | RF-CAT-036 | Detalle de producto | Nombre visible | Producto válido | PE | Se muestra el nombre correcto |
| CP-011 | RF-CAT-037, RF-CAT-052 | Detalle de producto | Mínimo de compra normal | `minimum > 1` | PE | Se muestra mensaje de cantidad mínima requerida |
| CP-012 | RF-CAT-037, RF-CAT-052 | Detalle de producto | Mínimo en límite inferior | `minimum = 1` | AVL | Se muestra el mínimo correcto |
| CP-013 | RF-CAT-038 | Detalle de producto | Precio restringido y usuario invitado | Usuario no autenticado + política activa | PE | Se muestra invitación a login/registro |
| CP-014 | RF-CAT-038, RF-CAT-048 | Detalle de producto | Precio visible para autenticado | Usuario autenticado | PE | Se muestra precio correctamente |
| CP-015 | RF-CAT-039 | Detalle de producto | Producto con reseñas | Total reseñas > 0 | PE | Se muestra cantidad correcta |
| CP-016 | RF-CAT-039, RF-CAT-040 | Detalle de producto | Producto sin reseñas | Total reseñas = 0 | AVL | Se muestra 0 reseñas correctamente |
| CP-017 | RF-CAT-041 | Detalle de producto | Opción con archivo permitida | Producto con opción file | PE | Se permite la carga de archivo |
| CP-018 | RF-CAT-042 | Detalle de producto | Archivo dentro del límite | Tamaño válido | PE | El archivo se acepta |
| CP-019 | RF-CAT-042 | Detalle de producto | Archivo excede límite | Tamaño > máximo | AVL | El sistema rechaza el archivo |
| CP-020 | RF-CAT-043, RF-CAT-044 | Detalle de producto | Fabricante visible | Producto con fabricante asociado | PE | Se muestra fabricante y enlace |
| CP-021 | RF-CAT-043 | Detalle de producto | Producto sin fabricante | Fabricante ausente | PE | El sistema mantiene flujo controlado |
| CP-022 | RF-CAT-045 | Detalle de producto | Modelo visible | Producto con modelo | PE | Se muestra el modelo correcto |
| CP-023 | RF-CAT-046 | Detalle de producto | Códigos adicionales activos | Producto con códigos activos | PE | Se listan los códigos activos |
| CP-024 | RF-CAT-046 | Detalle de producto | Sin códigos adicionales | Producto sin códigos | PE | No se muestran códigos |
| CP-025 | RF-CAT-047 | Detalle de producto | Producto con múltiples imágenes | Galería válida | PE | Se muestran imágenes y ampliación |
| CP-026 | RF-CAT-047 | Detalle de producto | Producto sin imagen | Imagen faltante | PE | Se usa comportamiento visual definido |
| CP-027 | RF-CAT-048 | Detalle de producto | Producto con precio especial | `special` activo | PE | Se muestran precio y precio especial |
| CP-028 | RF-CAT-048, RF-CAT-080 | Detalle de producto | Impuestos habilitados | Config fiscal activa | PE | Se muestran impuestos correctamente |
| CP-029 | RF-CAT-049 | Detalle de producto | Producto con stock disponible | Cantidad > 0 | PE | Se muestra disponibilidad positiva |
| CP-030 | RF-CAT-049 | Detalle de producto | Producto sin stock | Cantidad = 0 | AVL | Se muestra estado de no disponibilidad |
| CP-031 | RF-CAT-050 | Detalle de producto | Mostrar opciones configurables | Producto con opciones | PE | Se listan correctamente las opciones |
| CP-032 | RF-CAT-050 | Detalle de producto | Producto sin opciones | Sin opciones configuradas | PE | No se muestran controles de opciones |
| CP-033 | RF-CAT-051 | Detalle de producto | Producto con suscripción | Planes de suscripción disponibles | PE | Se muestran planes de suscripción |
| CP-034 | RF-CAT-051 | Detalle de producto | Producto sin suscripción | Sin planes | PE | No se muestran planes |
| CP-035 | RF-CAT-053 | Detalle de producto | Agregar a carrito desde detalle | Producto válido | PE | El producto se agrega al carrito |
| CP-036 | RF-CAT-054 | Detalle de producto | Agregar a comparación desde detalle | Producto válido | PE | El producto se agrega a comparación |
| CP-037 | RF-CAT-055 | Detalle de producto | Mostrar relacionados | Producto con relacionados | PE | Se listan productos relacionados |
| CP-038 | RF-CAT-055 | Detalle de producto | Sin relacionados | Producto sin relacionados | PE | No se muestran relacionados |
| CP-039 | RF-CAT-056 | Comparación | Lista de comparación inicial vacía | Sesión sin comparados | PE | La sesión inicia lista vacía o controlada |
| CP-040 | RF-CAT-057 | Comparación | Agregar producto válido | `product_id` existente | PE | Se agrega el producto a la lista |
| CP-041 | RF-CAT-058 | Comparación | Agregar producto inexistente | `product_id` inválido | PE | El sistema rechaza la operación |
| CP-042 | RF-CAT-059 | Comparación | Reagregar producto ya comparado | Producto ya en lista | PE | El producto se reubica al final |
| CP-043 | RF-CAT-060 | Comparación | Límite inferior de lista | 1 producto en comparación | AVL | La lista funciona correctamente |
| CP-044 | RF-CAT-060 | Comparación | Límite máximo permitido | 4 productos en comparación | AVL | La lista conserva 4 productos |
| CP-045 | RF-CAT-061 | Comparación | Superar máximo de comparables | Agregar 5.º producto | AVL | Se elimina el producto más antiguo |
| CP-046 | RF-CAT-062 | Comparación | Mostrar vista comparativa | 2 o más productos válidos | PE | Se muestra tabla comparativa |
| CP-047 | RF-CAT-063 | Comparación | Quitar producto de comparación | `remove=product_id` válido | PE | El producto se elimina de la comparación |
| CP-048 | RF-CAT-064 | Comparación | Ver atributos y datos comparativos | Productos válidos con atributos | PE | Se muestran imagen, precio, disponibilidad, reseñas, peso, dimensiones |
| CP-049 | RF-CAT-065 | Comparación | Atributos agrupados | Productos con grupos de atributos | PE | Se muestran agrupados correctamente |
| CP-050 | RF-CAT-066, RF-CAT-067 | Comparación | Limpiar productos inexistentes y agregar al carrito | Lista con producto borrado + acción carrito | PE | Se elimina el inexistente y se permite agregar al carrito el válido |

---

## Resumen por técnica

| Técnica | Cantidad |
|---|---:|
| Partición de Equivalencia (PE) | 86 |
| Análisis de Valores Límite (AVL) | 14 |
| **Total** | **100** |

---
