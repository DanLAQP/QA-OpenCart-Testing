#!/usr/bin/env node
/**
 * Verificacion basica de seguridad sobre OpenCart (upload/), cubriendo un subconjunto
 * automatizable del checklist OWASP en ../checklist-owasp.md.
 *
 * No requiere dependencias externas (usa el modulo http nativo de Node).
 *
 * Uso:
 *   node verificacion-seguridad.js
 *   BASE_URL=http://localhost/QA-OpenCart-Testing/upload node verificacion-seguridad.js
 */

const http = require('http');
const { URL } = require('url');

const BASE_URL = process.env.BASE_URL || 'http://localhost/QA-OpenCart-Testing/upload';
const ADMIN_URL = process.env.ADMIN_URL || `${BASE_URL}/admin`;

let passCount = 0;
let failCount = 0;
let warnCount = 0;

function request(method, urlStr, { body, headers } = {}) {
  return new Promise((resolve, reject) => {
    const url = new URL(urlStr);
    const payload = body ? new URLSearchParams(body).toString() : null;

    const options = {
      method,
      hostname: url.hostname,
      port: url.port || 80,
      path: url.pathname + url.search,
      headers: {
        ...(payload ? { 'Content-Type': 'application/x-www-form-urlencoded', 'Content-Length': Buffer.byteLength(payload) } : {}),
        ...headers,
      },
    };

    const req = http.request(options, (res) => {
      let data = '';
      res.on('data', (chunk) => (data += chunk));
      res.on('end', () => resolve({ status: res.statusCode, headers: res.headers, body: data }));
    });

    req.on('error', reject);
    if (payload) req.write(payload);
    req.end();
  });
}

function report(id, description, ok, detail, severity = 'info') {
  const icon = ok === true ? '\x1b[32mPASS\x1b[0m' : ok === 'warn' ? '\x1b[33mWARN\x1b[0m' : '\x1b[31mFAIL\x1b[0m';
  if (ok === true) passCount++;
  else if (ok === 'warn') warnCount++;
  else failCount++;
  console.log(`[${icon}] ${id} — ${description}`);
  if (detail) console.log(`       ${detail}`);
}

async function checkSecurityHeaders() {
  const res = await request('GET', `${BASE_URL}/index.php?route=common/home`);
  const h = res.headers;

  const expected = ['x-frame-options', 'x-content-type-options', 'content-security-policy', 'strict-transport-security'];
  const missing = expected.filter((name) => !h[name]);

  report(
    'A05-02',
    'Headers de seguridad HTTP presentes (X-Frame-Options, X-Content-Type-Options, CSP, HSTS)',
    missing.length === 0 ? true : 'warn',
    missing.length ? `Ausentes: ${missing.join(', ')}` : 'Todos presentes'
  );

  const serverHeader = h['server'] || '';
  const exposesVersion = /\d+\.\d+/.test(serverHeader);
  report(
    'A05-01',
    'Header Server no expone version detallada del stack',
    exposesVersion ? 'warn' : true,
    `Server: ${serverHeader || '(ausente)'}`
  );
}

async function checkCookieFlags() {
  const res = await request('GET', `${BASE_URL}/index.php?route=common/home`);
  const setCookie = res.headers['set-cookie'] || [];
  const sessionCookie = setCookie.find((c) => c.startsWith('OCSESSID'));

  const hasHttpOnly = sessionCookie && /HttpOnly/i.test(sessionCookie);
  const hasSameSite = sessionCookie && /SameSite=(Strict|Lax)/i.test(sessionCookie);

  report('A02-02', 'Cookie de sesion (OCSESSID) tiene flag HttpOnly', !!hasHttpOnly, sessionCookie || '(sin cookie de sesion)');
  report('A02-02b', 'Cookie de sesion (OCSESSID) tiene flag SameSite', !!hasSameSite, sessionCookie || '(sin cookie de sesion)');
}

async function checkDirectoryListing() {
  const paths = ['system/storage/', 'system/', '.git/config', 'admin/'];
  for (const p of paths) {
    const res = await request('GET', `${BASE_URL}/${p}`);
    const looksLikeListing = res.status === 200 && /Index of \//.test(res.body);
    report(
      'A05-03',
      `Directory listing deshabilitado en /${p}`,
      looksLikeListing ? false : true,
      `HTTP ${res.status}${looksLikeListing ? ' — listado de directorio expuesto' : ''}`
    );
  }
}

async function checkSqlInjection() {
  const payloads = ["28' OR '1'='1", "28' UNION SELECT 1,2,3-- -", "28'; DROP TABLE oc_product;--"];
  for (const payload of payloads) {
    const res = await request('GET', `${BASE_URL}/index.php?route=product/product&product_id=${encodeURIComponent(payload)}`);
    const exposesSqlError = /SQL syntax|mysqli_sql_exception|Unknown column|You have an error in your SQL/i.test(res.body);
    report(
      'A03-01',
      `SQL Injection no expone error de BD (payload: ${payload.slice(0, 25)}...)`,
      exposesSqlError ? false : true,
      `HTTP ${res.status}${exposesSqlError ? ' — error SQL visible en respuesta' : ''}`
    );
  }
}

async function checkReflectedXss() {
  const payload = '<script>alert(1)</script>';
  const res = await request('GET', `${BASE_URL}/index.php?route=product/search&search=${encodeURIComponent(payload)}`);
  const reflectedUnescaped = res.body.includes(payload);
  report('A03-02', 'XSS reflejado en parametro search esta escapado', reflectedUnescaped ? false : true, reflectedUnescaped ? 'Payload reflejado sin escapar' : 'Payload no encontrado sin escapar');
}

async function checkAdminAuthRequired() {
  const res = await request('GET', `${ADMIN_URL}/index.php?route=catalog/product.list`);
  const looksLikeLoginPage = /route=common\/login|<title>Administration<\/title>/i.test(res.body);
  const leaksProductData = /filter_quantity|product_id=\d+.*edit/i.test(res.body) && !looksLikeLoginPage;
  report(
    'A01-01',
    'catalog/product.list sin user_token no expone datos de productos',
    leaksProductData ? false : true,
    `HTTP ${res.status}${looksLikeLoginPage ? ' — redirige a login' : ''}`
  );
}

async function checkBruteForceProtection() {
  const results = [];
  for (let i = 1; i <= 8; i++) {
    const res = await request('POST', `${ADMIN_URL}/index.php?route=common/login.login&login_token=nonexistent`, {
      body: { username: 'admin', password: `wrong-${i}` },
    });
    results.push(res.status);
  }
  const allSameStatus = results.every((s) => s === results[0]);
  report(
    'A04-01',
    'Login admin aplica algun tipo de bloqueo/retraso tras intentos fallidos repetidos',
    'warn',
    `8 intentos consecutivos devolvieron status: [${results.join(', ')}]. ` +
      (allSameStatus ? 'Ningun cambio de comportamiento detectado (revisar manualmente si hay bloqueo por IP a nivel de infraestructura).' : 'Comportamiento vario entre intentos, revisar manualmente.')
  );
}

async function checkErrorHandling() {
  const paths = [
    'index.php?route=product/product&product_id=abc',
    'index.php?route=product/product&product_id=-1',
    'index.php?route=nonexistent/route',
  ];
  for (const p of paths) {
    const res = await request('GET', `${BASE_URL}/${p}`);
    const exposesStackTrace = /Fatal error|Stack trace|#0 \{main\}|on line \d+/i.test(res.body);
    report('A05-05', `Ruta '${p}' no expone stack trace de PHP`, exposesStackTrace ? false : true, `HTTP ${res.status}`);
  }
}

async function main() {
  console.log(`Verificacion de seguridad basica — objetivo: ${BASE_URL}\n`);

  await checkSecurityHeaders();
  await checkCookieFlags();
  await checkDirectoryListing();
  await checkSqlInjection();
  await checkReflectedXss();
  await checkAdminAuthRequired();
  await checkErrorHandling();
  await checkBruteForceProtection();

  console.log(`\nResumen: ${passCount} pass, ${warnCount} warn, ${failCount} fail`);
  process.exit(failCount > 0 ? 1 : 0);
}

main().catch((err) => {
  console.error('Error ejecutando la verificacion:', err.message);
  process.exit(2);
});
