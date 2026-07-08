# Pruebas de Integración — Pagos

Archivo de pruebas: [`PagoIntegrationTest.php`](./PagoIntegrationTest.php)

## Objetivo

Verificar la **interacción real entre el módulo de pagos y la orden** de OpenCart,
usando la base de datos real (no mocks). Se valida que el método de pago se
persista en la orden, que la confirmación del pago agregue el historial de estado
correspondiente y que el sistema sea resiliente ante timeouts e idempotente ante
reintentos.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Orden | `oc_order` |
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
phpunit --no-configuration tests/pagos/PagoIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-PAG-01** | `testPersisteMetodoDePagoEnLaOrden` | Al crear la orden se persisten `payment_code` y `payment_method` en `oc_order`. | **Prueba de flujo integrado** (persistencia) | `payment_code = 'cod'` y `payment_method = 'Cash On Delivery'`. |
| **CI-PAG-02** | `testConfirmacionDePagoAgregaHistorial` | Confirmar el pago agrega un registro en `oc_order_history`. | **Transición de estados** (Pendiente → Pagado) | Se crea 1 registro de historial con `order_status_id = 2`. |
| **CI-PAG-03** | `testTimeoutNoAgregaHistorialExitoso` | La pasarela excede el timeout durante el procesamiento del pago. | **Prueba de resiliencia** (tolerancia a latencia / timeout) | El pago retorna `false` y no se crea ningún registro de historial. |
| **CI-PAG-04** | `testReintentoNoDuplicaHistorialEquivalente` | Confirmar dos veces el mismo pago (mismo estado y comentario). | **Prueba de idempotencia** (regla de negocio: no duplicar historial equivalente) | Solo existe 1 registro de historial pese al reintento. |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Prueba de flujo integrado | CI-PAG-01 |
| Transición de estados | CI-PAG-02 |
| Prueba de resiliencia | CI-PAG-03 |
| Prueba de idempotencia | CI-PAG-04 |
| **Total** | **4 casos** |
