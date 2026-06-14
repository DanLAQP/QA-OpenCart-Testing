# Pruebas: Sistema de Reseñas

## Descripción

Este documento contiene una propuesta de **65 casos de prueba** para el módulo **Sistema de Reseñas**, diseñados con las técnicas de:

- **PE**: Partición de Equivalencia
- **AVL**: Análisis de Valores Límite

Se presentan en formato tabular para facilitar su uso en documentación QA, validación académica, matriz de pruebas o preparación de casos en herramientas de testing.

---

## Tabla de casos de prueba

| ID | Requisito(s) | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|---|
| CP-REV-001 | RF-REV-001 | Publicación y visualización frontend | Mostrar bloque de reseñas de producto válido | `product_id` existente | PE | Se muestra el bloque de reseñas asociado al producto. |
| CP-REV-002 | RF-REV-001, RF-REV-002 | Publicación y visualización frontend | Producto inexistente en bloque de reseñas | `product_id` inválido | PE | No se muestra información válida de reseñas. |
| CP-REV-003 | RF-REV-002 | Publicación y visualización frontend | `product_id` ausente | Sin `product_id` | PE | El sistema no consulta reseñas válidas. |
| CP-REV-004 | RF-REV-003 | Publicación y visualización frontend | Producto con reseñas existentes | Producto con reseñas publicadas | PE | Se muestra listado de reseñas. |
| CP-REV-005 | RF-REV-003 | Publicación y visualización frontend | Producto sin reseñas | Producto sin reseñas | PE | Se muestra lista vacía sin error. |
| CP-REV-006 | RF-REV-004 | Publicación y visualización frontend | Paginación estándar de reseñas | 6 reseñas publicadas | PE | Se muestran 5 en la primera página. |
| CP-REV-007 | RF-REV-004 | Publicación y visualización frontend | Límite exacto por página | 5 reseñas | AVL | Se muestran exactamente 5 reseñas. |
| CP-REV-008 | RF-REV-004 | Publicación y visualización frontend | Límite inferior de paginación | 1 reseña | AVL | Se muestra 1 reseña correctamente. |
| CP-REV-009 | RF-REV-004 | Publicación y visualización frontend | Página siguiente | `page=2`, total > 5 | AVL | Se muestran reseñas restantes. |
| CP-REV-010 | RF-REV-005 | Publicación y visualización frontend | Mostrar autor, texto, rating y fecha | Reseña válida publicada | PE | Se visualizan todos los campos esperados. |
| CP-REV-011 | RF-REV-006 | Publicación y visualización frontend | Mostrar total de reseñas | Producto con total > 0 | PE | El total coincide con el número de reseñas publicadas. |
| CP-REV-012 | RF-REV-006 | Publicación y visualización frontend | Total igual a cero | Producto sin reseñas | AVL | El total mostrado es 0. |
| CP-REV-013 | RF-REV-007 | Publicación y visualización frontend | Usuario invitado sin permiso para reseñar | Invitado + config deshabilitada | PE | Se muestra mensaje de login/registro. |
| CP-REV-014 | RF-REV-008 | Publicación y visualización frontend | Cliente autenticado puede reseñar | Usuario autenticado | PE | El formulario de reseña está habilitado. |
| CP-REV-015 | RF-REV-009 | Publicación y visualización frontend | Invitado con permiso habilitado | Invitado + config habilitada | PE | El formulario de reseña está habilitado. |
| CP-REV-016 | RF-REV-009 | Publicación y visualización frontend | Invitado con permiso deshabilitado | Invitado + config deshabilitada | PE | El formulario no permite envío. |
| CP-REV-017 | RF-REV-010 | Publicación y visualización frontend | Autocompletar nombre de cliente autenticado | Cliente logueado | PE | El nombre se prellena en el formulario. |
| CP-REV-018 | RF-REV-010 | Publicación y visualización frontend | Invitado sin nombre precargado | Invitado | PE | El campo autor aparece vacío. |
| CP-REV-019 | RF-REV-011 | Validación y envío | Token de reseña válido | `review_token` correcto | PE | El envío continúa a validación siguiente. |
| CP-REV-020 | RF-REV-011 | Validación y envío | Token inválido | `review_token` incorrecto | PE | Se devuelve error de token. |
| CP-REV-021 | RF-REV-011 | Validación y envío | Token ausente | Sin token | PE | Se devuelve error de token. |
| CP-REV-022 | RF-REV-012 | Validación y envío | Generación de token por sesión | Carga de formulario | PE | Se genera token único de reseña. |
| CP-REV-023 | RF-REV-013 | Validación y envío | Módulo habilitado | Configuración activa | PE | El sistema permite enviar reseñas. |
| CP-REV-024 | RF-REV-013 | Validación y envío | Módulo deshabilitado | Configuración inactiva | PE | El sistema rechaza la reseña. |
| CP-REV-025 | RF-REV-014 | Validación y envío | Producto existente al reseñar | `product_id` válido | PE | La reseña puede validarse. |
| CP-REV-026 | RF-REV-014 | Validación y envío | Producto inexistente al reseñar | `product_id` inválido | PE | Se devuelve error de producto. |
| CP-REV-027 | RF-REV-015 | Validación y envío | Autor con longitud válida | 10 caracteres | PE | El autor es aceptado. |
| CP-REV-028 | RF-REV-015 | Validación y envío | Autor en límite inferior válido | 3 caracteres | AVL | El autor es aceptado. |
| CP-REV-029 | RF-REV-015 | Validación y envío | Autor por debajo del mínimo | 2 caracteres | AVL | Se devuelve error de autor. |
| CP-REV-030 | RF-REV-015 | Validación y envío | Autor en límite superior válido | 25 caracteres | AVL | El autor es aceptado. |
| CP-REV-031 | RF-REV-015 | Validación y envío | Autor por encima del máximo | 26 caracteres | AVL | Se devuelve error de autor. |
| CP-REV-032 | RF-REV-016 | Validación y envío | Texto con longitud válida | 100 caracteres | PE | El texto es aceptado. |
| CP-REV-033 | RF-REV-016 | Validación y envío | Texto en límite inferior válido | 25 caracteres | AVL | El texto es aceptado. |
| CP-REV-034 | RF-REV-016 | Validación y envío | Texto por debajo del mínimo | 24 caracteres | AVL | Se devuelve error de texto. |
| CP-REV-035 | RF-REV-016 | Validación y envío | Texto en límite superior válido | 1000 caracteres | AVL | El texto es aceptado. |
| CP-REV-036 | RF-REV-016 | Validación y envío | Texto por encima del máximo | 1001 caracteres | AVL | Se devuelve error de texto. |
| CP-REV-037 | RF-REV-017 | Validación y envío | Rating válido intermedio | `rating=3` | PE | El rating es aceptado. |
| CP-REV-038 | RF-REV-017 | Validación y envío | Rating en límite inferior válido | `rating=1` | AVL | El rating es aceptado. |
| CP-REV-039 | RF-REV-017 | Validación y envío | Rating por debajo del mínimo | `rating=0` | AVL | Se devuelve error de rating. |
| CP-REV-040 | RF-REV-017 | Validación y envío | Rating en límite superior válido | `rating=5` | AVL | El rating es aceptado. |
| CP-REV-041 | RF-REV-017 | Validación y envío | Rating por encima del máximo | `rating=6` | AVL | Se devuelve error de rating. |
| CP-REV-042 | RF-REV-018 | Validación y envío | Invitado sin permiso intenta reseñar | Invitado + config deshabilitada | PE | Se devuelve error de login. |
| CP-REV-043 | RF-REV-019 | Validación y envío | Cliente con compra previa | Cliente autenticado con compra del producto | PE | Puede registrar reseña. |
| CP-REV-044 | RF-REV-019 | Validación y envío | Cliente sin compra previa | Cliente autenticado sin compra | PE | Se devuelve error de compra requerida. |
| CP-REV-045 | RF-REV-020 | Validación y envío | Captcha habilitado y correcto | Captcha válido | PE | El envío continúa. |
| CP-REV-046 | RF-REV-020 | Validación y envío | Captcha habilitado e incorrecto | Captcha inválido | PE | Se devuelve error de captcha. |
| CP-REV-047 | RF-REV-021 | Validación y envío | Error estructurado por autor | Autor inválido | PE | El error vuelve en campo `author`. |
| CP-REV-048 | RF-REV-021 | Validación y envío | Error estructurado por texto | Texto inválido | PE | El error vuelve en campo `text`. |
| CP-REV-049 | RF-REV-021 | Validación y envío | Error estructurado por rating | Rating inválido | PE | El error vuelve en campo `rating`. |
| CP-REV-050 | RF-REV-022, RF-REV-023 | Persistencia y publicación | Registro exitoso de reseña válida | Datos completos válidos | PE | La reseña se guarda y se devuelve éxito. |
| CP-REV-051 | RF-REV-024 | Persistencia y publicación | Guardado completo de reseña | Autor, cliente, producto, texto, rating válidos | PE | Se persisten todos los campos requeridos. |
| CP-REV-052 | RF-REV-025 | Persistencia y publicación | Asociación con cliente autenticado | Cliente logueado envía reseña | PE | La reseña queda asociada al `customer_id`. |
| CP-REV-053 | RF-REV-026 | Persistencia y publicación | Frontend muestra solo reseñas aprobadas | Mezcla de reseñas aprobadas/no aprobadas | PE | Solo se listan las aprobadas. |
| CP-REV-054 | RF-REV-027 | Persistencia y publicación | Producto inactivo con reseñas | Producto deshabilitado | PE | Sus reseñas no se listan en frontend. |
| CP-REV-055 | RF-REV-028 | Persistencia y publicación | Conteo solo de reseñas publicadas | Reseñas con estados mixtos | PE | El total considera solo aprobadas de productos válidos. |
| CP-REV-056 | RF-REV-029 | Persistencia y publicación | Orden descendente por fecha | Varias reseñas con distintas fechas | PE | Se muestran de más reciente a más antigua. |
| CP-REV-057 | RF-REV-030, RF-REV-031 | Integración con catálogo y producto | Mostrar total en ficha y pestaña | Producto con reseñas | PE | El total coincide en ambos puntos. |
| CP-REV-058 | RF-REV-032 | Integración con catálogo y producto | Configuración global habilita escritura | `config_review_status=1` | PE | El flujo de reseña está habilitado. |
| CP-REV-059 | RF-REV-032 | Integración con catálogo y producto | Configuración global bloquea escritura | `config_review_status=0` | PE | El flujo de reseña es rechazado. |
| CP-REV-060 | RF-REV-033 | Integración con catálogo y producto | Configuración exige login/compra | Configuración restrictiva activa | PE | Se aplican restricciones correctamente. |
| CP-REV-061 | RF-REV-034 | Integración con catálogo y producto | Mostrar reseñas en comparación | Comparación con reseñas habilitadas | PE | Se muestra información de reseñas en comparación. |
| CP-REV-062 | RF-REV-035, RF-REV-041 | Gestión administrativa | Listar reseñas en backend con paginación | Reseñas existentes | PE | El listado administrativo carga y pagina correctamente. |
| CP-REV-063 | RF-REV-036, RF-REV-037, RF-REV-038, RF-REV-039 | Gestión administrativa | Filtrar reseñas | Filtros por producto, autor, estado y fechas | PE | El listado refleja correctamente los filtros. |
| CP-REV-064 | RF-REV-040 | Gestión administrativa | Ordenar reseñas | Orden por producto, autor, rating o fecha | PE | El listado se ordena según criterio seleccionado. |
| CP-REV-065 | RF-REV-042, RF-REV-043, RF-REV-044, RF-REV-045 | Gestión administrativa | Crear, editar, eliminar y abrir formulario | Operaciones CRUD administrativas | PE | Las acciones administrativas se ejecutan y permiten acceder al formulario correspondiente. |

---

## Resumen por técnica

| Técnica | Cantidad |
|---|---:|
| Partición de Equivalencia (PE) | 50 |
| Análisis de Valores Límite (AVL) | 15 |
| **Total** | **65** |

---
