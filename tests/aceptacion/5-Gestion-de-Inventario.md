# Pruebas de Aceptación: Gestión de Inventario

Trazable a [`docs/Requisitos-funcionales/Gestion_inventario.md`](../../docs/Requisitos-funcionales/Gestion_inventario.md).

---

## Historia de Usuario 1: No puedo comprar un producto que ya no tiene stock

**Como** cliente,
**quiero** que el sistema me impida comprar productos agotados,
**para** no pagar por algo que la tienda no puede entregarme.

**Prioridad**: Alta
**Requisitos relacionados**: RF-INV-001 a RF-INV-006

```gherkin
Escenario: Producto sin stock no puede agregarse al carrito
  Dado que un producto tiene 0 unidades disponibles
  Y la tienda no permite vender productos sin stock
  Cuando intento agregarlo a mi carrito
  Entonces el sistema rechaza la operación
  Y me indica que el producto no está disponible

Escenario: Producto con stock puede comprarse normalmente
  Dado que un producto tiene unidades disponibles
  Cuando lo agrego al carrito
  Entonces la operación se completa sin problemas
```

**Verificado**: ✅ confirmado contra `upload/` (ver `tests/no-funcionales/k6/` y pruebas
funcionales de `InventoryManagerTest.php`, 129/129 exitosos).

---

## Historia de Usuario 2: Respetar la cantidad mínima de compra de un producto

**Como** vendedor,
**quiero** poder exigir una cantidad mínima de compra para ciertos productos,
**para** que las ventas al por mayor sean rentables.

**Prioridad**: Alta
**Requisitos relacionados**: RF-INV-005, RF-INV-017

```gherkin
Escenario: Rechazo por debajo de la cantidad mínima
  Dado que un producto tiene una cantidad mínima de compra de 5 unidades
  Cuando intento agregar solo 3 unidades al carrito
  Entonces el sistema rechaza la operación
  Y me informa la cantidad mínima requerida

Escenario: Aceptación en la cantidad mínima exacta
  Dado que un producto tiene una cantidad mínima de 5 unidades
  Cuando agrego exactamente 5 unidades
  Entonces el sistema acepta la operación sin problema
```

**Verificado**: ✅ confirmado contra `upload/` y en `InventoryManagerTest.php`.

---

## Historia de Usuario 3: El stock se descuenta solo cuando la compra se confirma

**Como** vendedor,
**quiero** que el inventario solo se reduzca cuando un pedido se confirma exitosamente,
**para** no perder ventas por productos "reservados" en carritos abandonados.

**Prioridad**: Alta
**Requisitos relacionados**: RF-INV-018, RF-INV-040

```gherkin
Escenario: El stock no cambia mientras el producto solo está en el carrito
  Dado que un cliente agrega un producto al carrito sin completar la compra
  Cuando reviso el inventario de ese producto
  Entonces la cantidad disponible sigue siendo la misma que antes

Escenario: El stock se reduce tras confirmar el pedido
  Dado que un cliente confirma exitosamente un pedido de 3 unidades
  Cuando reviso el inventario después de la confirmación
  Entonces la cantidad disponible se redujo exactamente en 3 unidades
```

**Verificado**: ✅ confirmado en `InventoryManagerTest.php`
(`testDecreaseProductQuantityAfterPurchase`).

---

## Historia de Usuario 4: Ver y filtrar el inventario como administrador

**Como** administrador de la tienda,
**quiero** ver y filtrar los productos por cantidad de stock,
**para** identificar rápidamente qué necesito reabastecer.

**Prioridad**: Alta
**Requisitos relacionados**: RF-INV-030, RF-INV-031, RF-INV-032

```gherkin
Escenario: Filtrar productos con bajo stock
  Dado que estoy en el panel de administración de productos
  Cuando filtro por un rango de cantidad entre 0 y 10 unidades
  Entonces veo únicamente los productos dentro de ese rango
  Y puedo priorizar cuáles reabastecer primero
```

**Verificado**: ✅ confirmado contra `upload/` en las pruebas de rendimiento (listado admin
filtrado por cantidad, p95 = 119.48 ms, ver
[`tests/no-funcionales/k6/README.md`](../no-funcionales/k6/README.md)).

**Nota importante**: durante las pruebas de sistema se detectó que el formulario de edición de
producto en el panel admin (`catalog/product.save`) falla con un error de servidor debido a un
evento mal configurado (`ssr.product.edit`). Esto **no afecta** la consulta/filtro de
inventario (que sí funciona), pero **sí impide guardar cambios de stock desde el panel admin**.
Ver el hallazgo original en el historial de pruebas de sistema si se requiere el detalle
técnico completo.

---

## Historia de Usuario 5: El catálogo del cliente siempre refleja el inventario real

**Como** cliente,
**quiero** que lo que veo disponible en la tienda coincida con lo que realmente puedo comprar,
**para** no llevarme sorpresas al momento de pagar.

**Prioridad**: Alta
**Requisitos relacionados**: RF-INV-036 a RF-INV-038

```gherkin
Escenario: El catálogo no muestra productos inactivos o fuera de fecha
  Dado que un producto está marcado como inactivo, o tiene una fecha de disponibilidad futura
  Cuando navego el catálogo de la tienda
  Entonces ese producto no aparece en los resultados

Escenario: El detalle de producto siempre muestra la cantidad actualizada
  Dado que el administrador reabastece un producto de 0 a 20 unidades
  Cuando visito la página de ese producto después del reabastecimiento
  Entonces veo la cantidad actualizada, no la cantidad anterior
```

**Verificado**: ✅ confirmado en `CatalogTest.php` (`testGetCategoryProductsInvalidCategory`,
cobertura de reglas de visualización) y en `InventoryManagerTest.php`.

---

## Registro de Ejecución

| Historia | Prioridad | Resultado | Evidencia / Incidente |
|---|---|---|---|
| 1. No comprar productos sin stock | Alta | ✅ Cumple | 129/129 tests, k6 |
| 2. Respetar cantidad mínima | Alta | ✅ Cumple | 129/129 tests |
| 3. Stock se descuenta solo al confirmar | Alta | ✅ Cumple | `testDecreaseProductQuantityAfterPurchase` |
| 4. Ver/filtrar inventario en admin | Alta | ✅ Cumple (consulta) / ⚠️ edición con error conocido | k6 p95=119ms; ver nota sobre `catalog/product.save` |
| 5. Catálogo refleja inventario real | Alta | ✅ Cumple | `CatalogTest.php`, `InventoryManagerTest.php` |
