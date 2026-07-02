# Pruebas de Integración — Frontera Carrito → Checkout/Pedido

Pruebas de integración sobre la API REST de OpenCart, enfocadas en verificar la cohesión y el
flujo de datos entre el **Subsistema A (Carrito)** y el **Subsistema B (Confirmación de Pedido)**.

## 1. Mapeo de la frontera

- **Subsistema A**: [`opencart/catalog/controller/api/cart.php`](../../opencart/catalog/controller/api/cart.php)
  Gestiona el carrito de sesión: `addProduct()`, `getProducts()`, `getTotals()`. Usa
  `model_catalog_product` para validar existencia/stock/opciones de cada producto.
- **Frontera**: [`opencart/catalog/controller/api/order.php`](../../opencart/catalog/controller/api/order.php),
  método `confirm()` (líneas 347-672):
  - L.351 → invoca `api/cart` para validar que el carrito no tenga errores.
  - L.382-389 → gate-keeping con `hasProducts()`, `hasStock()`, `hasMinimum()`.
  - L.534 → `$this->cart->getProducts()` entrega el array de productos del carrito, que se
    transforma en `$order_data['products']`.
  - L.566-568 → `model_checkout_cart::getTotals()` entrega totales/impuestos que se mezclan
    en `$order_data`.
  - L.635-644 → `model_checkout_order::addOrder()` / `editOrder()`: el **Subsistema B**
    persiste el pedido en BD consumiendo todo lo que el carrito generó.
  - L.667-669 → tras confirmar, se vuelve a invocar `api/cart.getProducts` / `getTotals`
    para reflejar el JSON final al cliente.
- **Subsistema B**: `model_checkout_order` (persistencia del pedido en `oc_order`).

Flujo probado: `POST api/cart.addProduct` → puebla el carrito de sesión → `POST
api/order?call=confirm` → confirma el pedido → se verifica que lo persistido en `oc_order`
coincide con lo que el carrito entregó.

## 2. Autenticación / sesión

Hallazgo clave durante la construcción de las pruebas: **`api/cart` no es accesible
directamente** desde fuera. `catalog/controller/startup/api.php:21-29` solo permite acceso
público a las rutas `api/order` y `api/subscription`; cualquier otra ruta `api/*` (incluida
`api/cart`) devuelve `403 Forbidden`. Por eso la colección usa `api/order?call=product_add`
(que internamente delega en `api/cart.php::addProduct()`, ver `order.php:309-340`) en vez de
pegarle a `api/cart` directo.

Además, toda llamada a `api/order`/`api/subscription` exige:
1. **Cookie de sesión PHP** (`OCSESSID`) — se obtiene con un `GET common/home` previo.
2. **Firma HMAC-SHA1** sobre `route\ncall\nusername\nHTTP_HOST\nPHP_SELF\nstore_id\nlanguage\ncurrency\nmd5(post)\ntime\n`,
   firmada con la clave secreta de una API key dada de alta en
   *Admin > System > Users > API* (`opencart/catalog/controller/startup/api.php:84-98`).
3. **IP autorizada** para esa API key en `oc_api_ip` — la tabla estaba vacía en este entorno
   (bloqueaba toda IP, incluida `127.0.0.1`/`::1`); se insertaron ambas IPs de loopback para
   la API key "Default" ya existente (`api_id=1`) para poder ejecutar las pruebas.

El pre-request script a nivel de colección (ver `postman/opencart-integration.postman_collection.json`,
bloque `event.prerequest`) calcula esta firma automáticamente con `require('crypto-js')` del
sandbox de Postman, usando las variables `api_username` / `api_key` del environment. Solo
firma requests cuya URL ya tenga `route=api/order`; deja intactas otras rutas (ej.
`common/home`).

**Detalle importante de implementación**: el signature en base64 puede contener `+` y `/`.
`pm.request.url.query.upsert()` no percent-encodea esos caracteres al serializar la URL, y
PHP interpreta un `+` literal en la query string como espacio (`application/x-www-form-urlencoded`),
lo que invalida la firma. El script hace `encodeURIComponent(signatureB64)` antes de insertarla
en la query para evitarlo. También se resuelven las variables `{{...}}` del body con
`pm.variables.replaceIn()` antes de calcular el MD5 del post, porque `pm.request.body.urlencoded`
expone el texto literal sin interpolar dentro de un pre-request script.

## 3. Estructura de la colección Postman

`postman/opencart-integration.postman_collection.json`, carpetas:

1. **00 - Setup** — inicializa la cookie de sesión y valida acceso al carrito vía `api/order&call=cart`.
2. **01 - Caso Sintáctico** — `POST api/order&call=product_add` con campos faltantes / tipos erróneos.
3. **02 - Caso Semántico** — cantidades fuera de stock / negativas, y `confirm()` con checkout incompleto.
4. **03 - Caso Resiliencia** — igual que el flujo de `confirm()` pero apuntando al **proxy con
   latencia** (`proxy/latency-proxy.js`) en vez de directo a OpenCart.

Variables de entorno (`postman/opencart.postman_environment.json`):
- `base_url` = `http://localhost/QA-OpenCart-Testing/opencart`
- `proxy_url` = `http://localhost:4000` (proxy de latencia, solo Caso 3)
- `valid_product_id` = `28` (stock alto, para flujo feliz)
- `low_stock_product_id` = `30` (stock = 7, para forzar el Caso 2)
- `api_username` / `api_key` = credenciales de la API key "Default" (`oc_api`, `api_id=1`)
- `store_id`, `language`, `currency`, `host`, `php_self` = parámetros exigidos por la firma HMAC

## 4. Proxy de latencia (Caso 3 — Resiliencia)

`proxy/latency-proxy.js` es un proxy HTTP mínimo (Node/Express + `http-proxy-middleware`)
que reenvía todo a `base_url`, pero intercepta específicamente
`index.php?route=api/order&call=confirm` y retrasa la respuesta un tiempo configurable
(`LATENCY_MS`, default 20000 ms) antes de dejarla pasar. Así se simula que el "Subsistema B"
(persistencia del pedido) tarda anormalmente, sin tocar el código de OpenCart.

### Cómo correr

```bash
cd tests/integracion/proxy
npm install
LATENCY_MS=25000 node latency-proxy.js
```

El proxy escucha en `http://localhost:4000` y reenvía a `http://localhost/QA-OpenCart-Testing/opencart`.

## 5. Ejecución de la colección con Newman

```bash
npm install -g newman newman-reporter-htmlextra
newman run postman/opencart-integration.postman_collection.json \
  -e postman/opencart.postman_environment.json \
  --insecure \
  -r cli,htmlextra --reporter-htmlextra-export report.html
```

Para el Caso 3, levantar antes el proxy (`node proxy/latency-proxy.js`) en otra terminal;
la carpeta "03 - Caso Resiliencia" usa `{{proxy_url}}` en vez de `{{base_url}}`.

## 6. Reportes de incidente

Cada falla detectada se documenta en `incident-reports/` usando la plantilla
[`incident-report-template.md`](incident-reports/incident-report-template.md), con
Resultado Esperado vs. Resultado Real, evidencia y severidad.

Hallazgos ya documentados y verificados contra el entorno local (10/10 aserciones del suite
pasan porque cada hallazgo quedó reflejado como comportamiento esperado-y-documentado, no
como test roto):

- **INC-001** (Sintáctico) — `api/cart.php::addProduct()` castea `quantity` no numérica a
  `0` en vez de rechazarla.
- **INC-002** (Semántico) — la API acepta `quantity` negativa como una operación exitosa; no
  hay validación de signo antes de tocar el carrito.
- **INC-003** (Resiliencia) — hipótesis de diseño, no confirmada empíricamente: `confirm()`
  no verifica `connection_aborted()` entre `addOrder()` y `addHistory()`, por lo que un
  timeout del cliente durante la latencia simulada podría dejar un pedido creado sin que el
  cliente lo sepa. Requiere un checkout completo (customer + direcciones + método de pago)
  para confirmarse; no se pudo completar ese flujo en este ciclo de pruebas.
