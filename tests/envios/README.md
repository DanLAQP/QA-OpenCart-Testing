# Pruebas de Integración — Envíos

Archivo principal de pruebas: `EnvioIntegrationTest.php`

## Objetivo

Validar la persistencia de dirección y método de envío, la cotización funcional mínima y el recálculo de totales cuando cambia el envío.

## Componentes / tablas integradas

| Componente | Tabla OpenCart |
|---|---|
| Pedido | `oc_order` |
| Totales de pedido | `oc_order_total` |

## Ejecución

```bash
vendor/bin/phpunit tests/integracion/envios/EnvioIntegrationTest.php
```

## Casos base

- Persistencia de dirección y método de envío
- Cotización simple de envío
- Reemplazo del método de envío
- Recalculo del total al cambiar el costo de envío
