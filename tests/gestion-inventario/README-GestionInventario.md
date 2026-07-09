# Pruebas de Integración — Gestión de Inventario

Archivo de pruebas: [`GestionInventarioIntegrationTest.php`](./GestionInventarioIntegrationTest.php)

## Objetivo

Verificar la **gestión del inventario** de OpenCart contra la base de datos real
(no mocks). Se valida que la actualización de stock se persista en `oc_product` y
que la regla de disponibilidad (estado activo, fecha de disponibilidad y stock
positivo) determine correctamente si un producto es comprable, así como la
validación de cantidad solicitada frente al stock existente.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Inventario | `oc_product` |

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
phpunit --no-configuration tests/gestion-inventario/GestionInventarioIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-INV-01** | `testActualizacionDeStockSePersisteEnProducto` | Actualizar la cantidad de un producto se persiste en `oc_product`. | **Prueba de flujo integrado** (persistencia) | Tras fijar 12, la lectura del stock devuelve 12. |
| **CI-INV-02** | `testProductoActivoYDisponibleSeMarcaComoComprable` | Producto activo, con stock y fecha de disponibilidad pasada. | **Tabla de decisión** (estado + fecha + stock) | El producto se evalúa como comprable (`true`). |
| **CI-INV-03** | `testProductoConFechaFuturaNoEsComprable` | Producto activo con `date_available` futura. | **Análisis de valores límite** (frontera de la fecha de disponibilidad) | El producto no es comprable (`false`). |
| **CI-INV-04** | `testCantidadExactaAlStockDisponibleEsAceptada` | Solicitar exactamente el stock disponible. | **Análisis de valores límite** (límite superior válido: `qty = stock`) | La solicitud es aceptada (`true`). Stock 3, se piden 3. |
| **CI-INV-05** | `testCantidadSuperiorAlStockDisponibleEsRechazada` | Solicitar una unidad más que el stock disponible. | **Análisis de valores límite** (límite superior no válido: `qty = stock + 1`) | La solicitud es rechazada (`false`). Stock 3, se piden 4. |

---

## Tabla de decisión (CI-INV-02 / CI-INV-03)

| Estado | Fecha disponible | Stock | ¿Comprable? |
|--------|------------------|-------|-------------|
| Activo | Pasada / hoy | > 0 | ✅ Sí |
| Activo | Futura | > 0 | ❌ No |
| Inactivo | Pasada | > 0 | ❌ No |
| Activo | Pasada | 0 | ❌ No |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Prueba de flujo integrado | CI-INV-01 |
| Tabla de decisión | CI-INV-02 |
| Análisis de valores límite | CI-INV-03, CI-INV-04, CI-INV-05 |
| **Total** | **5 casos** |
