# Pruebas: Gestión de Inventario

## Descripción

Este documento contiene una propuesta de **60 casos de prueba** para el módulo **Gestión de Inventario**, diseñados con las técnicas de:

- **PE**: Partición de Equivalencia
- **AVL**: Análisis de Valores Límite

Se presentan en formato tabular para facilitar su uso en documentación QA, pruebas funcionales, validación académica o preparación de casos en herramientas de testing.

---

## 1. Stock de Producto (RF-INV-001 al RF-INV-006)

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|
| CP-INV-001 | Stock de producto | Almacenar cantidad disponible | Producto con `quantity=50` en BD | PE | La cantidad se almacena correctamente en la tabla `product` campo `quantity`. |
| CP-INV-002 | Stock de producto | Editar cantidad disponible | Cambiar `quantity=50` a `quantity=30` vía administración | PE | La cantidad se actualiza en BD y el catálogo refleja el nuevo valor. |
| CP-INV-003 | Stock de producto | Validación de disponibilidad en frontend | Producto con `quantity=0` | PE | Se deshabilita botón de compra, se muestra estado "Sin stock". |
| CP-INV-004 | Stock de producto | Validación en frontend con stock positivo | Producto con `quantity=5` | PE | Se habilita botón de compra y permite agregar al carrito. |
| CP-INV-005 | Stock de producto | Impedir compra sin stock (config deshabilitada) | `allow_out_of_stock=false`, `quantity=0` | PE | Sistema rechaza agregar al carrito con mensaje de error. |
| CP-INV-006 | Stock de producto | Permitir compra sin stock (config habilitada) | `allow_out_of_stock=true`, `quantity=0` | PE | Sistema permite agregar al carrito a pesar de cantidad 0. |
| CP-INV-007 | Stock de producto | Cantidad mínima de compra | Producto con `minimum=5`, intento de agregar `quantity=3` | AVL | Sistema rechaza agregación, muestra mensaje de cantidad mínima requerida. |
| CP-INV-008 | Stock de producto | Cantidad mínima exacta | Producto con `minimum=5`, agregar `quantity=5` | AVL | Sistema acepta la cantidad, permite agregar al carrito. |
| CP-INV-009 | Stock de producto | Cantidad mínima por debajo del límite | Producto con `minimum=5`, `quantity=4` en stock | AVL | Producto no disponible, no se permite compra aunque tenga stock. |
| CP-INV-010 | Estado del producto | Producto activo en tienda | Producto con `status=1` y `date_available` en el pasado | PE | Producto visible en catálogo y disponible para compra. |
| CP-INV-011 | Estado del producto | Producto inactivo en tienda | Producto con `status=0` | PE | Producto no visible en catálogo, no disponible para compra. |
| CP-INV-012 | Disponibilidad por fecha | Producto futuro no disponible | Producto con `date_available` en 30 días | AVL | Producto visible pero deshabilitado para compra, muestra fecha de disponibilidad. |
| CP-INV-013 | Disponibilidad por fecha | Producto disponible hoy | Producto con `date_available` igual a hoy | AVL | Producto visible y habilitado para compra. |

---

## 2. Variantes y Opciones con Impacto en Inventario (RF-INV-007 al RF-INV-013)

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|
| CP-INV-014 | Variantes de producto | Crear variante de producto maestro | Producto maestro ID 1, crear variante con `sku='PROD-RED-S'` | PE | Variante se crea como registro dependiente del maestro. |
| CP-INV-015 | Producto maestro/variante | Producto maestro sin compra directa | Maestro con variantes, intento de agregar maestro al carrito | PE | Sistema rechaza, exige seleccionar una variante específica. |
| CP-INV-016 | Producto maestro/variante | Compra de variante específica | Maestro con variante `id=101`, agregar variante al carrito | PE | Se agrega variante `id=101`, no el maestro. |
| CP-INV-017 | Opciones derivadas de variantes | Mezclar opciones con override | Variante con opciones base + override adicional | PE | Se combinan opciones, override reemplaza base donde corresponde. |
| CP-INV-018 | Opciones derivadas de variantes | Sin override, usar opciones base | Variante con opciones base, sin override | PE | Se usan opciones base de la variante. |
| CP-INV-019 | Stock de opción | Stock a nivel de opción (descuento activo) | Opción `Talla=S` con `quantity=10`, descuento de inventario habilitado | PE | Stock se valida a nivel de opción, no solo producto. |
| CP-INV-020 | Stock de opción | Sin descuento de inventario | Opción `Talla=S` con descuento deshabilitado | PE | Stock no se descuenta por opción, se usa stock del producto. |
| CP-INV-021 | Validación de opciones select | Opción select con stock | `type='select'`, `Talla=[S:qty=5, M:qty=10]` | PE | Cada valor de select tiene su propio stock. |
| CP-INV-022 | Validación de opciones radio | Opción radio con stock | `type='radio'`, `Color=[Rojo:qty=3, Azul:qty=7]` | PE | Cada valor de radio tiene su propio stock. |
| CP-INV-023 | Validación de opciones checkbox | Opción checkbox con stock | `type='checkbox'`, `Accesorios=[Protector:qty=5, Cable:qty=8]` | PE | Cada valor de checkbox tiene su propio stock independiente. |
| CP-INV-024 | Acumulación de precio | Opción suma precio | Producto base=100, opción `Talla=XL` suma 10 | PE | Precio final = 110, acumulación correcta. |
| CP-INV-025 | Acumulación de puntos | Opción suma puntos | Producto base=50 puntos, opción suma 5 | PE | Puntos finales = 55, acumulación correcta. |
| CP-INV-026 | Acumulación de peso | Opción suma peso | Producto base=1kg, opción suma 0.5kg | PE | Peso final = 1.5kg, acumulación correcta. |
| CP-INV-027 | Marcar sin stock por opción | Opción requerida sin stock | Opción requerida `Talla=S` con `quantity=0` | PE | Producto marcado como sin stock aunque tenga cantidad general. |
| CP-INV-028 | Marcar sin stock múltiples opciones | Todas las opciones sin stock | Opción única `Talla` con todos los valores sin stock | PE | Producto sin stock, no se permite compra. |

---

## 3. Validación de Inventario en Carrito y Checkout (RF-INV-014 al RF-INV-019)

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|
| CP-INV-029 | Validación en carrito | Agregar producto con stock | `product_id=5`, `quantity=2`, stock disponible=10 | PE | Producto agregado al carrito exitosamente. |
| CP-INV-030 | Validación en carrito | Agregar producto sin stock | `product_id=5`, `quantity=2`, stock disponible=0 | PE | Sistema rechaza agregación si `allow_out_of_stock=false`. |
| CP-INV-031 | Validación en carrito | Cantidad mayor a stock disponible | `quantity=15`, stock=10, `allow_out_of_stock=false` | AVL | Sistema rechaza, muestra cantidad máxima disponible. |
| CP-INV-032 | Validación en carrito | Cantidad exacta a stock disponible | `quantity=10`, stock=10 | AVL | Sistema acepta la cantidad exacta. |
| CP-INV-033 | Validación en carrito | Opción sin stock al agregar | `option_id=3` (Talla=S), stock de opción=0 | PE | Sistema rechaza si la opción requiere stock. |
| CP-INV-034 | Validación en carrito | Opción con stock suficiente | `option_id=3`, cantidad requerida=2, stock=5 | PE | Sistema acepta, valida stock de opción. |
| CP-INV-035 | Suma total en carrito | Múltiples items del mismo producto | Producto ID=5, agregado 2 veces, 3 units cada una = 6 total | PE | Sistema suma correctamente: 6 units del mismo producto. |
| CP-INV-036 | Suma total en carrito | Stock total vs. carrito | Producto con stock=5, carrito ya tiene 3, intenta agregar 3 más | AVL | Sistema rechaza la segunda agregación, solo permite 2 más. |
| CP-INV-037 | Cantidad mínima en checkout | Cantidad en carrito menor a mínimo | `minimum=5`, carrito tiene 3 items | PE | Checkout rechaza, solicita cantidad mínima. |
| CP-INV-038 | Cantidad mínima en checkout | Cantidad en carrito igual a mínimo | `minimum=5`, carrito tiene 5 items | PE | Checkout procede sin error. |
| CP-INV-039 | Recálculo en checkout | Stock varía durante checkout | Stock inicial=10, en carrito=8, antes de confirmar stock=6 | PE | Sistema valida stock nuevamente, rechaza si cantidad excede. |
| CP-INV-040 | Recálculo en confirmación | Stock se reserva al confirmar | Orden en proceso de confirmación, stock se resta | PE | Stock disminuye solo tras confirmación exitosa. |
| CP-INV-041 | Reconstrucción de carrito | Carrito se reconstruye desde sesión | Usuario cierra sesión con carrito, vuelve a acceder | PE | Carrito se reconstruye y stock se valida nuevamente. |
| CP-INV-042 | Reconstrucción de carrito | Carrito inválido tras cambios de stock | Carrito obsoleto, producto sin stock ahora | PE | Sistema notifica cambios, elimina items no disponibles. |

---

## 4. Validación de Inventario vía API (RF-INV-020 al RF-INV-025)

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|
| CP-INV-043 | Validación API | Existencia de producto en API | GET `/api/products/5` | PE | API devuelve producto si existe, 404 si no existe. |
| CP-INV-044 | Validación API | Stock del producto por API | GET `/api/products/5?include=stock` | PE | Respuesta incluye campo `quantity` con valor actual. |
| CP-INV-045 | Validación API | Stock de opciones por API | GET `/api/products/5/options/3` | PE | Respuesta incluye stock de cada valor de opción. |
| CP-INV-046 | Validación API | Stock insuficiente en POST carrito | POST `/api/cart/items` con `quantity=15`, stock=10 | PE | API rechaza con código 400, mensaje de stock insuficiente. |
| CP-INV-047 | Validación API | Cantidad mínima por API | POST `/api/cart/items` con `quantity=2`, `minimum=5` | PE | API rechaza, solicita cantidad mínima. |
| CP-INV-048 | Validación API | Confirmación con stock insuficiente | POST `/api/orders` con `quantity=8`, stock=5 | PE | API rechaza confirmación antes de crear orden. |
| CP-INV-049 | Validación API | Consideración de orden existente | PATCH `/api/orders/123/items/1` con cantidad adicional | PE | Sistema considera stock ya comprometido en la orden. |
| CP-INV-050 | Validación API | Errores estructurados por producto | POST `/api/cart/items` con múltiples productos, uno sin stock | PE | Respuesta diferencia errores por `product_id` u `option_id`. |
| CP-INV-051 | Validación API | Errores por opción específica | Opción sin stock en respuesta de error | PE | Error especifica `option_id` y valor problemático. |

---

## 5. Estados de Stock y Disponibilidad (RF-INV-026 al RF-INV-029)

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|
| CP-INV-052 | Estado de stock | Producto sin cantidad, con estado | `quantity=0`, `stock_status_id=5` ("No disponible") | PE | Se muestra estado de stock en lugar de cantidad. |
| CP-INV-053 | Estado de stock | Producto sin cantidad, sin estado asignado | `quantity=0`, `stock_status_id=null` | PE | Se asigna estado por defecto o se muestra genérico. |
| CP-INV-054 | Mostrar disponibilidad | Config: mostrar cantidad real | `config_stock_display='quantity'`, producto con qty=7 | PE | Frontend muestra "7 en stock". |
| CP-INV-055 | Mostrar disponibilidad | Config: mostrar solo estado | `config_stock_display='status'`, producto qty=7 | PE | Frontend muestra "En stock", no la cantidad. |
| CP-INV-056 | Mostrar disponibilidad | Config: cantidad y estado | `config_stock_display='both'`, qty=7, status="Disponible" | PE | Frontend muestra ambos datos. |
| CP-INV-057 | Identificador de estado | Stock nulo usa ID de estado | `quantity=0`, `stock_status_id=4` | PE | Sistema usa el registro de estado para mostrar texto de stock. |
| CP-INV-058 | Visualización configurable | Stock visible en catálogo | `config_show_stock_catalog=1`, producto qty=3 | PE | Catálogo muestra cantidad en cada producto. |
| CP-INV-059 | Visualización en comparación | Stock visible en comparación | `config_show_stock_compare=1` | PE | Página de comparación muestra stock de cada producto. |
| CP-INV-060 | Disponibilidad en comparación | Productos no disponibles en comparación | Producto sin stock, agregado a comparación | PE | Se muestra estado "Sin stock" en comparativa. |

---

## 6. Administración y Consulta de Inventario (RF-INV-030 al RF-INV-035)

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|
| CP-INV-061 | Listado de inventario | Listar productos con cantidad | Acceder a admin de productos | PE | Cada producto muestra su cantidad en listado. |
| CP-INV-062 | Filtro por cantidad | Filtrar productos en rango de qty | `quantity_min=5`, `quantity_max=20` | PE | Se muestran solo productos cuya cantidad está en el rango. |
| CP-INV-063 | Filtro por cantidad mínima | Filtrar productos sin stock | `quantity_max=0` | AVL | Se listan solo productos con cantidad 0. |
| CP-INV-064 | Filtro por cantidad máxima | Filtrar productos con alto stock | `quantity_min=100` | AVL | Se listan solo productos con cantidad >= 100. |
| CP-INV-065 | Ordenar por cantidad | Orden ascendente de cantidad | Click en columna "Cantidad", orden ASC | PE | Productos ordenados de menor a mayor cantidad. |
| CP-INV-066 | Ordenar por cantidad | Orden descendente de cantidad | Click segundo en columna "Cantidad", orden DESC | PE | Productos ordenados de mayor a menor cantidad. |
| CP-INV-067 | Mostrar variantes | Listar productos maestros con variantes | Admin de productos | PE | Se muestran productos maestros y se puede expandir para ver variantes. |
| CP-INV-068 | Acceso a mantenimiento | Acceder a edición de variantes | Click en botón "Editar variantes" de un maestro | PE | Se abre vista de gestión de variantes. |
| CP-INV-069 | Distinguir maestros/variantes | Identificar producto maestro en listado | Columna o icono indicador | PE | Producto maestro se identifica claramente. |
| CP-INV-070 | Distinguir maestros/variantes | Identificar variante en listado | Columna o icono indicador, indentación | PE | Variante se identifica como dependiente del maestro. |

---

## 7. Integración con Catálogo y Búsqueda (RF-INV-036 al RF-INV-038)

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|
| CP-INV-071 | Stock en comparación | Producto con stock en comparativa | Producto activo, stock=5, en comparación | PE | Stock se muestra correctamente en tabla comparativa. |
| CP-INV-072 | Disponibilidad en comparación | Producto sin stock en comparativa | Producto sin stock, en comparación | PE | Se muestra estado "Sin stock" en la comparativa. |
| CP-INV-073 | Catálogo filtra activos | Catálogo muestra solo activos | Buscar productos en catálogo | PE | Se muestran solo productos con `status=1`. |
| CP-INV-074 | Catálogo filtra por fecha | Catálogo muestra solo vigentes | Producto con `date_available` futuro | PE | Producto no visible en catálogo hasta su fecha. |
| CP-INV-075 | Catálogo filtra por tienda | Catálogo respeta habilitación de tienda | Producto deshabilitado para una tienda | PE | Producto no visible en esa tienda específica. |
| CP-INV-076 | Cantidad en detalle | Detalle de producto muestra cantidad | Acceder a producto activo con qty=12 | PE | Página de detalle muestra cantidad disponible. |
| CP-INV-077 | Disponibilidad en detalle | Detalle muestra disponibilidad | Producto sin stock en detalle | PE | Se muestra estado de disponibilidad en detalle. |
| CP-INV-078 | Opciones en detalle | Opciones con stock en detalle | Detalle con opciones que descuentan inventario | PE | Cada opción muestra su disponibilidad según stock. |
| CP-INV-079 | Búsqueda incluye disponibilidad | Búsqueda filtra por stock | Búsqueda avanzada, filtro "En stock" | PE | Resultados muestran solo productos disponibles. |
| CP-INV-080 | Cantidad se conserva en detalle | Cantidad actualizada en detalle | Cambio de stock en admin, recarga en frontend | PE | Detalle refleja cantidad actualizada. |

---

## Resumen por técnica

| Técnica | Cantidad |
|---|---:|
| Partición de Equivalencia (PE) | 62 |
| Análisis de Valores Límite (AVL) | 18 |
| **Total** | **80** |

---
