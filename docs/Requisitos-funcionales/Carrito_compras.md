# Módulo: Catálogo y Búsqueda

## Descripción general

El módulo **Catálogo y Búsqueda** gestiona la exploración, consulta y descubrimiento de productos dentro de la tienda. Incluye la navegación por categorías, la búsqueda por distintos criterios, la visualización del detalle de producto, la comparación de productos y la exploración por fabricantes o marcas.

Este módulo permite que el usuario encuentre productos mediante estructuras jerárquicas, filtros, ordenamientos y páginas de detalle enriquecidas con información comercial, técnica y visual.

---

## Alcance funcional

El módulo cubre las siguientes áreas:

- Navegación por catálogo
- Búsqueda de productos
- Detalle de producto
- Comparación de productos
- Marcas / fabricantes
- Reglas de visualización de productos

---

## Requisitos funcionales

## 1. Navegación por catálogo

- **RF-CAT-001** El sistema debe mostrar categorías de productos.
- **RF-CAT-002** El sistema debe permitir navegar jerárquicamente por categorías y subcategorías.
- **RF-CAT-003** El sistema debe construir breadcrumbs de navegación según la ruta de categorías.
- **RF-CAT-004** El sistema debe mostrar nombre, descripción e imagen de la categoría.
- **RF-CAT-005** El sistema debe mostrar metadatos SEO de la categoría.
- **RF-CAT-006** El sistema debe listar subcategorías de una categoría.
- **RF-CAT-007** El sistema debe poder mostrar la cantidad de productos por subcategoría cuando la configuración lo habilite.
- **RF-CAT-008** El sistema debe permitir filtrar productos dentro de una categoría.
- **RF-CAT-009** El sistema debe permitir ordenar productos de una categoría.
- **RF-CAT-010** El sistema debe permitir paginar resultados de una categoría.
- **RF-CAT-011** El sistema debe permitir cambiar la cantidad de productos mostrados por página.
- **RF-CAT-012** El sistema debe mostrar enlace a comparación de productos desde la categoría.

## 2. Búsqueda de productos

- **RF-CAT-013** El sistema debe permitir buscar productos por texto.
- **RF-CAT-014** El sistema debe permitir buscar productos por descripción cuando se habilite ese criterio.
- **RF-CAT-015** El sistema debe permitir buscar productos por etiquetas.
- **RF-CAT-016** El sistema debe permitir limitar la búsqueda a una categoría específica.
- **RF-CAT-017** El sistema debe permitir incluir subcategorías en la búsqueda.
- **RF-CAT-018** El sistema debe mostrar el término buscado en el título y encabezado de resultados.
- **RF-CAT-019** El sistema debe construir breadcrumbs del flujo de búsqueda.
- **RF-CAT-020** El sistema debe mostrar una estructura de categorías de hasta tres niveles para apoyar la búsqueda.
- **RF-CAT-021** El sistema debe listar productos que coincidan con el criterio de búsqueda.
- **RF-CAT-022** El sistema debe mostrar imagen del producto en resultados de búsqueda.
- **RF-CAT-023** El sistema debe mostrar descripción resumida del producto en resultados.
- **RF-CAT-024** El sistema debe mostrar precio y precio especial en resultados cuando la política de precios lo permita.
- **RF-CAT-025** El sistema debe mostrar impuestos en resultados cuando la configuración fiscal lo habilite.
- **RF-CAT-026** El sistema debe permitir ordenar resultados de búsqueda.
- **RF-CAT-027** El sistema debe permitir paginar resultados de búsqueda.
- **RF-CAT-028** El sistema debe permitir cambiar el límite de resultados por página.
- **RF-CAT-029** El sistema debe mantener los filtros de búsqueda al navegar entre ordenamientos, páginas y límites.
- **RF-CAT-030** El sistema debe mostrar acceso a comparación de productos desde los resultados de búsqueda.

## 3. Detalle de producto

- **RF-CAT-031** El sistema debe mostrar la ficha/detalle de un producto.
- **RF-CAT-032** El sistema debe cargar el producto a partir de su `product_id`.
- **RF-CAT-033** El sistema debe mostrar metadatos SEO del producto.
- **RF-CAT-034** El sistema debe publicar URL canónica del producto.
- **RF-CAT-035** El sistema debe construir breadcrumbs desde categoría, fabricante o búsqueda hacia el producto.
- **RF-CAT-036** El sistema debe mostrar nombre del producto.
- **RF-CAT-037** El sistema debe mostrar mensaje de cantidad mínima requerida del producto.
- **RF-CAT-038** El sistema debe mostrar invitación a login/registro cuando el precio requiera autenticación.
- **RF-CAT-039** El sistema debe mostrar cantidad de reseñas del producto.
- **RF-CAT-040** El sistema debe mostrar pestaña de reseñas con el total correspondiente.
- **RF-CAT-041** El sistema debe soportar carga de archivos para opciones del producto cuando aplique.
- **RF-CAT-042** El sistema debe validar tamaño máximo de archivos asociados al producto.
- **RF-CAT-043** El sistema debe mostrar fabricante del producto.
- **RF-CAT-044** El sistema debe enlazar al listado del fabricante del producto.
- **RF-CAT-045** El sistema debe mostrar modelo del producto.
- **RF-CAT-046** El sistema debe mostrar códigos adicionales del producto cuando estén activos.
- **RF-CAT-047** El sistema debe mostrar imágenes del producto y soportar visualización ampliada.
- **RF-CAT-048** El sistema debe mostrar precio, precio especial e impuestos cuando corresponda.
- **RF-CAT-049** El sistema debe mostrar stock, disponibilidad o estado del producto.
- **RF-CAT-050** El sistema debe mostrar opciones configurables del producto.
- **RF-CAT-051** El sistema debe mostrar planes de suscripción cuando el producto los tenga.
- **RF-CAT-052** El sistema debe mostrar cantidad mínima de compra.
- **RF-CAT-053** El sistema debe permitir agregar el producto al carrito desde su detalle.
- **RF-CAT-054** El sistema debe permitir agregar el producto a comparación desde su detalle.
- **RF-CAT-055** El sistema debe mostrar productos relacionados cuando existan.

## 4. Comparación de productos

- **RF-CAT-056** El sistema debe mantener una lista de comparación en sesión.
- **RF-CAT-057** El sistema debe permitir agregar productos a la comparación.
- **RF-CAT-058** El sistema debe impedir agregar un producto inexistente a comparación.
- **RF-CAT-059** El sistema debe reordenar un producto ya comparado para enviarlo al final de la lista al volver a agregarlo.
- **RF-CAT-060** El sistema debe limitar la comparación a un máximo de 4 productos.
- **RF-CAT-061** El sistema debe eliminar el producto más antiguo cuando se supere el máximo de comparables.
- **RF-CAT-062** El sistema debe mostrar una vista comparativa de productos seleccionados.
- **RF-CAT-063** El sistema debe permitir quitar productos de la comparación.
- **RF-CAT-064** El sistema debe mostrar imagen, precio, precio especial, fabricante, disponibilidad, mínimo, rating, reseñas, peso y dimensiones en la comparación.
- **RF-CAT-065** El sistema debe mostrar atributos agrupados por grupo de atributos en la comparación.
- **RF-CAT-066** El sistema debe limpiar de la comparación productos que ya no existan.
- **RF-CAT-067** El sistema debe permitir agregar al carrito desde la pantalla de comparación.

## 5. Marcas / fabricantes

- **RF-CAT-068** El sistema debe listar fabricantes o marcas.
- **RF-CAT-069** El sistema debe agrupar fabricantes alfabéticamente y en el grupo `0-9` cuando corresponda.
- **RF-CAT-070** El sistema debe permitir acceder al catálogo de productos de un fabricante.
- **RF-CAT-071** El sistema debe mostrar breadcrumbs del fabricante.
- **RF-CAT-072** El sistema debe listar productos asociados a un fabricante.
- **RF-CAT-073** El sistema debe permitir ordenar productos del fabricante.
- **RF-CAT-074** El sistema debe permitir paginar productos del fabricante.
- **RF-CAT-075** El sistema debe permitir cambiar el límite de resultados del fabricante por página.
- **RF-CAT-076** El sistema debe mostrar comparación de productos también dentro del catálogo por fabricante.

## 6. Reglas de visualización de productos

- **RF-CAT-077** El sistema debe usar una imagen placeholder cuando el producto no tenga imagen válida.
- **RF-CAT-078** El sistema debe truncar descripciones largas según la longitud configurada.
- **RF-CAT-079** El sistema debe ocultar precios cuando la política de cliente lo requiera y el usuario no esté autenticado.
- **RF-CAT-080** El sistema debe calcular precios con impuestos según la configuración fiscal.
- **RF-CAT-081** El sistema debe respetar el mínimo del producto y usar 1 como mínimo por defecto cuando no exista otro valor.
- **RF-CAT-082** El sistema debe generar enlaces al detalle de producto preservando el contexto de navegación cuando corresponda.

---

## Resumen cuantitativo

- Total de requisitos del módulo: **82**
- Navegación por catálogo: **12**
- Búsqueda de productos: **18**
- Detalle de producto: **25**
- Comparación de productos: **12**
- Marcas / fabricantes: **9**
- Reglas de visualización: **6**

---

## Observaciones

- Este README documenta el comportamiento funcional del módulo a nivel de requisitos.
- Puede utilizarse como base para SRS, matriz de trazabilidad, casos de prueba o historias de usuario.
- Si se requiere, este documento puede extenderse con trazabilidad al código, casos de uso o criterios de aceptación.
