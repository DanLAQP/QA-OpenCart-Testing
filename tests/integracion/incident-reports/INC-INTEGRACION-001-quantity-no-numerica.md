# Reporte de Incidente — Integración API

**ID**: INC-INTEGRACION-001
**Fecha**: 2026-07-01
**Autor**: Equipo QA
**Frontera afectada**: api/cart.php (Subsistema A) → api/order.php::confirm() (Subsistema B)
**Caso de prueba**: Sintáctico
**Request de Postman**: `01 - Caso Sintactico / addProduct con quantity no numerica (string)`

## Resultado Esperado

Al enviar `quantity=abc` (tipo de dato erróneo, se espera un entero) a
`POST api/cart.addProduct`, la API debería rechazar la solicitud con un error de validación
explícito indicando que `quantity` no es un número válido, sin alterar el estado del carrito.

## Resultado Real

`api/cart.php::addProduct()` (línea 162-166) castea el valor directamente con `(int)`:

```php
if (isset($this->request->post['quantity'])) {
    $quantity = (int)$this->request->post['quantity'];
} else {
    $quantity = 1;
}
```

`(int)"abc"` en PHP se convierte silenciosamente en `0`. El producto se agrega al carrito
con `quantity = 0` y la API devuelve `{"success": "..."}` como si la operación hubiese sido
exitosa, sin ningún mensaje de error. El cliente (front-end o integración externa) no tiene
forma de saber que el dato enviado era inválido.

## Evidencia

```
Request:
POST {{base_url}}/index.php?route=api/cart.addProduct
Body (x-www-form-urlencoded):
  product_id=28
  quantity=abc

Response (HTTP 200):
{
  "success": "Success: You have modified your shopping cart!"
}
```

Verificación posterior con `GET api/order&call=cart` muestra el producto 28 en el carrito
con `quantity: 0`.

## Severidad

**Media** — no rompe el sistema ni genera un pedido corrupto directamente (los totales/stock
se recalculan en 0), pero permite que datos claramente inválidos pasen desapercibidos,
generando entradas de carrito inconsistentes y una experiencia confusa para integraciones
de terceros que consuman esta API.

## Impacto

- Un cliente API mal implementado (o un ataque deliberado) puede poblar el carrito con
  cantidades = 0 sin recibir ninguna señal de error.
- Si en `confirm()` no se revalida la cantidad mínima por línea de producto (más allá del
  `hasMinimum()` global), podría generarse un pedido con líneas de producto en cantidad 0.

## Causa raíz

`opencart/catalog/controller/api/cart.php:162-166` — cast directo a `(int)` sin validar que
el valor original sea numérico (`is_numeric()` ausente).

## Recomendación

Validar `is_numeric($this->request->post['quantity'])` antes de castear, y devolver
`$output['error']['quantity']` si el valor no es numérico o es menor a 1, replicando el
patrón de validación ya usado para `product_id` inexistente en el mismo método.
