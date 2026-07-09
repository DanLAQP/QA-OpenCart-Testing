# Pruebas de Integración — Totales y Descuentos

Archivo de pruebas: [`TotalesDescuentosIntegrationTest.php`](./TotalesDescuentosIntegrationTest.php)

## Objetivo

Verificar el **cálculo y la persistencia de los totales de una orden** en OpenCart
contra la base de datos real (no mocks). Se valida que el subtotal y el impuesto
se persistan en `oc_order_total`, que los descuentos por cupón y voucher se
apliquen correctamente y que el total final sea consistente con la suma de todos
sus componentes.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Orden | `oc_order` |
| Totales de la orden | `oc_order_total` |

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
phpunit --no-configuration tests/totales-descuentos/TotalesDescuentosIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-TOT-01** | `testPersisteSubtotalEImpuesto` | El subtotal y el impuesto se persisten como líneas en `oc_order_total`. | **Prueba de flujo integrado** (persistencia) | `sub_total = 100.00` y `tax = 18.00`. |
| **CI-TOT-02** | `testAplicaDescuentoPorCupon` | Un cupón negativo se aplica y reduce el total. | **Partición de equivalencia** (clase válida: descuento negativo) | `coupon = -10.00` y `total = 108.00` (100 + 18 − 10). |
| **CI-TOT-03** | `testAplicaVoucher` | Un voucher negativo se aplica y reduce el total. | **Partición de equivalencia** (clase válida: descuento negativo) | `voucher = -20.00` y `total = 98.00` (100 + 18 − 20). |
| **CI-TOT-04** | `testTotalFinalEsConsistenteConTodosLosComponentes` | El total final es consistente con subtotal + impuesto + cupón + voucher. | **Prueba de flujo integrado** (consistencia de componentes) | `total = 88.00` (100 + 18 − 10 − 20). |

---

## Componentes del total (order_total)

| `code` | Título | Ejemplo de valor | Efecto |
|--------|--------|------------------|--------|
| `sub_total` | Sub-Total | `100.00` | Base |
| `tax` | Tax | `18.00` | Suma |
| `coupon` | Coupon | `-10.00` | Resta (solo si ≠ 0) |
| `voucher` | Voucher | `-20.00` | Resta (solo si ≠ 0) |
| `total` | Total | `88.00` | Resultado final |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Prueba de flujo integrado | CI-TOT-01, CI-TOT-04 |
| Partición de equivalencia | CI-TOT-02, CI-TOT-03 |
| **Total** | **4 casos** |
