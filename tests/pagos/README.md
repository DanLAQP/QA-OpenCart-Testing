# Pruebas de Integración — Pagos

Archivo principal de pruebas: `PagoIntegrationTest.php`

## Objetivo

Validar la persistencia del método de pago, la confirmación del pago, el manejo de timeout y la prevención de duplicación funcional durante reintentos.

## Componentes / tablas integradas

| Componente | Tabla OpenCart |
|---|---|
| Orden | `oc_order` |
| Historial de orden | `oc_order_history` |

## Precondiciones

- OpenCart debe estar instalado en la base de datos.
- Debe existir la tabla `oc_order`.
- Debe existir la tabla `oc_order_history`.

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
vendor/bin/phpunit tests/integracion/pagos/PagoIntegrationTest.php
```

## Casos base

- Persistencia del método de pago en la orden
- Confirmación de pago con creación de historial
- Timeout de pago sin confirmación ni historial exitoso
- Reintento de confirmación sin duplicar historial equivalente

## Relación con CI/CD

Esta suite está pensada como base ejecutable para GitHub Actions después de instalar OpenCart y antes de smoke tests finales.
