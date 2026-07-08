# Pruebas de Integración — Gestión de Inventario

Archivo de pruebas: `GestionInventarioIntegrationTest.php`

## Objetivo

Validar la consistencia del stock, la disponibilidad y las reglas mínimas de compra sobre productos persistidos en OpenCart.

## Componentes / tablas integradas

| Componente | Tabla OpenCart |
|---|---|
| Producto | `oc_product` |
| Disponibilidad | `oc_product` |

## Precondiciones

- Debe existir al menos un producto en la base de datos.
- La base debe estar instalada y accesible desde CI.

## Variables de entorno

| Variable | Valor por defecto |
|---|---|
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `opencart` |
| `DB_USER` | `opencart` |
| `DB_PASS` | `opencart` |
| `DB_PREFIX` | `oc_` |

## Ejecución

```bash
vendor/bin/phpunit tests/integracion/gestion-inventario/GestionInventarioIntegrationTest.php
```

## Casos de prueba

| ID | Método | Descripción | Técnica | Resultado esperado |
|---|---|---|---|---|
| CI-INV-01 | `testActualizacionDeStockSePersisteEnProducto` | Actualiza stock y verifica persistencia. | Flujo integrado | La cantidad queda registrada en `oc_product`. |
| CI-INV-02 | `testProductoActivoYDisponibleSeMarcaComoComprable` | Producto activo, vigente y con stock. | Partición de equivalencia | El producto queda marcado como comprable. |
| CI-INV-03 | `testProductoConFechaFuturaNoEsComprable` | Producto con fecha futura. | Valor límite | El producto no puede comprarse todavía. |
| CI-INV-04 | `testCantidadExactaAlStockDisponibleEsAceptada` | Solicita exactamente el stock disponible. | Valor límite | La operación se acepta. |
| CI-INV-05 | `testCantidadSuperiorAlStockDisponibleEsRechazada` | Solicita más unidades que el stock. | Valor límite | La operación se rechaza. |

## Aislamiento

La suite usa transacciones y rollback para no contaminar el inventario real durante la ejecución.

## Relación con CI/CD

Esta suite debe ejecutarse después de preparar la base y antes de los smoke tests finales. Complementa la validación de checkout e inventario ya existente en el pipeline.
