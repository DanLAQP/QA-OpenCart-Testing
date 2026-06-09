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