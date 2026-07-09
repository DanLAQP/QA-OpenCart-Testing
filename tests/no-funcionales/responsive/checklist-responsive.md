# Checklist de Responsive

Evidencia visual de que OpenCart (`upload/`) se adapta correctamente a distintos tamaños de
pantalla. A diferencia del resto de categorías no funcionales, aquí la evidencia principal son
**capturas de pantalla**, revisadas contra un criterio visual simple: se ve bien y es usable en
cada viewport.

**Herramienta**: [Responsively App](https://responsively.app/) — navegador que renderiza la
misma URL en múltiples viewports simultáneamente, en paralelo (scroll sincronizado).
**Viewports usados**: iPhone 12 Pro (390×844), iPad (768×1024), MacBook Pro (1440×900) — cubren
móvil, tablet y desktop.
**Ambiente evaluado**: `http://localhost/QA-OpenCart-Testing/upload`
**Evidencia guardada en**: [`docs/evidencias/no_funcionales/`](../../../docs/evidencias/no_funcionales/)

## Criterios de aceptación (aplicar a cada captura)

- [x] El menú de navegación colapsa a un ícono/hamburguesa en móvil, y se expande en tablet/desktop.
- [x] No hay scroll horizontal ni contenido cortado/desbordado en ningún viewport.
- [x] El texto es legible sin necesidad de hacer zoom (tamaño de fuente adecuado en móvil).
- [x] Las imágenes/tarjetas de producto se reacomodan en grid según el ancho disponible (no se deforman).
- [x] Los formularios (login) mantienen los campos usables y alineados en cualquier ancho.

---

## Evidencia capturada

| # | Vista | Captura | Resultado |
|---|---|---|---|
| 1 | Home de la tienda (catálogo, navegación, productos destacados) | `prueba_resposive_con_responsively_cliente.png` | ✅ Se ve y funciona correctamente en los 3 viewports |
| 2 | Dashboard del panel admin (widgets, mapa, gráfico de ventas) | `prueba_resposive_con_responsively.png` | ✅ Se ve y funciona correctamente en los 3 viewports |
| 3 | Panel admin — Orders / Add Order (formulario) | `prueba_responsive_carrito.png` | ✅ Se ve y funciona correctamente en los 3 viewports |
| 4 | Login del panel admin | `prueba_responsive_login.png` | ✅ Se ve y funciona correctamente en los 3 viewports |

### 1. Home de la tienda

En móvil el menú colapsa a un ícono de hamburguesa y las tarjetas de producto pasan a una
columna; en tablet se ven 3 columnas de productos destacados; en desktop el menú de categorías
se muestra completo como barra horizontal. Sin overflow ni contenido cortado en ningún viewport.

### 2. Dashboard del panel admin

En móvil el sidebar de navegación colapsa a un menú hamburguesa; en tablet y desktop se expande
como barra lateral fija. Los widgets (Total Orders, Sales, Customers, People Online) y los
gráficos (mapa, ventas) se reacomodan en grid según el ancho disponible.

### 3. Panel admin — Orders / Add Order

El formulario de creación de pedido mantiene todos los campos (Invoice, Customer, Date Added,
Store, Language, Currency, tabla de productos, Payment Address/Method) legibles y usables en
los 3 viewports, sin que ningún campo se corte o se superponga.

### 4. Login del panel admin

El formulario de login se mantiene centrado y con buen espaciado en los 3 viewports; los campos
Username/Password y el botón Login son claramente usables incluso en el viewport más angosto
(iPhone 12 Pro, 390px).

---

## Hallazgos

Ninguno — las 4 vistas capturadas se ven y funcionan correctamente en los 3 viewports
evaluados (móvil, tablet, desktop).

## Conclusión

El comportamiento observado es consistente con el uso del framework **Bootstrap** de OpenCart 4
(grid responsive nativo, navbar colapsable). Tanto la tienda (home) como el panel administrativo
(dashboard, gestión de pedidos, login) se adaptan correctamente a los distintos tamaños de
pantalla evaluados, sin overflow horizontal ni elementos cortados.
