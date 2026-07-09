import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

// Pruebas de rendimiento/carga cubriendo los 6 modulos funcionales del proyecto:
// 1. Login y Registro        4. Checkout y Pago (solo lectura)
// 2. Catalogo y Busqueda     5. Gestion de Inventario (catalogo/carrito + listado admin)
// 3. Carrito de compras      6. Resenas
//
// Cada modulo corre como un escenario independiente de k6 para poder medir y ajustar
// umbrales por separado, y para poder deshabilitar uno sin afectar a los demas
// (ver seccion "scenarios" mas abajo).

const BASE_URL = __ENV.BASE_URL || 'http://localhost/QA-OpenCart-Testing/upload';
const ADMIN_URL = __ENV.ADMIN_URL || 'http://localhost/QA-OpenCart-Testing/upload/admin';
const PRODUCT_ID = __ENV.PRODUCT_ID || '28';
const SEARCH_TERM = __ENV.SEARCH_TERM || 'Camera';
const CATEGORY_ID = __ENV.CATEGORY_ID || '33';
const ADMIN_USERNAME = __ENV.ADMIN_USERNAME || 'admin';
const ADMIN_PASSWORD = __ENV.ADMIN_PASSWORD || 'admin123';

const errorRate = new Rate('errores_negocio');

const loginRegistroTrend = new Trend('duracion_login_registro', true);
const catalogoBusquedaTrend = new Trend('duracion_catalogo_busqueda', true);
const carritoTrend = new Trend('duracion_carrito', true);
const checkoutTrend = new Trend('duracion_checkout', true);
const inventarioTrend = new Trend('duracion_inventario', true);
const listadoAdminTrend = new Trend('duracion_listado_admin', true);
const resenasTrend = new Trend('duracion_resenas', true);

export const options = {
  scenarios: {
    // 1. Login y Registro
    login_y_registro: {
      executor: 'ramping-vus',
      exec: 'loginYRegistro',
      startVUs: 0,
      stages: [
        { duration: '20s', target: 5 },
        { duration: '40s', target: 5 },
        { duration: '20s', target: 0 },
      ],
    },
    // 2. Catalogo y Busqueda
    catalogo_y_busqueda: {
      executor: 'ramping-vus',
      exec: 'catalogoYBusqueda',
      startVUs: 0,
      stages: [
        { duration: '20s', target: 15 },
        { duration: '40s', target: 15 },
        { duration: '20s', target: 0 },
      ],
    },
    // 3. Carrito de compras (ademas de agregar, cubre ver/actualizar/eliminar)
    carrito_de_compras: {
      executor: 'ramping-vus',
      exec: 'carritoDeCompras',
      startVUs: 0,
      stages: [
        { duration: '20s', target: 10 },
        { duration: '40s', target: 10 },
        { duration: '20s', target: 0 },
      ],
    },
    // 4. Checkout y Pago (solo endpoints de lectura: totales del carrito)
    checkout_y_pago: {
      executor: 'ramping-vus',
      exec: 'checkoutYPago',
      startVUs: 0,
      stages: [
        { duration: '20s', target: 8 },
        { duration: '40s', target: 8 },
        { duration: '20s', target: 0 },
      ],
    },
    // 5. Gestion de Inventario (catalogo/carrito + listado admin filtrado)
    gestion_de_inventario: {
      executor: 'ramping-vus',
      exec: 'gestionDeInventario',
      startVUs: 0,
      stages: [
        { duration: '30s', target: 10 },
        { duration: '1m', target: 10 },
        { duration: '30s', target: 30 },
        { duration: '1m', target: 30 },
        { duration: '30s', target: 0 },
      ],
    },
    listado_admin_inventario: {
      executor: 'constant-vus',
      exec: 'listadoAdminInventario',
      vus: 3,
      duration: '3m30s',
    },
    // 6. Resenas
    resenas: {
      executor: 'ramping-vus',
      exec: 'resenas',
      startVUs: 0,
      stages: [
        { duration: '20s', target: 8 },
        { duration: '40s', target: 8 },
        { duration: '20s', target: 0 },
      ],
    },
  },
  thresholds: {
    http_req_duration: ['p(95)<800'],
    http_req_failed: ['rate<0.01'],
    errores_negocio: ['rate<0.05'],
    duracion_login_registro: ['p(95)<700'],
    duracion_catalogo_busqueda: ['p(95)<500'],
    duracion_carrito: ['p(95)<600'],
    duracion_checkout: ['p(95)<700'],
    duracion_inventario: ['p(95)<600'],
    duracion_listado_admin: ['p(95)<1000'],
    duracion_resenas: ['p(95)<500'],
  },
};

function isJson(body) {
  try {
    JSON.parse(body);
    return true;
  } catch (e) {
    return false;
  }
}

// ---------------------------------------------------------------------------
// 1. Login y Registro (RF del modulo Login_registro.md)
// ---------------------------------------------------------------------------
export function loginYRegistro() {
  group('Cliente: ver formulario de login', function () {
    const res = http.get(`${BASE_URL}/index.php?route=account/login`);
    loginRegistroTrend.add(res.timings.duration);
    check(res, { 'formulario de login responde 200': (r) => r.status === 200 });
  });

  sleep(1);

  group('Cliente: registrar cuenta nueva', function () {
    const formRes = http.get(`${BASE_URL}/index.php?route=account/register`);
    loginRegistroTrend.add(formRes.timings.duration);

    const match = formRes.body.match(/register_token=([a-f0-9]+)/);
    if (!match) {
      errorRate.add(true);
      return;
    }
    const registerToken = match[1];

    const uniqueId = `${__VU}_${__ITER}_${Date.now()}`;
    const res = http.post(
      `${BASE_URL}/index.php?route=account/register.register&register_token=${registerToken}`,
      {
        customer_group_id: '1',
        firstname: 'Carga',
        lastname: `Prueba${uniqueId}`,
        email: `carga.prueba.${uniqueId}@example.test`,
        telephone: '5555555555',
        password: 'Passw0rd!',
        confirm: 'Passw0rd!',
        agree: '1',
      },
      { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
    );
    loginRegistroTrend.add(res.timings.duration);

    const ok = check(res, {
      'registro responde 200': (r) => r.status === 200,
      'respuesta de registro es JSON valido': (r) => isJson(r.body),
    });
    errorRate.add(!ok);
  });

  sleep(2);
}

// ---------------------------------------------------------------------------
// 2. Catalogo y Busqueda (RF del modulo Catalogo_Busquedas.md)
// ---------------------------------------------------------------------------
export function catalogoYBusqueda() {
  group('Cliente: buscar productos', function () {
    const res = http.get(`${BASE_URL}/index.php?route=product/search&search=${SEARCH_TERM}`);
    catalogoBusquedaTrend.add(res.timings.duration);
    check(res, { 'busqueda responde 200': (r) => r.status === 200 });
  });

  sleep(1);

  group('Cliente: navegar categoria', function () {
    const res = http.get(`${BASE_URL}/index.php?route=product/category&path=${CATEGORY_ID}`);
    catalogoBusquedaTrend.add(res.timings.duration);
    check(res, { 'categoria responde 200': (r) => r.status === 200 });
  });

  sleep(1);

  group('Cliente: ver detalle de producto', function () {
    const res = http.get(`${BASE_URL}/index.php?route=product/product&product_id=${PRODUCT_ID}`);
    catalogoBusquedaTrend.add(res.timings.duration);
    check(res, { 'detalle de producto responde 200': (r) => r.status === 200 });
  });

  sleep(2);
}

// ---------------------------------------------------------------------------
// 3. Carrito de compras (RF del modulo Carrito_compras.md)
// ---------------------------------------------------------------------------
export function carritoDeCompras() {
  let cartKey = '';

  group('Cliente: agregar producto al carrito', function () {
    const res = http.post(
      `${BASE_URL}/index.php?route=checkout/cart.add`,
      { product_id: PRODUCT_ID, quantity: '1' },
      { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
    );
    carritoTrend.add(res.timings.duration);

    const ok = check(res, {
      'agregar al carrito responde 200': (r) => r.status === 200,
      'respuesta de agregar es JSON valido': (r) => isJson(r.body),
    });
    errorRate.add(!ok);
  });

  sleep(1);

  group('Cliente: ver carrito', function () {
    const res = http.get(`${BASE_URL}/index.php?route=checkout/cart.list`);
    carritoTrend.add(res.timings.duration);

    const ok = check(res, {
      'ver carrito responde 200': (r) => r.status === 200,
      'ver carrito devuelve contenido': (r) => r.body.length > 0,
    });
    errorRate.add(!ok);

    const match = res.body.match(/cart\.remove[^"']*key=(\d+)/);
    if (match) {
      cartKey = match[1];
    }
  });

  sleep(1);

  if (cartKey) {
    group('Cliente: actualizar cantidad en el carrito', function () {
      const res = http.post(
        `${BASE_URL}/index.php?route=checkout/cart.edit`,
        { key: String(cartKey), quantity: '2' },
        { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
      );
      carritoTrend.add(res.timings.duration);
      check(res, { 'actualizar cantidad responde 200': (r) => r.status === 200 });
    });

    sleep(1);

    group('Cliente: eliminar producto del carrito', function () {
      const res = http.get(`${BASE_URL}/index.php?route=checkout/cart.remove&key=${cartKey}`);
      carritoTrend.add(res.timings.duration);
      check(res, { 'eliminar del carrito responde 200': (r) => r.status === 200 });
    });
  }

  sleep(2);
}

// ---------------------------------------------------------------------------
// 4. Checkout y Pago (RF del modulo Checkout_pago.md) — solo endpoints de lectura,
//    para no generar ordenes reales ni requerir datos completos de pago.
// ---------------------------------------------------------------------------
export function checkoutYPago() {
  group('Cliente: agregar producto antes del checkout', function () {
    const res = http.post(
      `${BASE_URL}/index.php?route=checkout/cart.add`,
      { product_id: PRODUCT_ID, quantity: '1' },
      { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
    );
    checkoutTrend.add(res.timings.duration);
    check(res, { 'agregar antes de checkout responde 200': (r) => r.status === 200 });
  });

  sleep(1);

  group('Cliente: obtener totales del carrito (checkout)', function () {
    const res = http.get(`${BASE_URL}/index.php?route=checkout/cart.list`);
    checkoutTrend.add(res.timings.duration);

    const ok = check(res, {
      'totales de checkout responde 200': (r) => r.status === 200,
      'totales de checkout devuelve contenido': (r) => r.body.length > 0,
    });
    errorRate.add(!ok);
  });

  sleep(1);

  group('Cliente: ver pagina de checkout', function () {
    const res = http.get(`${BASE_URL}/index.php?route=checkout/checkout`);
    checkoutTrend.add(res.timings.duration);
    // Puede redirigir a checkout/cart si no hay stock/minimo cumplido; ambos son 200 validos.
    check(res, { 'pagina de checkout responde 200': (r) => r.status === 200 });
  });

  sleep(2);
}

// ---------------------------------------------------------------------------
// 5. Gestion de Inventario (RF-INV-001 al RF-INV-038) — igual que inventario-carga.js
// ---------------------------------------------------------------------------
export function gestionDeInventario() {
  group('Cliente: navegar catalogo (inventario)', function () {
    const res = http.get(`${BASE_URL}/index.php?route=common/home`);
    inventarioTrend.add(res.timings.duration);
    check(res, { 'home responde 200': (r) => r.status === 200 });
  });

  sleep(1);

  group('Cliente: ver detalle de producto (inventario)', function () {
    const res = http.get(`${BASE_URL}/index.php?route=product/product&product_id=${PRODUCT_ID}`);
    inventarioTrend.add(res.timings.duration);
    check(res, { 'detalle de producto responde 200': (r) => r.status === 200 });
  });

  sleep(1);

  group('Cliente: agregar al carrito (validacion de stock)', function () {
    const res = http.post(
      `${BASE_URL}/index.php?route=checkout/cart.add`,
      { product_id: PRODUCT_ID, quantity: '1' },
      { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
    );
    inventarioTrend.add(res.timings.duration);

    const ok = check(res, {
      'agregar al carrito responde 200': (r) => r.status === 200,
      'respuesta es JSON valido': (r) => isJson(r.body),
    });
    errorRate.add(!ok);
  });

  sleep(2);
}

export function listadoAdminInventario() {
  let userToken = '';

  group('Admin: login', function () {
    const loginPage = http.get(`${ADMIN_URL}/index.php?route=common/login`);
    const match = loginPage.body.match(/login_token=([a-f0-9]+)/);
    if (!match) {
      errorRate.add(true);
      return;
    }
    const loginToken = match[1];

    const loginRes = http.post(
      `${ADMIN_URL}/index.php?route=common/login.login&login_token=${loginToken}`,
      { username: ADMIN_USERNAME, password: ADMIN_PASSWORD },
      { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
    );

    const tokenMatch = loginRes.body.match(/user_token=([a-f0-9]+)/);
    if (tokenMatch) {
      userToken = tokenMatch[1];
    } else {
      errorRate.add(true);
    }
  });

  if (!userToken) {
    sleep(1);
    return;
  }

  group('Admin: listado de productos filtrado por cantidad', function () {
    const res = http.get(
      `${ADMIN_URL}/index.php?route=catalog/product.list&user_token=${userToken}&filter_quantity_from=0&filter_quantity_to=1000`
    );
    listadoAdminTrend.add(res.timings.duration);
    check(res, { 'listado admin responde 200': (r) => r.status === 200 });
  });

  sleep(2);
}

// ---------------------------------------------------------------------------
// 6. Resenas (RF del modulo Reseñas.md)
// ---------------------------------------------------------------------------
export function resenas() {
  group('Cliente: ver listado de resenas de un producto', function () {
    const res = http.get(`${BASE_URL}/index.php?route=product/review.list&product_id=${PRODUCT_ID}&page=1`);
    resenasTrend.add(res.timings.duration);
    check(res, { 'listado de resenas responde 200': (r) => r.status === 200 });
  });

  sleep(1);

  group('Cliente: enviar una resena nueva', function () {
    // El review_token solo se genera cuando product/review se carga como sub-controlador
    // embebido dentro de product/product (ver catalog/controller/product/product.php),
    // no llamando product/review como ruta HTTP independiente.
    const formRes = http.get(`${BASE_URL}/index.php?route=product/product&product_id=${PRODUCT_ID}`);
    resenasTrend.add(formRes.timings.duration);

    const match = formRes.body.match(/review_token=([a-f0-9]+)/);
    if (!match) {
      errorRate.add(true);
      return;
    }
    const reviewToken = match[1];

    const res = http.post(
      `${BASE_URL}/index.php?route=product/review.write&product_id=${PRODUCT_ID}&review_token=${reviewToken}`,
      {
        author: 'Cliente de Carga',
        text: 'Resena generada automaticamente durante una prueba de rendimiento/carga con k6. '.repeat(1),
        rating: '5',
      },
      { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
    );
    resenasTrend.add(res.timings.duration);

    const ok = check(res, {
      'enviar resena responde 200': (r) => r.status === 200,
      'respuesta de resena es JSON valido': (r) => isJson(r.body),
    });
    errorRate.add(!ok);
  });

  sleep(2);
}
