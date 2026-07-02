# Reporte de Incidente — Integración API

**ID**: INC-INTEGRACION-003
**Fecha**: 2026-07-01
**Autor**: Equipo QA
**Frontera afectada**: api/order.php::confirm() (Subsistema A/orquestador) → model_checkout_order::addOrder() (Subsistema B)
**Caso de prueba**: Resiliencia
**Request de Postman**: `03 - Caso Resiliencia / confirm() a traves de proxy con latencia`

## Resultado Esperado

Cuando el Subsistema B (persistencia del pedido, dentro de `confirm()`) tarda anormalmente en
responder (simulado con `tests/integracion/proxy/latency-proxy.js`, latencia inyectada
configurable), el cliente debería:
1. Cortar la espera tras su propio timeout configurado, sin bloquearse indefinidamente.
2. El servidor no debería dejar el pedido en un estado parcialmente creado (huérfano en
   `oc_order` sin historial, o con historial pero sin cabecera) si el cliente corta la conexión
   a mitad de proceso.

## Resultado Real

**Lado cliente**: verificado y correcto. Con `--timeout-request` de Newman configurado por
debajo de `LATENCY_MS` del proxy (1500 ms vs. 3000 ms de latencia inyectada), el cliente
aborta la espera con `ESOCKETTIMEDOUT` en vez de bloquearse. El comportamiento del cliente HTTP
es adecuado.

**Lado servidor**: **no verificado en este ciclo de pruebas**. `confirm()`
(`opencart/catalog/controller/api/order.php:347-672`) ejecuta en un solo hilo PHP-FPM/Apache
síncrono:
1. Validaciones (customer, cart, addresses, payment/shipping method) — líneas 376-435.
2. `model_checkout_order->addOrder($order_data)` — línea 638.
3. `model_checkout_order->addHistory($order_id, $order_status_id)` — línea 656.

Si el cliente corta la conexión (timeout) **después** de que el servidor ya ejecutó
`addOrder()` pero **antes** de `addHistory()`, PHP normalmente sigue ejecutando el script hasta
el final salvo que `ignore_user_abort()` esté deshabilitado explícitamente (por defecto en
PHP, la desconexión del cliente no interrumpe la ejecución del script salvo que se compruebe
`connection_aborted()`). Esto significa que es plausible que se cree un `order_id` en `oc_order`
sin que el cliente jamás reciba el `order_id`, dejando un pedido "fantasma" en el sistema.

En este entorno de pruebas no fue posible completar el flujo end-to-end (customer, payment
address, shipping method, payment method) requerido para que `confirm()` llegue a
`addOrder()`, por lo que el escenario de "pedido huérfano por timeout" queda como **hipótesis
razonada, no confirmada empíricamente**. Se documenta como pendiente de verificación con un
carrito y checkout completos.

## Severidad

**Media** (hallazgo de diseño, no confirmado) — si se confirma, sería **Alta**: pedidos
fantasma sin que el cliente lo sepa afectan inventario/reportes sin visibilidad para el
usuario ni para soporte.

## Impacto

Si se confirma la hipótesis: el cliente (frontend, integración externa) no tiene forma de
saber si el pedido se creó o no tras un timeout, y no existe ningún mecanismo de idempotencia
visible en `confirm()` para reintentar de forma segura (el `order_id=0` en el request siempre
crea un pedido nuevo si no se pasa `order_id`).

## Causa raíz

`opencart/catalog/controller/api/order.php:635-656` — no hay verificación de
`connection_aborted()` ni transacción atómica que agrupe `addOrder()` + `addHistory()` con
posibilidad de rollback si el cliente se desconecta a mitad de proceso.

## Recomendación

1. Confirmar empíricamente completando un checkout real (customer + payment_address +
   shipping_method + payment_method) y repitiendo el Caso 3 con `LATENCY_MS` alto,
   verificando `oc_order` tras el timeout del cliente.
2. Si se confirma el pedido huérfano: evaluar mecanismo de idempotencia (ej. un
   `idempotency_key` enviado por el cliente) para que reintentos tras timeout no dupliquen
   pedidos, y/o envolver `addOrder()` + `addHistory()` en una transacción de BD.
