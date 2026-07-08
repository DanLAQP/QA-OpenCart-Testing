# Pruebas de Integración — Totales y Descuentos

Archivo principal de pruebas: `TotalesDescuentosIntegrationTest.php`

## Objetivo

Validar subtotal, impuesto, descuento por cupón, voucher y total final en `oc_order_total`, asegurando coherencia matemática básica.

## Componentes / tablas integradas

| Componente | Tabla OpenCart |
|---|---|
| Pedido | `oc_order` |
| Totales | `oc_order_total` |

## Ejecución

```bash
vendor/bin/phpunit tests/integracion/totales-descuentos/TotalesDescuentosIntegrationTest.php
```

## Casos base

- Persistencia de subtotal e impuesto
- Aplicación de descuento por cupón
- Aplicación de voucher
- Consistencia del total final
