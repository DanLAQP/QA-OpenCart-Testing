# Pruebas: Carrito de Compras

## Descripción

Este documento contiene una propuesta de **60 casos de prueba** para el módulo **Carrito de Compras**, diseñados con las técnicas de:

- **PE**: Partición de Equivalencia
- **AVL**: Análisis de Valores Límite

Se presentan en formato tabular para facilitar su uso en documentación QA, pruebas funcionales, validación académica o preparación de casos en herramientas de testing.

---


## Tabla de casos de prueba

| ID | Requisito(s) | Componente | Escenario | Entrada | Técnica | Resultado esperado |
|---|---|---|---|---|---|---|
| CART-01 | RF-CART-001 | Visualización | Mostrar carrito con productos | Carrito con 2 productos | PE | Se muestra vista de carrito con lista de artículos. [P1] | 
| CART-02 | RF-CART-002, RF-CART-008 | Visualización | Listar productos y mostrar imágenes | 3 productos agregados | PE | Se muestra nombre, cantidad, precio, imagen de cada producto. [P1] | 
| CART-03 | RF-CART-003 | Visualización | Breadcrumbs de navegación | Usuario en vista carrito | PE | Se construyen breadcrumbs: Home > Carrito. [P1] | catalog/controller/checkout/cart.php |
| CART-04 | RF-CART-004 | Visualización | Mensajes de error en sesión | error almacenado en session | PE | Se muestra mensaje de error y se limpia de sesión. [P1] | 
| CART-05 | RF-CART-004 | Visualización | Mensajes de éxito en sesión | success almacenado en session | PE | Se muestra mensaje de éxito y se limpia de sesión. [P1] | 
| CART-06 | RF-CART-005 | Visualización | Advertencia de falta de stock | Producto sin stock, config_stock_warning=1 | PE | Se muestra advertencia de falta de stock. [P2] | 
| CART-07 | RF-CART-006 | Visualización | Mensaje de login requerido para ver precios | Usuario no autenticado, config_customer_price=1 | PE | Se muestra aviso para autenticarse. [P2] | 
| CART-08 | RF-CART-007 | Visualización | Mostrar peso total del carrito | config_cart_weight=1, productos con peso | PE | Se muestra peso total formateado. [P2] | 
| CART-09 | RF-CART-009, RF-CART-011 | Visualización | Mostrar opciones seleccionadas | Producto con color + talla | PE | Se listan opciones; valores > 20 caracteres se truncan. [P2] | 
| CART-10 | RF-CART-010 | Visualización | Resolver archivo en opción type=file | Opción tipo archivo con upload_id válido | PE | Se muestra nombre legible del archivo. [P2] | 
| CART-11 | RF-CART-012 | Visualización | Mostrar información de suscripción | Producto con plan suscripción activo | PE | Se muestran precio prueba, frecuencia, duración. [P2] | 
| CART-12 | RF-CART-013 | Visualización | Estado de stock de productos | Producto con stock=10 | PE | Se muestra estado disponible/no disponible. [P1] | 
| CART-13 | RF-CART-014 | Visualización | Advertencia cantidad mínima no cumplida | Producto con minimum=5, cantidad=2 | PE | Se muestra advertencia de mínimo requerido. [P2] | 
| CART-14 | RF-CART-015 | Visualización | Mostrar precio unitario y total | Usuario autenticado, config_customer_price=0 | PE | Se muestran precios unitarios y totales. [P1] | 
| CART-15 | RF-CART-016 | Visualización | Enlaces a detalles de producto | Producto en carrito | PE | Se genera URL válida hacia product/product. [P1] | 
| CART-16 | RF-CART-017 | Visualización | Enlaces para eliminar productos | 3 productos en carrito | PE | Se genera URL válida remove por cada artículo. [P1] | 
| CART-17 | RF-CART-018 | Totales | Calcular impuestos aplicables | Producto con tax_class_id válido | PE | Se calcula impuesto según tasa configurada. [P1] | 
| CART-18 | RF-CART-019, RF-CART-020 | Totales | Calcular y mostrar total general | Carrito con 2 productos + impuestos | PE | Total = Subtotal + Impuestos; se muestra en línea totales. [P1] | 
| CART-19 | RF-CART-021 | Totales | Integrar extensiones tipo total | extension total configurada y habilitada | PE | Se incluyen líneas de totales de extensión (envío, cupones). [P2] | 
| CART-20 | RF-CART-022 | Totales | Mostrar módulos adicionales | Extension total=1, status=1 | PE | Se carga controller de extensión; NO_ENCONTRADO si no existe. [P2] | 
| CART-21 | RF-CART-023 | Navegación | Enlace continuar comprando | Usuario en carrito | PE | Se genera URL válida hacia common/home. [P1] | 
| CART-22 | RF-CART-024 | Navegación | Enlace checkout disponible | Carrito con productos | PE | Se genera URL válida hacia checkout/checkout. [P1] | 
| CART-23 | RF-CART-024 | Navegación | Checkout deshabilitado sin productos | Carrito vacío | PE | No se muestra enlace checkout. [P1] | 
| CART-24 | RF-CART-025, RF-CART-026 | Agregar | Agregar producto básico | product_id=1, qty=1 | PE | Producto se agrega al carrito; se envía JSON éxito. [P1] | 
| CART-25 | RF-CART-025, RF-CART-026 | Agregar | Agregar con opciones | product_id=5, qty=2, option[]={'color': 'red'} | PE | Se agrega con opciones; se verifica en cart table. [P1] | 
| CART-26 | RF-CART-026 | Agregar | Agregar con plan suscripción | product_id=10, subscription_plan_id=3 | PE | Se agrega producto con suscripción asociada. [P2] | 
| CART-27 | RF-CART-027 | Agregar | Validar producto no existe | product_id=99999 | PE | Se retorna error_product en JSON. [P1] | 
| CART-28 | RF-CART-028, RF-CART-029 | Agregar | Agregar variante (master_id existe) | Variante con master_id=5 | PE | Se resuelve master_id; opciones se combinan correctamente. [P2] | 
| CART-29 | RF-CART-030 | Agregar | Validar opción obligatoria faltante | Producto con required option, no enviada | PE | Se retorna error_required en JSON. [P1] | 
| CART-30 | RF-CART-031 | Agregar | Validar regex en opción texto | Opción texto con validation='[0-9]{3}', entrada='abc' | PE | Se retorna error_regex en JSON. [P1] | 
| CART-31 | RF-CART-032 | Agregar | Validar suscripción requerida faltante | Producto con subscription requerida, no enviada | PE | Se retorna error_subscription en JSON. [P1] | 
| CART-32 | RF-CART-033, RF-CART-034 | Agregar | Agregar exitoso devuelve URL producto y carrito | Validaciones OK | PE | Se retorna text_success con links a producto y carrito. [P1] | 
| CART-33 | RF-CART-035 | Agregar | Fallo redirecciona a detalle producto | Opción obligatoria falta | PE | Se retorna redirect a product/product. [P1] | 
| CART-34 | RF-CART-036 | Agregar | Limpiar métodos envío y pago | Agregar producto exitoso | PE | session order_id, shipping_method, payment_method se eliminan. [P2] | 
| CART-35 | RF-CART-037, RF-CART-038 | Editar | Actualizar cantidad válida | cart_id=1, qty=5 | PE | UPDATE en BD con nueva cantidad. [P1] | 
| CART-36 | RF-CART-037 | Editar | Editar a cantidad mínima | Producto con minimum=1, qty=1 | AVL | Se actualiza; no hay validación de mínimo en edit. [P2] | 
| CART-37 | RF-CART-037 | Editar | Editar a cantidad cero | cart_id=1, qty=0 | AVL | UPDATE cantidad=0; producto permanece en carrito. [P2] | 
| CART-38 | RF-CART-039, RF-CART-041 | Editar | Editar devuelve confirmación | Carrito con 2+ productos, editar 1 | PE | Se retorna text_edit en JSON. [P1] | 
| CART-39 | RF-CART-040, RF-CART-041 | Editar | Editar último producto a 0 deja carrito vacío | 1 producto, qty=0 | PE | Se retorna redirect a checkout/cart. [P1] | 
| CART-40 | RF-CART-041 | Editar | Limpiar información de pedido tras editar | Edición exitosa | PE | order_id, shipping_method, payment_method, reward se limpian. [P2] | 
| CART-41 | RF-CART-042 | Eliminar | Eliminar producto existente | cart_id válido | PE | DELETE en BD; producto se remueve de carrito. [P1] | 
| CART-42 | RF-CART-043, RF-CART-045 | Eliminar | Eliminar devuelve confirmación | Carrito con 2+ productos | PE | Se retorna text_remove en JSON. [P1] | 
| CART-43 | RF-CART-044, RF-CART-045 | Eliminar | Eliminar último producto deja carrito vacío | 1 producto en carrito | PE | Se retorna redirect a checkout/cart. [P1] | 
| CART-44 | RF-CART-045 | Eliminar | Limpiar información tras eliminación | Eliminación exitosa | PE | order_id, shipping_method, payment_method, reward se limpian. [P2] | 
| CART-45 | RF-CART-046 | Persistencia | Carrito asociado a store_id, customer_id, session_id | Usuario autenticado | PE | INSERT/SELECT valida store_id, customer_id, session_id. [P1] | 
| CART-46 | RF-CART-047 | Persistencia | Eliminar carritos visitante expirados | Carrito visitante, age > session_expire | PE | DELETE automático en constructor Cart. [P2] | 
| CART-47 | RF-CART-048 | Persistencia | Actualizar session_id al autenticar | Customer login con carrito existente | PE | UPDATE session_id en tabla cart. [P2] | 
| CART-48 | RF-CART-049 | Persistencia | Transferir carrito visitante a autenticado | Login con carrito previo | PE | UPDATE customer_id=0→customer_id para items visitor. [P2] | 
| CART-49 | RF-CART-050 | Persistencia | Cargar automáticamente productos al inicializar | new Cart(registry) | PE | Constructor popula this.data desde BD. [P1] | 
| CART-50 | RF-CART-051 | Validación | Validar stock producto al cargar | Producto con quantity < cart qty | PE | stock_status=false si stock insuficiente. [P1] | 
| CART-51 | RF-CART-052 | Validación | Validar stock opción con subtract | Opción con quantity=2, subtract=1, cart qty=3 | PE | stock_status=false si opción stock < qty. [P2] | 
| CART-52 | RF-CART-053 | Validación | Acumular impacto precio de opciones | Opción +10, segunda opción +5 | PE | option_price=15 acumulado en precio total. [P1] | 
| CART-53 | RF-CART-053 | Validación | Acumular puntos de opciones | 2 opciones con points=10, points=5 | PE | option_points=15 acumulado en total puntos. [P2] | 
| CART-54 | RF-CART-053 | Validación | Acumular peso de opciones | 2 opciones con weight=0.5, weight=0.3 | PE | option_weight=0.8 acumulado en peso. [P2] | 
| CART-55 | RF-CART-025 | Agregar | Agregar mismo producto + opciones duplicadas | producto_id=1, option={'color':'red'} x 2 | PE | Cantidad se incrementa; no crea 2 registros. [P1] | 
| CART-56 | RF-CART-018 | Totales | Calcular impuestos con múltiples tasas | 2 productos con diferentes tax_class_id | PE | getTaxes() retorna array con tax_rate_id como clave. [P2] | 
| CART-57 | RF-CART-002 | Visualización | Producto sin imagen muestra placeholder | image='' o file no existe | PE | Se usa 'placeholder.png' como imagen por defecto. [P1] | 
| CART-58 | RF-CART-001 | Visualización | Carrito vacío muestra lista vacía | Ningún producto en carrito | PE | products array vacío; se muestra mensaje o sección vacía. [P1] | 
| CART-59 | RF-CART-017 | Visualización | URL remove genera con parámetro key | Producto con cart_id=123 | PE | Se genera ?key=123 válido en URL remove. [P1] | 
| CART-60 | RF-CART-036 | Agregar | Agregar sobrescribe métodos previos | Métodos previos configurados, nuevo agregar | PE | Métodos de envío/pago previos se descartan. [P2] | 


---

## Resumen por técnica

| Técnica | Cantidad |
|---|---:|
| Partición de Equivalencia (PE) | 58 |
| Análisis de Valores Límite (AVL) | 2 |
| **Total** | **60** |

---
