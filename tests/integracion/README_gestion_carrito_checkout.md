# Pruebas de Integración — Carrito y Checkout

Archivo de pruebas: [`CarritoCheckoutIntegrationTest.php`](./CarritoCheckoutIntegrationTest.php)

## Objetivo

Verificar la **interacción real entre componentes** del flujo de compra de OpenCart,
usando la base de datos real (no mocks). Se valida que el flujo
**Carrito → Checkout → Orden** mantenga la consistencia entre el inventario y los
datos persistidos.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Inventario | `oc_product` |
| Orden | `oc_order` |
| Detalle de orden | `oc_order_product` |

## Estrategia de aislamiento

Cada prueba se ejecuta dentro de una **transacción** (`beginTransaction()` en `setUp()`)
que se **revierte** (`rollBack()` en `tearDown()`). Así la base de datos queda intacta
tras la ejecución y las pruebas son repetibles e independientes.

## Configuración (variables de entorno)

| Variable | Valor por defecto |
|----------|-------------------|
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `opencart` |
| `DB_USER` | `opencart` |
| `DB_PASS` | `opencart` |
| `DB_PREFIX` | `oc_` |

## Ejecución

```bash
vendor/bin/phpunit tests/integracion/carrito-checkout/CarritoCheckoutIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-CAR-01** | `testFlujoCompletoCarritoAOrden` | Agregar un producto real al carrito y confirmar el checkout; se crean las filas en `oc_order` y `oc_order_product`. | **Prueba de flujo integrado** (camino feliz / end-to-end) | La orden se crea con una línea de detalle, la cantidad corresponde a la comprada y el total coincide con los valores calculados. |
| **CI-CAR-02** | `testConfirmarOrdenDescuentaStockDelInventario` | Al confirmar la orden se descuenta automáticamente el inventario del producto adquirido. | **Transición de estados** (Stock Disponible → Stock Reducido) | El stock disminuye exactamente en la cantidad comprada y queda persistido en `oc_product`. |
| **CI-CAR-03** | `testCalculoDeTotalesConSubtotalImpuestoYEnvio` | Se valida el cálculo de subtotal, impuesto, costo de envío y total final de la compra. | **Partición de equivalencia** (clase válida de valores monetarios) | Los valores de `subtotal`, `tax` y `total` coinciden con los cálculos esperados. |
| **CI-CAR-04** | `testCantidadEnLimiteExactoDelStockEsAceptada` | Comprar exactamente la cantidad disponible en inventario. | **Análisis de valores límite** (límite superior válido: `qty = stock`) | La orden se genera correctamente y el inventario queda en cero. |
| **CI-CAR-05** | `testCantidadSuperiorAlStockEsRechazada` | Intentar comprar una unidad más que el stock disponible. | **Análisis de valores límite** (límite superior no válido: `qty = stock + 1`) | Se produce una `RuntimeException` indicando stock insuficiente y la orden no es creada. |
| **CI-CAR-06** | `testRechazaProductoConIdNoNumerico` | Agregar un producto utilizando un identificador y cantidad no numéricos. | **Prueba sintáctica** (validación de tipos de entrada) | Se lanza una `InvalidArgumentException` antes de realizar cualquier operación sobre la base de datos. |
| **CI-CAR-07** | `testRechazaCheckoutConCarritoVacio` | Intentar confirmar una compra sin productos en el carrito. | **Prueba semántica** (regla de negocio: carrito obligatorio) | Se lanza una `RuntimeException` indicando que el carrito no contiene productos. |
| **CI-CAR-08** | `testDisponibilidadCheckoutSegunStockYEstado` | Evaluar las reglas de negocio que determinan si un producto puede comprarse según el stock, estado y configuración de backorder. | **Tabla de decisión** | Las cuatro combinaciones posibles producen el resultado esperado (comprable / no comprable). |
| **CI-CAR-09** | `testActualizarCantidadRecalculaTotalDeLinea` | Modificar la cantidad del producto antes del checkout para recalcular el importe persistido de la línea. | **Prueba de flujo integrado** | La cantidad almacenada y el total de la línea corresponden al nuevo valor actualizado. |
| **CI-CAR-10** | `testResilienciaAnteTimeoutDePasarelaDePago` | Simular un timeout durante la comunicación con la pasarela de pago. | **Prueba de resiliencia** (tolerancia a latencia / timeout) | La orden no se crea, el pago falla y el inventario permanece sin modificaciones. |

---

## Tabla de decisión (CI-CAR-08)

| Stock | Estado | Permitir sin stock | ¿Comprable? |
|-------|--------|--------------------|-------------|
| 10 | Activo | No |  Sí |
| 0 | Activo | No |  No |
| 0 | Activo | Sí |  Sí |
| 10 | Inactivo | No |  No |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Prueba de flujo integrado | CI-CAR-01, CI-CAR-09 |
| Transición de estados | CI-CAR-02 |
| Partición de equivalencia | CI-CAR-03 |
| Análisis de valores límite | CI-CAR-04, CI-CAR-05 |
| Prueba sintáctica | CI-CAR-06 |
| Prueba semántica | CI-CAR-07 |
| Tabla de decisión | CI-CAR-08 |
| Prueba de resiliencia | CI-CAR-10 |
| **Total** | **10 casos** |