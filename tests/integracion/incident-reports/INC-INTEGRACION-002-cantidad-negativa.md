# Reporte de Incidente — Integración API

**ID**: INC-INTEGRACION-002
**Fecha**: 2026-07-01
**Autor**: Equipo QA
**Frontera afectada**: api/cart.php (Subsistema A) → api/order.php::confirm() (Subsistema B)
**Caso de prueba**: Semántico
**Request de Postman**: `02 - Caso Semantico / product_add con cantidad negativa`

## Resultado Esperado

Al enviar `quantity=-5` (valor legal en formato — es un entero — pero ilógico para el negocio,
ya que no existen cantidades negativas de producto) a `POST api/order?call=product_add`, la API
debería rechazar la solicitud con un error de validación de cantidad mínima/inválida.

## Resultado Real

La API responde **200 OK** con `"success"`, agregando el producto al carrito con
`quantity: "-5"`. Verificado de forma reproducible en 5 corridas consecutivas de la colección
Postman/Newman (`tests/integracion/postman/opencart-integration.postman_collection.json`,
carpeta "02 - Caso Semantico").

```
Request:
POST index.php?route=api/order&call=product_add ...
Body: product_id=28, quantity=-5

Response (HTTP 200):
{"success":"Success: You have modified your shopping cart!", "products":[...], ...}
```

Causa: en `api/cart.php::addProduct()` (líneas 234-247), el chequeo de stock es:

```php
if (!$this->config->get('config_stock_checkout') && (!$product_info['quantity'] || ($product_info['quantity'] < $product_total))) {
    $output['error']['warning'] = $this->language->get('error_stock');
}
```

Con `$product_total = -5`, la condición `$product_info['quantity'] < $product_total` (ej.
`939 < -5`) es `false`, por lo que nunca se marca error. No existe ningún chequeo explícito de
`quantity > 0` en todo el método.

## Severidad

**Alta** — una cantidad negativa que llegue hasta `confirm()` puede alterar `sub_total` y
`total` del pedido de forma incorrecta (resta en vez de suma), afectando directamente el monto
que se persiste en `oc_order` vía `model_checkout_order::addOrder()`.

## Impacto

- Un pedido confirmado con líneas de producto en cantidad negativa puede generar totales
  incorrectos (descuentos no autorizados) en el momento de persistir en `checkout/order`.
- Riesgo de abuso: un cliente API podría potencialmente manipular el total pagado combinando
  productos en cantidad positiva y negativa para reducir el total a cobrar.

## Causa raíz

`opencart/catalog/controller/api/cart.php:150-266` (método `addProduct()`) — no valida que
`quantity` sea un entero positivo antes de llamar a `$this->cart->add()` (línea 260). El mismo
patrón se repite en `index()` (líneas 16-141).

## Recomendación

Agregar una validación explícita `if ($quantity < 1) { $output['error']['quantity'] = ...; }`
en ambos métodos (`index()` y `addProduct()`) de `api/cart.php`, antes de cualquier chequeo de
stock, replicando el patrón de validación ya usado para `product_id` inexistente.
