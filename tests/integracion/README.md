# Índice de Pruebas de Integración — OpenCart

Este árbol organiza las suites de integración del proyecto por dominio funcional y las relaciona con una estrategia de cobertura en **5 capas**.

## Objetivo

Las pruebas de integración verifican que los componentes de OpenCart funcionen correctamente cuando interactúan entre sí, con la base de datos MySQL y con los servicios preparados en el pipeline.

El propósito de esta carpeta es:

- comprobar la comunicación entre módulos;
- validar reglas de negocio compartidas;
- verificar la persistencia y consistencia de los datos;
- probar flujos que atraviesan más de un módulo;
- ejecutar las suites en un entorno reproducible mediante GitHub Actions;
- conservar trazabilidad entre las pruebas ejecutadas y sus resultados.

## Las 5 coberturas que debe cumplir esta base

### 1. Cobertura estructural
Responde a si existen carpetas y suites para los módulos importantes del sistema.

Esta estructura cubre esa capa organizando las pruebas por dominio:

- `carrito-checkout`
- `clientes-autenticacion`
- `catalogo-busqueda`
- `gestion-inventario`
- `sistema-resenas`
- `pagos`
- `pedidos`
- `envios`
- `totales-descuentos`

### 2. Cobertura funcional
Responde a si cada suite valida reglas reales del módulo: flujo feliz, errores, límites, reglas de negocio y persistencia.

Cada carpeta debe contener pruebas que cubran al menos:

- flujo principal
- validaciones críticas
- límites relevantes
- errores semánticos o sintácticos
- consistencia de datos persistidos

### 3. Cobertura transversal
Responde a si se validan flujos que cruzan varios módulos.

En esta base, la cobertura transversal aparece sobre todo en suites como:

- `carrito-checkout/CarritoCheckoutIntegrationTest.php`
- `gestion-inventario/CheckoutInventoryIntegrationTest.php`
- futuras suites de `pagos`, `pedidos`, `envios` y `totales-descuentos`

Estas pruebas deben verificar cadenas funcionales como:

- carrito → checkout → envío → pago → pedido
- cambio de dirección → recálculo de envío → recálculo de total
- confirmación de pedido → historial → consistencia de tablas

### 4. Cobertura real del código
Responde a si las pruebas trabajan con tablas, reglas y persistencia reales del proyecto.

Esta base está diseñada para usar:

- MySQL real
- variables de entorno reales del pipeline
- tablas como `oc_product`, `oc_order`, `oc_order_product`, `oc_order_total`, `oc_order_history`, `oc_customer`, `oc_review`, entre otras

### 5. Ejecución real en CI/CD
Responde a si las suites realmente se ejecutan dentro del pipeline.

Esta capa se cubre con:

- un workflow de GitHub Actions
- base de datos MySQL real
- instalación previa de OpenCart
- ejecución de `vendor/bin/phpunit tests/integracion`
- logs y artifacts cuando corresponda

---

## Estructura actual por módulo

| Módulo | Estado | Propósito |
|---|---|---|
| Carrito y checkout | Base creada | Flujo de carrito, validaciones y orden |
| Clientes y autenticación | Base creada | Registro, login, bloqueo y sesión |
| Catálogo y búsqueda | Base creada | Navegación, búsqueda y detalle |
| Gestión de inventario | Base creada | Stock, disponibilidad y validaciones |
| Sistema de reseñas | Base creada | Persistencia y publicación de reseñas |
| Pagos | Base inicial | Método, confirmación y reintentos |
| Pedidos | Base inicial | Creación, historial y consistencia |
| Envíos | Base inicial | Dirección, método, cotización y recálculo |
| Totales y descuentos | Base inicial | Subtotal, impuestos, cupones y vouchers |

---

## Estructura de archivos

```text
tests/integracion/
├── README.md
├── carrito-checkout/
├── clientes-autenticacion/
├── catalogo-busqueda/
├── gestion-inventario/
├── sistema-resenas/
├── pagos/
├── pedidos/
├── envios/
└── totales-descuentos/
```

---

## Orden sugerido de ejecución en GitHub Actions

1. Preparar dependencias y MySQL.
2. Instalar OpenCart en entorno de prueba.
3. Ejecutar suites de integración prioritarias.
4. Ejecutar smoke tests si existen.
5. Publicar logs y resultados.

---

## Variables de entorno compartidas

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `DB_PREFIX`

---

## Ejecución sugerida

```bash
vendor/bin/phpunit tests/integracion
```

Para ejecutar solamente un dominio:

```bash
vendor/bin/phpunit tests/integracion/carrito-checkout
vendor/bin/phpunit tests/integracion/gestion-inventario
```

Antes de ejecutar las pruebas, MySQL debe encontrarse disponible y las variables de entorno deben apuntar a una base de datos exclusiva para pruebas.

---

## Resultado de la ejecución en GitHub Actions

**Fecha:** 19/07/2026  
**Workflow:** [Ejecución 29708563940](https://github.com/DanLAQP/QA-OpenCart-Testing/actions/runs/29708563940)  
**Job:** [opencart — 88249347162](https://github.com/DanLAQP/QA-OpenCart-Testing/actions/runs/29708563940/job/88249347162)  
**Resultado de las pruebas de integración:** ✅ Aprobado  
**Estado final del workflow:** ✅ Completado correctamente  
**Commit evaluado:** `cdf03cb2c989269b97f0be211de61ce8cc00fe8d`  
**Entorno:** Ubuntu 24.04, PHP 8.2.32, PHPUnit 10.5.64 y MySQL 8.0

### Resumen de resultados verificados

| Etapa | Resultado | Observación |
|---|---|---|
| Inicialización de contenedores | ✅ Aprobado | Los servicios requeridos iniciaron correctamente |
| Configuración de PHP | ✅ Aprobado | El entorno de PHP quedó disponible |
| Instalación de dependencias | ✅ Aprobado | Las dependencias se instalaron sin errores |
| Disponibilidad de MySQL | ✅ Aprobado | El pipeline pudo conectarse al servicio MySQL |
| Preparación de archivos y almacenamiento | ✅ Aprobado | Se crearon directorios y archivos de configuración |
| Inicio del servidor PHP | ✅ Aprobado | El servidor local respondió correctamente |
| Instalación de OpenCart | ✅ Aprobado | La aplicación quedó instalada en el entorno de prueba |
| Instalación de PHPUnit | ✅ Aprobado | PHPUnit quedó disponible para ejecutar las suites |
| Verificación de la tabla `oc_order` | ✅ Aprobado | La estructura pudo consultarse correctamente |
| Ejecución de pruebas de integración | ✅ Aprobado | El paso `Run Integration Tests` terminó con éxito |
| Smoke test del frontend local | ✅ Aprobado | El frontend local respondió correctamente |
| Smoke test del administrador local | ✅ Aprobado | El panel administrativo local respondió correctamente |
| Creación del enlace público temporal | ✅ Aprobado | El túnel público fue creado |
| Actualización de URL en configuración y BD | ✅ Aprobado | Las URLs públicas se aplicaron correctamente |
| Smoke test del frontend público | ✅ Aprobado | El frontend público respondió correctamente |
| Smoke test del administrador público | ✅ Aprobado | El panel administrativo público respondió correctamente |

### Conclusión de la ejecución

La suite de integración se ejecutó satisfactoriamente. El pipeline pudo preparar MySQL, instalar OpenCart, ejecutar PHPUnit y validar el acceso al frontend y al panel administrativo tanto de forma local como mediante el enlace público temporal.

No se registraron errores ni fallos. El paso `Keep server alive temporarily` mantuvo disponible el servidor público durante 15 minutos y después terminó correctamente. Las tareas de limpieza cerraron los procesos y eliminaron el contenedor MySQL sin afectar el resultado.

### Resultado consolidado de PHPUnit

| Métrica | Resultado |
|---|---|
| Suites ejecutadas | 10 |
| Tests ejecutados | 55 |
| Assertions realizadas | 111 |
| Errores | 0 |
| Fallos | 0 |
| Suites omitidas | 0 |
| Duración acumulada informada por PHPUnit | 1.890 segundos |
| Memoria informada por cada suite | 8.00 MB |
| Resultado final | ✅ Aprobado |

### Resultado por suite

| Suite | Tests | Assertions | Duración | Resultado |
|---|---:|---:|---:|---|
| `CarritoCheckoutIntegrationTest.php` | 10 | 25 | 0.040 s | ✅ Aprobado |
| `CheckoutInventoryIntegrationTest.php` | 4 | 8 | 1.019 s | ✅ Aprobado |
| `ClientesAutenticacionIntegrationTest.php` | 10 | 24 | 0.683 s | ✅ Aprobado |
| `CatalogoBusquedaIntegrationTest.php` | 5 | 11 | 0.018 s | ✅ Aprobado |
| `GestionInventarioIntegrationTest.php` | 5 | 5 | 0.019 s | ✅ Aprobado |
| `SistemaResenasIntegrationTest.php` | 5 | 10 | 0.023 s | ✅ Aprobado |
| `PagoIntegrationTest.php` | 4 | 6 | 0.022 s | ✅ Aprobado |
| `PedidoIntegrationTest.php` | 4 | 9 | 0.024 s | ✅ Aprobado |
| `EnvioIntegrationTest.php` | 4 | 6 | 0.021 s | ✅ Aprobado |
| `TotalesDescuentosIntegrationTest.php` | 4 | 7 | 0.021 s | ✅ Aprobado |
| **Total** | **55** | **111** | **1.890 s** | **✅ Aprobado** |

### Validación HTTP

Los smoke tests locales y públicos del frontend y del panel administrativo respondieron satisfactoriamente. En la validación pública, ambos endpoints devolvieron el código HTTP `200`.

### Advertencias no bloqueantes

El log contiene advertencias que no provocaron fallos:

- GitHub Actions informó que Node.js 20 está deprecado y que `actions/checkout@v4` fue ejecutado mediante Node.js 24.
- MySQL advirtió sobre opciones de configuración deprecadas y certificados autofirmados del contenedor de pruebas.
- El comando de MySQL mostró una advertencia por utilizar la contraseña en la línea de comandos.

Estas advertencias no afectaron las 55 pruebas, los 111 assertions ni los smoke tests.

---

## Registro de próximas ejecuciones

Para conservar la trazabilidad, cada ejecución posterior debe registrar:

- fecha;
- enlace del workflow;
- rama y commit;
- cantidad de tests y assertions;
- duración;
- resultado final;
- pruebas omitidas o incompletas;
- incidentes relacionados.

Los reportes detallados pueden almacenarse en:

```text
tests/integracion/reports/resultado-AAAA-MM-DD.md
```

---

## Criterios de aprobación

Una ejecución de integración se considera aprobada cuando:

- PHPUnit termina sin errores ni fallos;
- la conexión y las operaciones sobre MySQL funcionan;
- no se producen inconsistencias en las tablas verificadas;
- los módulos intercambian datos correctamente;
- los smoke tests responden satisfactoriamente;
- el proceso devuelve un código de salida exitoso.

---

## Buenas prácticas

- Utilizar una base de datos exclusiva para pruebas.
- No guardar credenciales ni secretos en el repositorio.
- Preparar y limpiar los datos utilizados por cada caso.
- Evitar que una prueba dependa del resultado de otra.
- Mantener las pruebas repetibles y deterministas.
- Documentar como incidente cualquier fallo reproducible.
- Actualizar este README cuando cambie la estructura, el pipeline o los comandos de ejecución.
