import http from 'k6/http';
import { check, sleep } from 'k6';

// Smoke test: 1 usuario virtual, unas pocas iteraciones. Verifica que el script y el ambiente
// funcionan antes de correr la prueba de carga completa (inventario-carga.js).

const BASE_URL = __ENV.BASE_URL || 'http://localhost/QA-OpenCart-Testing/opencart';
const PRODUCT_ID = __ENV.PRODUCT_ID || '28';

export const options = {
  vus: 1,
  iterations: 3,
};

export default function () {
  const home = http.get(`${BASE_URL}/index.php?route=common/home`);
  check(home, { 'home responde 200': (r) => r.status === 200 });

  const product = http.get(`${BASE_URL}/index.php?route=product/product&product_id=${PRODUCT_ID}`);
  check(product, { 'detalle de producto responde 200': (r) => r.status === 200 });

  const cart = http.post(
    `${BASE_URL}/index.php?route=checkout/cart.add`,
    { product_id: PRODUCT_ID, quantity: '1' },
    { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
  );
  check(cart, { 'agregar al carrito responde 200': (r) => r.status === 200 });

  sleep(1);
}
