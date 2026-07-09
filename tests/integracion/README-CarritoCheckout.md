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
phpunit --no-configuration tests/integracion/CarritoCheckoutIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-CAR-01** | `testFlujoCompletoCarritoAOrden` | Agregar un producto real al carrito y confirmar el checkout; se crean las filas en `oc_order` y `oc_order_product`. | **Prueba de flujo integrado** (camino feliz / end-to-end) | La orden se crea con 1 línea, cantidad 2 y `total` coherente con los totales calculados. |
| **CI-CAR-02** | `testConfirmarOrdenDescuentaStockDelInventario` | Al confirmar la orden se descuenta el stock en `oc_product`. | **Transición de estados** (Stock Disponible → Stock Reducido) | Stock inicial 20, compra 5 → stock final = 15. |
| **CI-CAR-03** | `testCalculoDeTotalesConSubtotalImpuestoYEnvio` | El total persistido es coherente con subtotal + impuesto + envío. | **Partición de equivalencia** (clase válida de valores monetarios) | `subtotal`, `tax` y `total` coinciden con lo calculado (impuesto 18% + envío 20.00). |
| **CI-CAR-04** | `testCantidadEnLimiteExactoDelStockEsAceptada` | Comprar exactamente el stock disponible. | **Análisis de valores límite** (límite superior válido: `qty = stock`) | La orden se crea y el inventario queda en 0. |
| **CI-CAR-05** | `testCantidadSuperiorAlStockEsRechazada` | Solicitar una unidad más que el stock disponible. | **Análisis de valores límite** (límite superior no válido: `qty = stock + 1`) | Se lanza `RuntimeException` («Stock insuficiente…») y no se crea la orden. |
| **CI-CAR-06** | `testRechazaProductoConIdNoNumerico` | Agregar al carrito un `product_id` no numérico. | **Prueba sintáctica** (validación de tipos de entrada) | Se lanza `InvalidArgumentException` antes de tocar la BD. |
| **CI-CAR-07** | `testRechazaCheckoutConCarritoVacio` | Confirmar el checkout sin productos. | **Prueba semántica** (regla de negocio: no se factura un carrito vacío) | Se lanza `RuntimeException` («El carrito no contiene productos.»). |
| **CI-CAR-08** | `testDisponibilidadCheckoutSegunStockYEstado` | Regla combinada stock / estado / permitir-sin-stock que decide si un producto es comprable. | **Tabla de decisión** | Se cumplen las 4 combinaciones de la tabla (comprable / no comprable). |
| **CI-CAR-09** | `testActualizarCantidadRecalculaTotalDeLinea` | Modificar la cantidad del carrito antes de confirmar recalcula el total de línea en `oc_order_product`. | **Prueba de flujo integrado** | `quantity = 4` y `total` de línea = `precio × 4`. |
| **CI-CAR-10** | `testResilienciaAnteTimeoutDePasarelaDePago` | La pasarela de pago excede el timeout durante la confirmación. | **Prueba de resiliencia** (tolerancia a latencia / timeout) | El pago no se realiza, no se crea la orden y el stock permanece intacto. |

---

## Tabla de decisión (CI-CAR-08)

| Stock | Estado | Permitir sin stock | ¿Comprable? |
|-------|--------|--------------------|-------------|
| 10 | Activo | No | ✅ Sí |
| 0 | Activo | No | ❌ No |
| 0 | Activo | Sí | ✅ Sí |
| 10 | Inactivo | No | ❌ No |

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
