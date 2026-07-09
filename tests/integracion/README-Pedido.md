# Pruebas de Integración — Pedidos

Archivo de pruebas: [`PedidoIntegrationTest.php`](./PedidoIntegrationTest.php)

## Objetivo

Verificar la **creación y evolución de un pedido** de OpenCart contra la base de
datos real (no mocks). Se valida que un pedido se cree de forma completa (orden,
líneas y totales), que los cambios de estado registren el historial, que el
historial se recupere en orden cronológico y que exista consistencia entre las
líneas del pedido y el total registrado.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Orden | `oc_order` |
| Detalle de orden | `oc_order_product` |
| Totales de la orden | `oc_order_total` |
| Historial de la orden | `oc_order_history` |

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
phpunit --no-configuration tests/pedidos/PedidoIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-PED-01** | `testCreacionCompletaDePedidoConLineasYTotales` | Crear un pedido completo con su línea de producto y su total. | **Prueba de flujo integrado** (camino feliz / end-to-end) | `order_id > 0`, 1 línea en `oc_order_product` y totales no vacíos en `oc_order_total`. |
| **CI-PED-02** | `testCambioDeEstadoAgregaHistorial` | Actualizar el estado del pedido agrega un registro de historial. | **Transición de estados** | Se crea 1 registro de historial con `order_status_id = 3`. |
| **CI-PED-03** | `testHistorialDelPedidoSeRecuperaEnOrdenCronologico` | Varios cambios de estado se recuperan en orden cronológico. | **Prueba de flujo integrado** (secuencia / ordenamiento) | 2 registros de historial: «Creado» antes que «Completado». |
| **CI-PED-04** | `testConsistenciaEntreLineasYTotalRegistrado` | La suma de los totales de las líneas coincide con el total registrado. | **Partición de equivalencia** (consistencia de valores monetarios) | `Σ total(líneas) == total` de `oc_order_total` (redondeado a 4 decimales). |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Prueba de flujo integrado | CI-PED-01, CI-PED-03 |
| Transición de estados | CI-PED-02 |
| Partición de equivalencia | CI-PED-04 |
| **Total** | **4 casos** |
