# Pruebas de Aceptación: Carrito de Compras

Trazable a [`docs/Requisitos-funcionales/Carrito_compras.md`](../../docs/Requisitos-funcionales/Carrito_compras.md).

---

## Historia de Usuario 1: Agregar un producto al carrito

**Como** cliente,
**quiero** agregar un producto que me interesa al carrito,
**para** poder comprarlo más adelante junto con otros productos.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CART-025 a RF-CART-036

```gherkin
Escenario: Agregar un producto con stock disponible
  Dado que el producto "HTC Touch HD" tiene stock disponible
  Cuando lo agrego al carrito desde su página de detalle
  Entonces el sistema confirma que el producto fue agregado
  Y el ícono del carrito refleja la nueva cantidad de artículos

Escenario: No se puede agregar un producto sin seleccionar una opción obligatoria
  Dado que un producto requiere seleccionar una opción obligatoria (por ejemplo, talla)
  Cuando intento agregarlo al carrito sin seleccionar esa opción
  Entonces el sistema rechaza la operación
  Y me indica qué opción debo seleccionar
```

**Verificado**: ✅ confirmado contra `upload/` — agregar un producto responde con mensaje de
éxito y el carrito refleja la cantidad correcta.

---

## Historia de Usuario 2: Ver y entender el contenido de mi carrito

**Como** cliente,
**quiero** ver claramente qué productos tengo en el carrito y cuánto voy a pagar,
**para** decidir si continúo con la compra o sigo agregando productos.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CART-001 a RF-CART-022

```gherkin
Escenario: Ver el resumen del carrito con totales
  Dado que tengo 2 productos distintos en mi carrito
  Cuando accedo a la pantalla del carrito
  Entonces veo cada producto con su imagen, cantidad y precio
  Y veo el total general, incluyendo impuestos si aplican

Escenario: Advertencia de cantidad mínima no alcanzada
  Dado que un producto en mi carrito requiere una cantidad mínima de 5 unidades
  Y actualmente tengo solo 2 unidades de ese producto
  Cuando veo mi carrito
  Entonces el sistema me advierte que no cumplo la cantidad mínima para ese producto
```

**Verificado**: ✅ confirmado contra `upload/` — el carrito muestra correctamente productos,
cantidades y totales vía `checkout/cart.list`.

---

## Historia de Usuario 3: Modificar la cantidad de un producto en el carrito

**Como** cliente,
**quiero** cambiar la cantidad de un producto sin tener que eliminarlo y agregarlo de nuevo,
**para** ajustar mi compra de forma rápida y cómoda.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CART-037 a RF-CART-041

```gherkin
Escenario: Aumentar la cantidad de un producto ya agregado
  Dado que tengo 1 unidad de un producto en el carrito
  Cuando actualizo la cantidad a 3 unidades
  Entonces el carrito refleja las 3 unidades
  Y el total se recalcula automáticamente

Escenario: Reducir la cantidad a cero elimina el producto
  Dado que tengo un producto en el carrito
  Cuando actualizo su cantidad a 0
  Entonces el producto se elimina del carrito
```

**Verificado**: ✅ confirmado contra `upload/` — `checkout/cart.edit` actualiza la cantidad
correctamente.

---

## Historia de Usuario 4: Eliminar un producto del carrito

**Como** cliente,
**quiero** quitar un producto que ya no me interesa comprar,
**para** mantener mi carrito con solo lo que realmente quiero llevar.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CART-042 a RF-CART-045

```gherkin
Escenario: Eliminar un producto y seguir con otros en el carrito
  Dado que tengo 2 productos distintos en mi carrito
  Cuando elimino uno de ellos
  Entonces el carrito muestra solo el producto restante
  Y el total se actualiza sin incluir el producto eliminado

Escenario: Eliminar el último producto vacía el carrito
  Dado que tengo un solo producto en mi carrito
  Cuando lo elimino
  Entonces el carrito queda vacío
  Y el sistema me indica que no tengo productos para comprar
```

**Verificado**: ✅ confirmado contra `upload/` — `checkout/cart.remove` elimina correctamente
el producto.

---

## Historia de Usuario 5: Mi carrito persiste aunque cierre sesión o cambie de dispositivo

**Como** cliente que compra como invitado y luego decide crear una cuenta o iniciar sesión,
**quiero** que los productos que ya agregué no se pierdan,
**para** no tener que volver a buscarlos y agregarlos de nuevo.

**Prioridad**: Media
**Requisitos relacionados**: RF-CART-046 a RF-CART-050

```gherkin
Escenario: El carrito de invitado se transfiere al iniciar sesión
  Dado que agregué productos al carrito sin haber iniciado sesión
  Cuando inicio sesión con una cuenta existente en la misma sesión de navegador
  Entonces los productos que agregué como invitado siguen en mi carrito
```

**Estado de este criterio**: ⏳ Pendiente de verificación manual (requiere flujo completo de
invitado → login en la misma sesión de navegador, no verificable solo con peticiones HTTP
aisladas).

---

## Registro de Ejecución

| Historia | Prioridad | Resultado | Evidencia / Incidente |
|---|---|---|---|
| 1. Agregar producto al carrito | Alta | ✅ Cumple | Verificado contra `upload/` |
| 2. Ver contenido y totales del carrito | Alta | ✅ Cumple | Verificado contra `upload/` |
| 3. Modificar cantidad en el carrito | Alta | ✅ Cumple | Verificado contra `upload/` |
| 4. Eliminar producto del carrito | Alta | ✅ Cumple | Verificado contra `upload/` |
| 5. Persistencia del carrito de invitado | Media | ⏳ Pendiente | Requiere prueba manual de sesión |
