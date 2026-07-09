# Pruebas No Funcionales — Rendimiento / Carga (6 módulos)

Pruebas de rendimiento y carga sobre OpenCart local usando **k6**, cubriendo los 6 módulos
funcionales del proyecto:

1. [Login y Registro](../../../docs/Requisitos-funcionales/Login_registro.md)
2. [Catálogo y Búsquedas](../../../docs/Requisitos-funcionales/Catalogo_Busquedas..md)
3. [Carrito de compras](../../../docs/Requisitos-funcionales/Carrito_compras.md)
4. [Checkout y Pago](../../../docs/Requisitos-funcionales/Checkout_pago.md) (solo endpoints de lectura)
5. [Gestión de Inventario](../../../docs/Requisitos-funcionales/Gestion_inventario.md) (RF-INV-001 al RF-INV-038)
6. [Reseñas](../../../docs/Requisitos-funcionales/Reseñas.md)

A diferencia de las pruebas funcionales (unitarias, integración, sistema, aceptación), que
verifican **qué tan correcto** es el comportamiento, estas pruebas verifican **qué tan rápido y
estable** se mantiene ese comportamiento bajo carga concurrente.

## 0. Ambiente: `upload/`, no `opencart/`

**Importante**: este proyecto tiene dos copias del código de OpenCart en el repo:
`opencart/` y `upload/`. El workflow de CI (`opencart-e2e.yml`) instala y sirve el sitio desde
**`upload/`**, y es la única de las dos que tiene las plantillas `.twig` correctas — `opencart/`
tiene plantillas `.html` desincronizadas con el motor Twig del framework, por lo que **nunca
renderiza HTML real** (devuelve un error de Twig con código HTTP 200, lo cual puede pasar
desapercibido si solo se valida el status code). Todos los scripts de esta carpeta apuntan a
`upload/` por defecto.

Para instalar `upload/` en un XAMPP nuevo (igual que hace CI):

```bash
# 1. Crear una base de datos dedicada
mysql -u root -e "CREATE DATABASE opencart_upload CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

# 2. Preparar config y storage
cd upload
cp config-dist.php config.php
cp admin/config-dist.php admin/config.php
mkdir -p system/storage/cache system/storage/logs system/storage/session system/storage/upload system/storage/download system/storage/modification

# 3. Instalar dependencias de Composer (Twig, etc.) desde la raiz del repo
cd ..
composer install --no-interaction --prefer-dist

# 4. Instalar OpenCart via CLI
cd upload
php install/cli_install.php install \
  --username admin --password admin123 --email admin@example.com \
  --http_server "http://localhost/QA-OpenCart-Testing/upload/" \
  --language en-gb --db_driver mysqli --db_hostname 127.0.0.1 \
  --db_username root --db_password "" --db_database opencart_upload \
  --db_port 3306 --db_prefix oc_
```

Si `upload/config.php` ya existe pero quedó vacío (0 bytes) tras alguna limpieza de git, vuelve
a correr el paso 4 del instalador — reescribe `config.php`/`admin/config.php` con el contenido
real sin perder los datos ya existentes en la base de datos.

Verificar con `curl http://localhost/QA-OpenCart-Testing/upload/index.php?route=common/home`
— debe devolver HTML completo, no un mensaje de error de Twig.

## 1. Requisitos previos

- **XAMPP corriendo** (Apache + MySQL) con OpenCart instalado en `upload/` (ver sección 0).
- **k6** instalado. En este entorno se instaló con:
  ```powershell
  winget install k6 --source winget
  ```
  Verificar con `k6 version`. Si el comando no se reconoce, cierra y reabre la terminal (el
  instalador agrega `k6` al PATH del sistema, que no se recarga en sesiones ya abiertas).

## 2. Escenarios cubiertos

### 2.1 `seis-modulos-carga.js` — los 6 módulos (recomendado)

Corre 7 escenarios en paralelo, uno (o dos) por módulo:

| Escenario | Módulo | Qué hace |
|---|---|---|
| `login_y_registro` | 1. Login y Registro | Ve el formulario de login y registra una cuenta nueva con datos únicos por iteración. |
| `catalogo_y_busqueda` | 2. Catálogo y Búsqueda | Busca por término, navega una categoría, ve el detalle de un producto. |
| `carrito_de_compras` | 3. Carrito de compras | Agrega al carrito, lo consulta, actualiza cantidad y elimina el ítem. |
| `checkout_y_pago` | 4. Checkout y Pago | Agrega un producto, consulta los totales del carrito y abre la página de checkout (sin completar el pago). |
| `gestion_de_inventario` + `listado_admin_inventario` | 5. Gestión de Inventario | Navegación + carrito con validación de stock (cliente) y listado de productos filtrado por cantidad (admin). |
| `resenas` | 6. Reseñas | Ve el listado de reseñas de un producto y envía una reseña nueva. |

Duración total: ~3 minutos 30 segundos (todos los escenarios corren en paralelo).

> **Nota de rutas**: algunas rutas de esta versión de OpenCart 4 difieren de otras variantes
> documentadas en línea — en particular el registro se envía a `account/register.register`
> (no `account/register.save`), el carrito se consulta con `checkout/cart.list` (HTML, no
> existe `checkout/cart.json` en esta versión), y el `review_token` para enviar una reseña solo
> se genera cuando `product/review` se carga embebido dentro de `product/product` (no llamando
> `product/review` como ruta independiente). El script ya reflejan estos hallazgos.

### 2.2 `inventario-carga.js` — solo Gestión de Inventario (script original, más granular)

Mantiene únicamente los escenarios de catálogo/carrito con validación de stock y el listado
admin filtrado por cantidad, útil si solo interesa medir ese módulo con más detalle o con
umbrales distintos.

### Umbrales de aceptación (`thresholds`)

| Métrica | Umbral | Justificación |
|---|---|---|
| `http_req_duration` (p95, global) | < 800 ms | Límite general de respuesta aceptable bajo carga. |
| `http_req_failed` (rate) | < 1% | Tolerancia mínima de errores HTTP (5xx, timeouts). |
| `duracion_login_registro` (p95) | < 700 ms | Incluye validaciones de formulario más costosas. |
| `duracion_catalogo_busqueda` (p95) | < 500 ms | Navegación/búsqueda debe sentirse instantánea. |
| `duracion_carrito` (p95) | < 600 ms | Agregar/editar/eliminar incluye validación de stock. |
| `duracion_checkout` (p95) | < 700 ms | Cálculo de totales y renderizado de checkout. |
| `duracion_inventario` (p95) | < 600 ms | Mismo criterio que `duracion_carrito`. |
| `duracion_listado_admin` (p95) | < 1000 ms | Consulta administrativa, tolerancia mayor. |
| `duracion_resenas` (p95) | < 500 ms | Lectura/escritura simple de reseñas. |
| `errores_negocio` (rate) | < 5% | Respuestas que no son JSON válido, tokens faltantes, o login admin fallido. |

Ajusta estos umbrales según lo que tu rúbrica o plan de pruebas defina como aceptable.

## 3. Ejecución paso a paso

### 3.1 Smoke test (validar que todo funciona antes de la carga completa)

```powershell
cd tests/no-funcionales/k6
k6 run seis-modulos-smoke.js
```

Corre 1 iteración de cada uno de los 6 módulos (~5 segundos). Debe mostrar
`checks_succeeded: 100.00%` y `errores_negocio: 0.00%`. Si falla, revisa primero que `upload/`
esté instalado y sirviendo HTML real (sección 0), y que `PRODUCT_ID` (por defecto `28`) exista.

### 3.2 Prueba de carga completa (~3.5 minutos)

```powershell
cd tests/no-funcionales/k6
k6 run seis-modulos-carga.js
```

Variables configurables vía `-e`:

```powershell
k6 run seis-modulos-carga.js `
  -e BASE_URL=http://localhost/QA-OpenCart-Testing/upload `
  -e ADMIN_URL=http://localhost/QA-OpenCart-Testing/upload/admin `
  -e PRODUCT_ID=28 `
  -e SEARCH_TERM=Camera `
  -e CATEGORY_ID=33 `
  -e ADMIN_USERNAME=admin `
  -e ADMIN_PASSWORD=admin123
```

> Esta corrida crea cuentas de cliente y reseñas reales en la base de datos (una por
> iteración de `login_y_registro` / `resenas`). Si vas a repetir la corrida muchas veces,
> considera limpiar `oc_customer` y `oc_review` periódicamente o usar una BD dedicada.

### 3.3 Guardar el resultado como reporte

En PowerShell (con fecha dinámica):
```powershell
k6 run seis-modulos-carga.js --summary-export="../reports/resultado-$(Get-Date -Format 'yyyyMMdd-HHmmss').json"
```

En `cmd.exe`, o sin necesidad de fecha en el nombre:
```
k6 run seis-modulos-carga.js --summary-export=../reports/resultado.json
```

## 4. Cómo leer el resultado

Al final de la corrida, k6 imprime una tabla `TOTAL RESULTS`. Presta atención a:

- **`checks_succeeded`**: porcentaje de aserciones (`check()`) que pasaron.
- **`http_req_duration`**: latencia HTTP general, con percentiles p90/p95.
- **`http_req_failed`**: porcentaje de requests con error de red o código ≥400.
- Si alguna métrica con `thresholds` definido no cumple el umbral, k6 termina con **código de
  salida distinto de 0** (99) y marca esa métrica con una `✗` — útil para integrarlo en CI.
- En el JSON exportado con `--summary-export`, cada métrica en `metrics.<nombre>.thresholds`
  tiene un booleano por umbral: `false` significa que el umbral **no** se incumplió (pasó),
  `true` significa que sí se incumplió.

## 5. Reportes de incidente

Cualquier degradación de rendimiento relevante (p. ej. un umbral incumplido de forma
consistente, o un cuello de botella identificado) se documenta en `../incident-reports/` usando
[`incident-report-template.md`](../incident-reports/incident-report-template.md).
