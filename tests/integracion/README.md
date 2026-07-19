# Índice de Pruebas de Integración — OpenCart

Este árbol organiza las suites de integración del proyecto por dominio funcional y las relaciona con una estrategia de cobertura en **5 capas**.

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

---

## Observación importante

Esta estructura no garantiza por sí sola una cobertura total, pero sí deja una base organizada para medir y expandir la cobertura de integración con trazabilidad real hacia código, base de datos y CI/CD.
