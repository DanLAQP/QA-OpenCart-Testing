# Pruebas de Integración — Pedidos

Archivo principal de pruebas: `PedidoIntegrationTest.php`

## Objetivo

Validar la creación de pedidos, el cambio de estado, el historial y la consistencia entre `oc_order`, `oc_order_product`, `oc_order_total` y `oc_order_history`.

## Componentes / tablas integradas

| Componente | Tabla OpenCart |
|---|---|
| Pedido | `oc_order` |
| Detalle de pedido | `oc_order_product` |
| Totales | `oc_order_total` |
| Historial | `oc_order_history` |

## Ejecución

```bash
vendor/bin/phpunit tests/integracion/pedidos/PedidoIntegrationTest.php
```

## Casos base

- Creación de pedido completa con detalle y totales
- Cambio de estado con historial
- Lectura de historial del pedido
- Consistencia entre suma de líneas y total registrado
