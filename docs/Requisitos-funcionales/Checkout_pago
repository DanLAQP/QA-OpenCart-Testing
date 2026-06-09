# Módulo: Checkout / Pago

## Descripción general

El módulo **Checkout / Pago** gestiona el proceso de compra desde la preparación de datos del pedido hasta la confirmación final. Incluye validación del carrito, direcciones de pago y envío, métodos de envío, métodos de pago, comentario del pedido, aceptación de términos y estados finales de éxito o fallo.

Este módulo centraliza la generación de la orden y valida que toda la información necesaria esté disponible antes de completar la compra.

---

## Alcance funcional

El módulo cubre las siguientes áreas:

- Flujo general de checkout
- Dirección de pago
- Dirección de envío
- Método de envío
- Método de pago
- Confirmación del pedido
- Éxito y fallo

---

## Requisitos funcionales

## 1. Flujo general de checkout

- **RF-CHK-001** El sistema debe mostrar una pantalla principal de checkout.
- **RF-CHK-002** El sistema debe impedir entrar al checkout si el carrito no tiene productos válidos.
- **RF-CHK-003** El sistema debe impedir entrar al checkout si no hay stock suficiente y la configuración no permite continuar sin stock.
- **RF-CHK-004** El sistema debe impedir entrar al checkout si no se cumplen cantidades mínimas de compra.
- **RF-CHK-005** El sistema debe mostrar breadcrumbs del proceso de checkout.
- **RF-CHK-006** El sistema debe mostrar registro durante checkout para usuarios no autenticados.
- **RF-CHK-007** El sistema debe mostrar dirección de pago cuando la configuración lo requiera y el cliente esté autenticado.
- **RF-CHK-008** El sistema debe mostrar dirección de envío cuando el carrito requiera envío y el cliente esté autenticado.
- **RF-CHK-009** El sistema debe mostrar selección de método de envío cuando el carrito requiera envío.
- **RF-CHK-010** El sistema debe mostrar selección de método de pago.
- **RF-CHK-011** El sistema debe mostrar una etapa de confirmación del pedido.

## 2. Dirección de pago

- **RF-CHK-012** El sistema debe listar direcciones existentes del cliente para pago.
- **RF-CHK-013** El sistema debe permitir registrar una nueva dirección de pago durante checkout.
- **RF-CHK-014** El sistema debe validar nombre y apellido de la dirección de pago.
- **RF-CHK-015** El sistema debe validar dirección principal y ciudad.
- **RF-CHK-016** El sistema debe validar código postal cuando el país lo requiera.
- **RF-CHK-017** El sistema debe validar país válido.
- **RF-CHK-018** El sistema debe validar zona/departamento cuando el país tenga zonas configuradas.
- **RF-CHK-019** El sistema debe soportar campos personalizados de tipo dirección en pago.
- **RF-CHK-020** El sistema debe validar campos personalizados obligatorios y regex de dirección de pago.
- **RF-CHK-021** El sistema debe soportar carga de archivos en dirección de pago cuando aplique.
- **RF-CHK-022** El sistema debe validar tamaño máximo de archivo cargado.
- **RF-CHK-023** El sistema debe guardar la nueva dirección de pago y asociarla a la sesión actual.
- **RF-CHK-024** El sistema debe permitir seleccionar una dirección existente como dirección de pago.
- **RF-CHK-025** El sistema debe limpiar métodos de envío y pago previamente calculados al cambiar la dirección de pago.

## 3. Dirección de envío

- **RF-CHK-026** El sistema debe listar direcciones existentes del cliente para envío.
- **RF-CHK-027** El sistema debe permitir registrar una nueva dirección de envío durante checkout.
- **RF-CHK-028** El sistema debe validar nombre y apellido de la dirección de envío.
- **RF-CHK-029** El sistema debe validar dirección principal y ciudad de envío.
- **RF-CHK-030** El sistema debe validar código postal cuando el país lo requiera.
- **RF-CHK-031** El sistema debe validar país válido para envío.
- **RF-CHK-032** El sistema debe validar zona/departamento cuando corresponda.
- **RF-CHK-033** El sistema debe soportar campos personalizados de dirección de envío.
- **RF-CHK-034** El sistema debe validar campos personalizados obligatorios y regex de dirección de envío.
- **RF-CHK-035** El sistema debe soportar carga de archivos en dirección de envío cuando aplique.
- **RF-CHK-036** El sistema debe guardar la nueva dirección de envío y asociarla a la sesión actual.
- **RF-CHK-037** El sistema debe permitir seleccionar una dirección existente como dirección de envío.
- **RF-CHK-038** El sistema debe limpiar métodos de envío y pago al cambiar la dirección de envío.
- **RF-CHK-039** El sistema debe impedir el flujo de dirección de envío cuando el carrito no requiera envío.

## 4. Método de envío

- **RF-CHK-040** El sistema debe obtener cotizaciones de envío disponibles según la dirección de envío actual.
- **RF-CHK-041** El sistema debe exigir datos de cliente para cotizar envío.
- **RF-CHK-042** El sistema debe exigir dirección de pago cuando la configuración la requiera antes de cotizar envío.
- **RF-CHK-043** El sistema debe exigir dirección de envío válida antes de cotizar envío.
- **RF-CHK-044** El sistema debe informar error cuando no existan métodos de envío disponibles.
- **RF-CHK-045** El sistema debe almacenar en sesión los métodos de envío disponibles.
- **RF-CHK-046** El sistema debe permitir seleccionar un método de envío disponible.
- **RF-CHK-047** El sistema debe validar que el método de envío seleccionado exista dentro de las cotizaciones disponibles.
- **RF-CHK-048** El sistema debe guardar en sesión el método de envío seleccionado.
- **RF-CHK-049** El sistema debe limpiar métodos de pago al cambiar el método de envío.

## 5. Método de pago

- **RF-CHK-050** El sistema debe mostrar el método de pago actualmente seleccionado.
- **RF-CHK-051** El sistema debe mostrar el comentario del pedido asociado al pago si existe.
- **RF-CHK-052** El sistema debe mostrar el estado de aceptación de términos de checkout si existe.
- **RF-CHK-053** El sistema debe mostrar el texto de aceptación de términos cuando haya una página informativa configurada para checkout.
- **RF-CHK-054** El sistema debe obtener métodos de pago disponibles según la dirección de pago o, en ciertos casos, la dirección de envío.
- **RF-CHK-055** El sistema debe exigir datos de cliente antes de obtener métodos de pago.
- **RF-CHK-056** El sistema debe exigir dirección de pago cuando la configuración lo requiera.
- **RF-CHK-057** El sistema debe exigir dirección y método de envío cuando el carrito requiera envío antes de obtener métodos de pago.
- **RF-CHK-058** El sistema debe informar error cuando no existan métodos de pago disponibles.
- **RF-CHK-059** El sistema debe almacenar en sesión los métodos de pago disponibles.
- **RF-CHK-060** El sistema debe permitir seleccionar un método de pago disponible.
- **RF-CHK-061** El sistema debe validar que el método de pago seleccionado exista dentro de los métodos disponibles.
- **RF-CHK-062** El sistema debe guardar en sesión el método de pago seleccionado.
- **RF-CHK-063** El sistema debe permitir registrar un comentario del pedido durante la etapa de pago.
- **RF-CHK-064** El sistema debe persistir el comentario del pedido sobre la orden actual.
- **RF-CHK-065** El sistema debe permitir registrar la aceptación de términos de checkout.

## 6. Confirmación del pedido

- **RF-CHK-066** El sistema debe calcular totales, impuestos y total general antes de confirmar la orden.
- **RF-CHK-067** El sistema debe validar que exista información de cliente antes de generar la orden.
- **RF-CHK-068** El sistema debe validar nuevamente productos, stock y mínimos antes de confirmar.
- **RF-CHK-069** El sistema debe validar dirección y método de envío cuando el carrito requiera envío.
- **RF-CHK-070** El sistema debe limpiar datos de envío de la sesión si el carrito no requiere envío.
- **RF-CHK-071** El sistema debe validar dirección de pago cuando la configuración lo requiera.
- **RF-CHK-072** El sistema debe validar que exista un método de pago seleccionado.
- **RF-CHK-073** El sistema debe validar aceptación de términos de checkout cuando estén configurados.
- **RF-CHK-074** El sistema debe recuperar una orden existente en sesión si ya fue creada previamente.
- **RF-CHK-075** El sistema debe eliminar de sesión el `order_id` cuando este apunte a una orden inexistente.
- **RF-CHK-076** El sistema debe generar una orden cuando el checkout esté en estado válido.
- **RF-CHK-077** El sistema debe construir la orden con datos de tienda, cliente, dirección de pago, dirección de envío, método de pago, método de envío, comentario y totales.
- **RF-CHK-078** El sistema debe incorporar datos de afiliación o marketing cuando exista tracking en sesión.
- **RF-CHK-079** El sistema debe usar idioma y moneda actuales en la orden.
- **RF-CHK-080** El sistema debe dejar lista la orden para continuar con el procesamiento del método de pago.

## 7. Éxito y fallo

- **RF-CHK-081** El sistema debe mostrar una pantalla de éxito tras completar correctamente el checkout.
- **RF-CHK-082** El sistema debe vaciar el carrito al llegar al estado de éxito con una orden activa en sesión.
- **RF-CHK-083** El sistema debe limpiar de sesión `order_id`, métodos de pago, métodos de envío, comentario, aceptación de términos, cupón y recompensa tras una compra exitosa.
- **RF-CHK-084** El sistema debe mostrar un mensaje de éxito distinto para clientes autenticados y para invitados.
- **RF-CHK-085** El sistema debe permitir volver al inicio desde la pantalla de éxito.
- **RF-CHK-086** El sistema debe mostrar una pantalla de fallo cuando el checkout o el pago no se completen correctamente.
- **RF-CHK-087** El sistema debe mostrar un mensaje de contacto/soporte en caso de fallo.
- **RF-CHK-088** El sistema debe permitir continuar hacia el inicio desde la pantalla de fallo.

---

## Resumen cuantitativo

- Total de requisitos del módulo: **88**

---

## Observaciones

- Este README documenta el flujo completo de compra hasta los estados finales.
- Es útil como base para pruebas end-to-end y trazabilidad funcional.
