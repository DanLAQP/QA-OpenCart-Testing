import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

// Pruebas de rendimiento/carga sobre el modulo de Gestion de Inventario (RF-INV-001 al RF-INV-038).
// Cubre 3 flujos representativos: catalogo (lectura), agregar al carrito (escritura con
// validacion de stock), y listado admin filtrado por cantidad (consulta administrativa pesada).

const BASE_URL = __ENV.BASE_URL || 'http://localhost/QA-OpenCart-Testing/upload';
const ADMIN_URL = __ENV.ADMIN_URL || 'http://localhost/QA-OpenCart-Testing/upload/admin';
const PRODUCT_ID = __ENV.PRODUCT_ID || '28';
const ADMIN_USERNAME = __ENV.ADMIN_USERNAME || 'admin';
const ADMIN_PASSWORD = __ENV.ADMIN_PASSWORD || 'admin123';

const errorRate = new Rate('errores_negocio');
const catalogTrend = new Trend('duracion_catalogo', true);
const cartTrend = new Trend('duracion_carrito', true);
const adminListTrend = new Trend('duracion_listado_admin', true);

export const options = {
  scenarios: {
    catalogo_y_carrito: {
      executor: 'ramping-vus',
      exec: 'catalogoYCarrito',
      startVUs: 0,
      stages: [
        { duration: '30s', target: 10 },
        { duration: '1m', target: 10 },
        { duration: '30s', target: 30 },
        { duration: '1m', target: 30 },
        { duration: '30s', target: 0 },
      ],
    },
    listado_admin: {
      executor: 'constant-vus',
      exec: 'listadoAdmin',
      vus: 3,
      duration: '3m30s',
    },
  },
  thresholds: {
    http_req_duration: ['p(95)<800'],
    http_req_failed: ['rate<0.01'],
    duracion_catalogo: ['p(95)<500'],
    duracion_carrito: ['p(95)<600'],
    duracion_listado_admin: ['p(95)<1000'],
    errores_negocio: ['rate<0.05'],
  },
};

export function catalogoYCarrito() {
  group('Cliente: navegar catalogo', function () {
    const res = http.get(`${BASE_URL}/index.php?route=common/home`);
    catalogTrend.add(res.timings.duration);
    check(res, { 'home responde 200': (r) => r.status === 200 });
  });

  sleep(1);

  group('Cliente: ver detalle de producto', function () {
    const res = http.get(`${BASE_URL}/index.php?route=product/product&product_id=${PRODUCT_ID}`);
    catalogTrend.add(res.timings.duration);
    check(res, { 'detalle de producto responde 200': (r) => r.status === 200 });
  });

  sleep(1);

  group('Cliente: agregar al carrito', function () {
    const res = http.post(
      `${BASE_URL}/index.php?route=checkout/cart.add`,
      { product_id: PRODUCT_ID, quantity: '1' },
      { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
    );
    cartTrend.add(res.timings.duration);

    const ok = check(res, {
      'agregar al carrito responde 200': (r) => r.status === 200,
      'respuesta es JSON valido': (r) => {
        try {
          JSON.parse(r.body);
          return true;
        } catch (e) {
          return false;
        }
      },
    });

    errorRate.add(!ok);
  });

  sleep(2);
}

export function listadoAdmin() {
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
    adminListTrend.add(res.timings.duration);
    check(res, { 'listado admin responde 200': (r) => r.status === 200 });
  });

  sleep(2);
}
