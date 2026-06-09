# Pruebas: Checkout y Pago

### Tabla de casos de prueba

| ID | Componente | Escenario | Entrada | Técnica | Resultado esperado |
| :--- | :--- | :--- | :--- | :--- | :--- |
| CP-Chk-001 | Flujo general | Inicialización del estado del checkout (RF-CHK-001, 005 al 011) | `cart.isValid = true`, `user.isAuthenticated = true/false` | PE | La función inicial retorna el estado correcto (etapas requeridas y banderas de UI). |
| CP-Chk-002 | Flujo general | Intento de checkout con carrito inválido o vacío (RF-CHK-002) | `cart.items = []` o `cart.items[0].price < 0` | AVL | Se lanza excepción `InvalidCartError` o retorna `false`. |
| CP-Chk-003 | Flujo general | Validación de stock estricta sin permiso de backorder (RF-CHK-003) | `item.stock = 0`, `config.allow_backorder = false` | TD | La validación retorna error de inventario insuficiente. |
| CP-Chk-004 | Flujo general | Validación de stock permisiva con backorder (RF-CHK-003) | `item.stock = 0`, `config.allow_backorder = true` | TD | La validación de stock pasa exitosamente. |
| CP-Chk-005 | Flujo general | Límite inferior de cantidad de compra (RF-CHK-004) | `item.qty = 1`, `config.min_qty = 5` | AVL | Retorna error de cantidad mínima no alcanzada. |
| CP-Chk-006 | Dir. de Pago | Carga y selección de dirección existente (RF-CHK-012, 024) | `user.addresses = [...]`, `selected_id = 1` | PE | El sistema asocia la dirección elegida a `session.billing_address`. |
| CP-Chk-007 | Dir. de Pago | Validación de campos base vacíos o nulos (RF-CHK-014, 015) | `name=""`, `lastName=""`, `city=""`, `address=""` | AVL | El validador retorna error de campos obligatorios. |
| CP-Chk-008 | Dir. de Pago | Reglas geográficas dinámicas (RF-CHK-016 al 018) | `country="PE"`, `zipCode=null`, `department=null` | TD | Retorna error solicitando departamento, pero ignora código postal. |
| CP-Chk-009 | Dir. de Pago | Campos personalizados y validación Regex (RF-CHK-019, 020) | `custom_field="123"`, `regex="^[A-Z]+$"` | PE | Falla validación Regex personalizada y retorna error de formato. |
| CP-Chk-010 | Dir. de Pago | Límite superior de carga de archivo (RF-CHK-021, 022) | `file.size = config.max_size + 1` | AVL | El sistema rechaza el archivo por exceso de peso. |
| CP-Chk-011 | Dir. de Pago | Persistencia de nueva dirección (RF-CHK-013, 023) | Objeto `address` válido y completo. | PE | Retorna objeto guardado y lo asocia a la sesión actual. |
| CP-Chk-012 | Dir. de Pago | Efecto cascada al cambiar dirección de pago (RF-CHK-025) | `session.shipping_methods = [...]`, invocación de `change_billing()` | PE | `session.shipping_methods` y `session.payment_methods` se limpian (`null` o `[]`). |
| CP-Chk-013 | Dir. de Envío | Carga y selección de dirección existente (RF-CHK-026, 037) | `user.addresses = [...]`, `selected_id = 2` | PE | El sistema asocia la dirección a `session.shipping_address`. |
| CP-Chk-014 | Dir. de Envío | Validación de campos base vacíos o nulos (RF-CHK-028, 029) | `name=""`, `lastName=""`, `city=""`, `address=""` | AVL | El validador retorna error de campos obligatorios para envío. |
| CP-Chk-015 | Dir. de Envío | Reglas geográficas de código postal obligatorio (RF-CHK-030 al 032) | `country="US"`, `zipCode=null` | TD | Retorna error solicitando código postal obligatorio. |
| CP-Chk-016 | Dir. de Envío | Campos personalizados y Regex de envío (RF-CHK-033 al 035) | Objeto `address` con string inválido para regex. | PE | Retorna errores específicos del campo personalizado. |
| CP-Chk-017 | Dir. de Envío | Persistencia de nueva dirección de envío (RF-CHK-027, 036) | Objeto `address` válido y completo. | PE | Retorna objeto guardado y lo asocia a la sesión actual. |
| CP-Chk-018 | Dir. de Envío | Efecto cascada al cambiar dirección de envío (RF-CHK-038) | `session.shipping_methods = [...]`, invocación de `change_shipping()` | PE | `session.shipping_methods` y `session.payment_methods` quedan vacíos. |
| CP-Chk-019 | Dir. de Envío | Salto de etapa si el carrito no requiere envío (RF-CHK-039) | `cart.requires_shipping = false` | PE | El router interno retorna la etapa "Método de Pago" como siguiente paso. |
| CP-Chk-020 | Método de Envío | Intento de cotización sin cumplir pre-requisitos (RF-CHK-041 al 043) | `session.shipping_address = null` | PE | Lanza `PreconditionFailedError` o retorna lista de cotizaciones vacía. |
| CP-Chk-021 | Método de Envío | Cotización exitosa de métodos de envío (RF-CHK-040, 045) | Direcciones válidas inyectadas en sesión. | PE | Retorna el arreglo de cotizaciones y lo asigna a `session.available_shipping`. |
| CP-Chk-022 | Método de Envío | Sin métodos de envío disponibles (RF-CHK-044) | Retorno de API/Servicio de cotización vacío. | AVL | Devuelve estado de error o bandera `no_methods_found = true`. |
| CP-Chk-023 | Método de Envío | Prevención de inyección de método inexistente (RF-CHK-047) | `method = "gratis_falso"`, `disponibles = ["dhl_10"]` | PE | Rechaza la selección y no modifica la sesión actual. |
| CP-Chk-024 | Método de Envío | Selección exitosa de método de envío (RF-CHK-046, 048) | `method = "dhl_10"`, `disponibles = ["dhl_10"]` | PE | `session.selected_shipping` se actualiza correctamente. |
| CP-Chk-025 | Método de Envío | Efecto cascada al cambiar método de envío (RF-CHK-049) | `session.payment_methods = [...]`, `change_shipping_method()` | PE | `session.payment_methods` se limpia. |
| CP-Chk-026 | Método de Pago | Recuperación de estado previo del pago (RF-CHK-050 al 053) | `session.payment`, `session.comment`, `session.terms` | PE | Retorna el DTO del estado actual correctamente mapeado. |
| CP-Chk-027 | Método de Pago | Obtención de métodos saltando pre-requisitos (RF-CHK-055 al 057) | `cart.requires_shipping = true`, `session.shipping_method = null` | PE | Retorna error exigiendo selección de método de envío primero. |
| CP-Chk-028 | Método de Pago | Obtención exitosa de métodos de pago (RF-CHK-054, 059) | Todas las dependencias de sesión válidas. | PE | Retorna y guarda en sesión los métodos habilitados en `session.available_payments`. |
| CP-Chk-029 | Método de Pago | Bloqueo por falta de métodos de pago (RF-CHK-058) | Reglas de negocio excluyen todos los métodos. | AVL | Devuelve bandera `no_payment_methods = true`. |
| CP-Chk-030 | Método de Pago | Prevención de inyección de método de pago (RF-CHK-061) | `method = "bypass"`, `disponibles = ["tarjeta"]` | PE | Rechaza la selección por no existir en la lista de disponibles. |
| CP-Chk-031 | Método de Pago | Selección exitosa de método de pago (RF-CHK-060, 062) | `method = "tarjeta"`, `disponibles = ["tarjeta"]` | PE | `session.selected_payment` se actualiza correctamente. |
| CP-Chk-032 | Método de Pago | Adición y persistencia de comentarios (RF-CHK-063, 064) | `comment = "Dejar en portería"` | PE | `session.order_comment` persiste el string inyectado. |
| CP-Chk-033 | Método de Pago | Registro de aceptación de términos (RF-CHK-065) | `terms_accepted = true` | PE | `session.terms_accepted` cambia a verdadero. |
| CP-Chk-034 | Confirmación | Cálculo matemático preciso de totales (RF-CHK-066) | `subtotal=100`, `shipping=10`, `tax=18%` | PE | `total` calculado es exactamente `129.80`. |
| CP-Chk-035 | Confirmación | Interrupción por fallo de re-validación final (RF-CHK-067 al 069, 071 al 073) | Mock inyecta `stock = 0` justo antes de confirmar. | TD | `generateOrder()` aborta y retorna arreglo de errores de validación. |
| CP-Chk-036 | Confirmación | Limpieza de datos residuales de envío (RF-CHK-070) | `cart.requires_shipping = false`, `session.shipping_address != null` | PE | Antes de armar la orden, los datos de envío se fuerzan a `null`. |
| CP-Chk-037 | Confirmación | Gestión de Order ID huérfano (RF-CHK-075) | `session.order_id = 999` (ID no existe en mock DB) | PE | Limpia `session.order_id` antes de crear la nueva orden. |
| CP-Chk-038 | Confirmación | Construcción exitosa del Payload de la Orden (RF-CHK-074, 076 al 079) | Sesión completa válida, idioma "es", moneda "PEN", tracking "affiliate_01". | PE | Construye y retorna el objeto `Order` completo con metadatos y totales exactos. |
| CP-Chk-039 | Confirmación | Handshake inicial de la orden (RF-CHK-080) | Objeto `Order` generado correctamente. | PE | El estado de la orden devuelto es `pending_payment` o `ready_to_process`. |
| CP-Chk-040 | Éxito y Fallo | Limpieza total de sesión post-éxito (RF-CHK-082, 083) | Función invocada con evento `PAYMENT_SUCCESS`. | PE | `session.cart`, `order_id`, `methods`, cupones y términos quedan nulos. |
| CP-Chk-041 | Éxito y Fallo | Retorno de estado de éxito (RF-CHK-081, 084, 085) | `status = "SUCCESS"`, `is_guest = false` | TD | Retorna DTO con URL de éxito y mensaje personalizado para usuario autenticado. |
| CP-Chk-042 | Éxito y Fallo | Retorno de estado de fallo (RF-CHK-086 al 088) | `status = "FAILED"`, `error_code = "CARD_DECLINED"` | TD | Retorna DTO con mensaje de fallo, datos de soporte y bandera para reintento. |