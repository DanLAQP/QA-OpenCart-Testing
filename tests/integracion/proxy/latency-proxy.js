// Proxy de inyección de latencia para el Caso 3 (Resiliencia).
//
// Reenvía todo el tráfico hacia OpenCart (TARGET_URL) sin modificarlo, excepto las
// llamadas a index.php?route=api/order&call=confirm, donde retiene la respuesta
// LATENCY_MS antes de devolverla al cliente. Esto simula que el Subsistema B
// (persistencia del pedido) responde con latencia anormal, sin tocar el código fuente
// de OpenCart.

const express = require('express');
const { createProxyMiddleware, responseInterceptor } = require('http-proxy-middleware');

const PORT = process.env.PORT || 4000;
const TARGET_URL = process.env.TARGET_URL || 'http://localhost/QA-OpenCart-Testing/opencart';
const LATENCY_MS = parseInt(process.env.LATENCY_MS || '20000', 10);

const app = express();

function isConfirmCall(req) {
	return req.url.includes('route=api/order') && req.url.includes('call=confirm');
}

app.use(
	'/',
	createProxyMiddleware({
		target: TARGET_URL,
		changeOrigin: true,
		selfHandleResponse: true,
		cookieDomainRewrite: '',
		onProxyReq: (proxyReq, req) => {
			if (isConfirmCall(req)) {
				console.log(`[latency-proxy] Interceptando confirm() -> delay de ${LATENCY_MS}ms`);
			}
		},
		onProxyRes: responseInterceptor(async (responseBuffer, proxyRes, req) => {
			if (isConfirmCall(req)) {
				await new Promise((resolve) => setTimeout(resolve, LATENCY_MS));
				console.log(`[latency-proxy] Delay completado, liberando respuesta de confirm()`);
			}
			return responseBuffer;
		}),
	})
);

app.listen(PORT, () => {
	console.log(`[latency-proxy] Escuchando en http://localhost:${PORT}`);
	console.log(`[latency-proxy] Reenviando a ${TARGET_URL}`);
	console.log(`[latency-proxy] Latencia inyectada en api/order?call=confirm: ${LATENCY_MS}ms`);
});
