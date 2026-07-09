# Pruebas de Integración — Envíos

Archivo de pruebas: [`EnvioIntegrationTest.php`](./EnvioIntegrationTest.php)

## Objetivo

Verificar la **interacción real** entre la gestión de envíos y la persistencia de las
órdenes de OpenCart utilizando la base de datos real (sin mocks). La suite valida
el flujo **creación de la orden → asignación del método de envío → registro del
costo de envío → actualización del método → recálculo de los totales**, asegurando
la consistencia de la información almacenada.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Orden | `oc_order` |
| Totales de la orden | `oc_order_total` |

## Estrategia de aislamiento

Cada prueba se ejecuta dentro de una **transacción** (`beginTransaction()` en `setUp()`)
que se **revierte** (`rollBack()` en `tearDown()`). De esta forma, la base de datos
permanece intacta al finalizar cada prueba, garantizando ejecuciones repetibles,
independientes y libres de efectos secundarios.

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
vendor/bin/phpunit tests/integracion/envios/EnvioIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-ENV-01** | `testPersisteDireccionYMetodoDeEnvio` | Crear una orden verificando que la dirección, el método y el código de envío se almacenen correctamente. | **Prueba de flujo integrado** (camino feliz) | La orden persiste correctamente `shipping_method`, `shipping_code` y la dirección de envío registrada. |
| **CI-ENV-02** | `testCotizacionSimpleDeEnvioSePersisteEnOrderTotal` | Registrar una cotización de envío y verificar que el costo quede almacenado en los totales de la orden. | **Prueba de persistencia de datos** | El registro correspondiente al código `shipping` en `oc_order_total` conserva el importe esperado del envío. |
| **CI-ENV-03** | `testCambioDeMetodoDeEnvioActualizaCodigoYMonto` | Cambiar el método de envío de una orden ya creada y comprobar la actualización de la información asociada. | **Transición de estados** (Método de envío inicial → Método actualizado) | Se actualizan correctamente el método de envío, su código y el importe registrado para el envío. |
| **CI-ENV-04** | `testRecalculoDeTotalesCuandoCambiaElEnvio` | Modificar el costo del envío y verificar el recálculo automático del total de la orden. | **Prueba de flujo integrado** (regla de negocio) | El valor almacenado en `total` corresponde al subtotal más el nuevo costo de envío. |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Prueba de flujo integrado | CI-ENV-01, CI-ENV-04 |
| Prueba de persistencia de datos | CI-ENV-02 |
| Transición de estados | CI-ENV-03 |
| **Total** | **4 casos** |