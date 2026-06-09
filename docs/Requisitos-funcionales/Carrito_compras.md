# Módulo: Carrito de Compras (Versión 1)

## Descripción general

El módulo **Carrito de Compras** gestiona el almacenamiento temporal de productos seleccionados por el cliente antes de completar el proceso de compra. Permite agregar, editar y eliminar productos, validar opciones de producto, suscripciones, cantidades mínimas y disponibilidad de inventario, así como calcular impuestos, totales y otros cargos asociados al pedido.

El módulo mantiene la persistencia del carrito entre sesiones y autenticaciones de usuario, permitiendo transferir automáticamente los productos agregados como visitante a una cuenta autenticada. Adicionalmente, expone funcionalidades mediante API para la gestión remota del carrito, incluyendo validaciones avanzadas de productos, stock y opciones.

## Alcance funcional

El módulo cubre las siguientes áreas:

- Visualización del carrito
- Totales y cálculo del carrito
- Navegación desde el carrito
- Agregar productos al carrito
- Edición de productos del carrito
- Eliminación de productos del carrito
- Persistencia y gestión interna
- Validaciones de stock y opciones
- API del carrito

## Requisitos funcionales

## 1. Visualización del carrito

- **RF-CART-001** El sistema debe mostrar una pantalla de carrito de compras.
- **RF-CART-002** El sistema debe construir una vista listada de los productos agregados al carrito.
- **RF-CART-003** El sistema debe mostrar breadcrumbs de navegación hacia el carrito.
- **RF-CART-004** El sistema debe mostrar mensajes de error y éxito almacenados en sesión.
- **RF-CART-005** El sistema debe advertir falta de stock cuando corresponda según la configuración establecida.
- **RF-CART-006** El sistema debe mostrar un mensaje de atención para login o registro cuando la visualización de precios requiera autenticación.
- **RF-CART-007** El sistema debe mostrar el peso total del carrito cuando la configuración lo habilite.
- **RF-CART-008** El sistema debe mostrar la imagen de cada producto agregado al carrito.
- **RF-CART-009** El sistema debe mostrar las opciones seleccionadas para cada producto.
- **RF-CART-010** El sistema debe resolver el nombre de archivos cargados cuando una opción sea de tipo archivo.
- **RF-CART-011** El sistema debe truncar visualmente valores extensos de opciones al mostrarlos.
- **RF-CART-012** El sistema debe mostrar información de suscripción asociada al producto cuando corresponda.
- **RF-CART-013** El sistema debe mostrar el estado de stock de cada producto dentro del carrito.
- **RF-CART-014** El sistema debe mostrar advertencias cuando no se cumpla la cantidad mínima requerida de un producto.
- **RF-CART-015** El sistema debe mostrar el precio unitario y el total por producto cuando la política de precios lo permita.
- **RF-CART-016** El sistema debe generar enlaces al detalle de cada producto desde el carrito.
- **RF-CART-017** El sistema debe generar enlaces para eliminar productos del carrito.

## 2. Totales y cálculo del carrito

- **RF-CART-018** El sistema debe calcular los impuestos aplicables al carrito.
- **RF-CART-019** El sistema debe calcular el total general del carrito.
- **RF-CART-020** El sistema debe mostrar las líneas de totales calculadas.
- **RF-CART-021** El sistema debe integrar extensiones de tipo total en la pantalla del carrito.
- **RF-CART-022** El sistema debe mostrar módulos o extensiones adicionales del carrito cuando existan y se carguen correctamente.

## 3. Navegación desde el carrito

- **RF-CART-023** El sistema debe permitir continuar comprando desde el carrito.
- **RF-CART-024** El sistema debe permitir avanzar al proceso de checkout cuando existan productos en el carrito.

## 4. Agregar productos al carrito

- **RF-CART-025** El sistema debe permitir agregar productos al carrito desde el frontend.
- **RF-CART-026** El sistema debe recibir product_id, cantidad, opciones seleccionadas y plan de suscripción al agregar un producto.
- **RF-CART-027** El sistema debe validar que el producto exista antes de agregarlo al carrito.
- **RF-CART-028** El sistema debe soportar productos variantes y resolver el producto maestro cuando corresponda.
- **RF-CART-029** El sistema debe combinar automáticamente variantes y opciones cuando se utilicen mecanismos de override de variantes.
- **RF-CART-030** El sistema debe validar las opciones obligatorias del producto antes de agregarlo al carrito.
- **RF-CART-031** El sistema debe validar expresiones regulares definidas para opciones de tipo texto.
- **RF-CART-032** El sistema debe validar que se seleccione un plan de suscripción válido cuando el producto lo requiera.
- **RF-CART-033** El sistema debe agregar el producto al carrito cuando todas las validaciones sean satisfactorias.
- **RF-CART-034** El sistema debe devolver un mensaje de éxito con enlaces al producto y al carrito cuando la operación sea exitosa.
- **RF-CART-035** El sistema debe redirigir al detalle del producto cuando falle alguna validación durante el agregado.
- **RF-CART-036** El sistema debe limpiar métodos de envío y pago previamente calculados después de agregar productos al carrito.

## 5. Edición de productos del carrito

- **RF-CART-037** El sistema debe permitir actualizar la cantidad de un producto previamente agregado al carrito.
- **RF-CART-038** El sistema debe identificar cada elemento del carrito mediante una clave o identificador interno único.
- **RF-CART-039** El sistema debe devolver una confirmación de actualización cuando el carrito continúe conteniendo productos.
- **RF-CART-040** El sistema debe redirigir al carrito cuando la actualización deje el carrito sin productos.
- **RF-CART-041** El sistema debe limpiar información previa de pedido, envío, pago y recompensas después de modificar el carrito.

## 6. Eliminación de productos del carrito

- **RF-CART-042** El sistema debe permitir eliminar productos del carrito.
- **RF-CART-043** El sistema debe devolver una confirmación cuando la eliminación sea exitosa y aún existan productos en el carrito.
- **RF-CART-044** El sistema debe redirigir al carrito vacío cuando ya no queden productos.
- **RF-CART-045** El sistema debe limpiar información previa de pedido, envío, pago y recompensas después de eliminar productos del carrito.

## 7. Persistencia y comportamiento interno del carrito

- **RF-CART-046** El sistema debe almacenar el carrito asociado a la tienda, cliente y sesión correspondiente.
- **RF-CART-047** El sistema debe eliminar automáticamente carritos expirados pertenecientes a visitantes no autenticados.
- **RF-CART-048** El sistema debe actualizar el identificador de sesión del carrito cuando un cliente autenticado cambie de sesión.
- **RF-CART-049** El sistema debe transferir automáticamente al cliente autenticado los productos agregados previamente como visitante dentro de la misma sesión.
- **RF-CART-050** El sistema debe cargar automáticamente los productos del carrito al inicializar la librería correspondiente.

## 8. Validaciones de stock y opciones

- **RF-CART-051** El sistema debe validar stock del producto al cargar productos del carrito.
- **RF-CART-052** El sistema debe validar stock de opciones seleccionadas que descuenten inventario.
- **RF-CART-053** El sistema debe acumular impactos de precio, puntos y peso derivados de opciones de producto.
- **RF-CART-054** El sistema debe considerar opciones de tipo selección, radio y checkbox al reconstruir el carrito.
- **RF-CART-055** El sistema debe conservar datos descriptivos de las opciones seleccionadas.
- **RF-CART-056** El sistema debe excluir del carrito productos no vigentes, inactivos o no disponibles para la tienda/idioma actual.


## 9. API del carrito

- **RF-CART-057** El sistema debe permitir agregar múltiples productos al carrito mediante API.
- **RF-CART-058** El sistema debe permitir agregar un solo producto al carrito mediante API.
- **RF-CART-059** El sistema debe aceptar order_id en API para recalcular disponibilidad sobre pedidos existentes.
- **RF-CART-060** El sistema debe recuperar cantidades de productos y opciones ya presentes en un pedido existente cuando se opere con order_id.
- **RF-CART-061** El sistema debe validar que las opciones enviadas por API pertenezcan realmente al producto.
- **RF-CART-062** El sistema debe validar stock de opciones por API.
- **RF-CART-063** El sistema debe validar opciones obligatorias por API.
- **RF-CART-064** El sistema debe validar regex de opciones tipo texto por API.
- **RF-CART-065** El sistema debe validar stock total del producto por API.
- **RF-CART-066** El sistema debe validar cantidad mínima del producto en la confirmación por API.
- **RF-CART-067** El sistema debe validar planes de suscripción por API.
- **RF-CART-068** El sistema debe devolver errores estructurados por producto y opción cuando una validación falle en API.
- **RF-CART-069** El sistema debe devolver un mensaje general de éxito cuando la operación API sea válida.
- **RF-CART-070** El sistema debe devolver un mensaje general de error cuando alguna validación falle en API.

## Resumen cuantitativo

- Total de requisitos del módulo: **72**
- Visualización del carrito: **17**
- Totales y cálculo del carrito: **5**
- Navegación desde el carrito: **2**
- Agregar productos al carrito: **12**
- Edición de productos del carrito: **5**
- Eliminación de productos del carrito: **4**
- Persistencia y comportamiento interno: **5**
- Validaciones de stock y opciones: **8**
- API del carrito: **14**

## Observaciones

- El módulo presenta una cobertura funcional completa basada en la revisión de controladores frontend, controladores API y librerías internas del carrito.
- El carrito mantiene persistencia por tienda, cliente y sesión, permitiendo la migración automática de productos agregados como visitante al autenticarse un cliente.
- El cálculo de precios contempla descuentos, promociones, impuestos, suscripciones, opciones de producto, puntos de recompensa y peso.
- El sistema incorpora validaciones de stock tanto para productos como para opciones que descuentan inventario.
- La funcionalidad puede extenderse mediante módulos y extensiones de tipo total configuradas en la plataforma.
- La API del carrito proporciona mecanismos de validación equivalentes a los disponibles en la interfaz web, garantizando consistencia funcional entre ambos canales.
- Este documento puede utilizarse como base para especificaciones SRS, matrices de trazabilidad, criterios de aceptación y casos de prueba funcionales.


---
