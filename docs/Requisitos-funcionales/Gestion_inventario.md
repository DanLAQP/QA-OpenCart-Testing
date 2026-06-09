# Módulo: Gestión de Inventario

## Descripción general

El módulo **Gestión de Inventario** administra la disponibilidad de productos, variantes y opciones con impacto en stock. Controla cantidades, validaciones de inventario en catálogo, carrito, checkout y API, y soporta estados de stock y administración operativa desde backend.

Este módulo es clave para asegurar coherencia entre la oferta visible al usuario y la disponibilidad real de compra.

---

## Alcance funcional

El módulo cubre las siguientes áreas:

- Stock de producto
- Variantes y opciones con impacto en inventario
- Validación de inventario en carrito y checkout
- Validación de inventario vía API
- Estados de stock y disponibilidad
- Administración y consulta de inventario
- Integración con catálogo y búsqueda

---

## Requisitos funcionales

## 1. Stock de producto

- **RF-INV-001** El sistema debe almacenar cantidad disponible por producto.
- **RF-INV-002** El sistema debe permitir editar la cantidad disponible de un producto.
- **RF-INV-003** El sistema debe usar la cantidad del producto para validar disponibilidad en frontend.
- **RF-INV-004** El sistema debe impedir continuar con compra cuando no haya stock suficiente y la configuración no permita vender sin stock.
- **RF-INV-005** El sistema debe considerar la cantidad mínima del producto para compra.
- **RF-INV-006** El sistema debe manejar estado del producto en tienda y fecha de disponibilidad para exponerlo al catálogo.

## 2. Variantes y opciones con impacto en inventario

- **RF-INV-007** El sistema debe soportar variantes de producto.
- **RF-INV-008** El sistema debe soportar producto maestro y producto variante.
- **RF-INV-009** El sistema debe mezclar opciones derivadas de variantes cuando exista configuración override.
- **RF-INV-010** El sistema debe manejar stock a nivel de opción/valor cuando la opción descuente inventario.
- **RF-INV-011** El sistema debe validar stock de opciones tipo select, radio y checkbox.
- **RF-INV-012** El sistema debe acumular impactos de precio, puntos y peso provenientes de opciones.
- **RF-INV-013** El sistema debe marcar un producto como sin stock si una opción requerida no tiene disponibilidad suficiente.

## 3. Validación de inventario en carrito y checkout

- **RF-INV-014** El sistema debe validar stock del producto al agregarlo al carrito.
- **RF-INV-015** El sistema debe validar stock de opciones al agregar al carrito.
- **RF-INV-016** El sistema debe validar la suma total de cantidades del mismo producto dentro del carrito.
- **RF-INV-017** El sistema debe validar cantidades mínimas del producto antes de checkout.
- **RF-INV-018** El sistema debe recalcular stock disponible durante checkout y confirmación.
- **RF-INV-019** El sistema debe validar stock también cuando el carrito se reconstruye desde sesión/base de datos.

## 4. Validación de inventario vía API

- **RF-INV-020** El sistema debe validar existencia del producto en operaciones API.
- **RF-INV-021** El sistema debe validar stock del producto por API.
- **RF-INV-022** El sistema debe validar stock de opciones por API.
- **RF-INV-023** El sistema debe validar cantidades mínimas por API en confirmación.
- **RF-INV-024** El sistema debe considerar cantidades ya asociadas a un pedido existente cuando una operación API se hace sobre `order_id`.
- **RF-INV-025** El sistema debe devolver errores estructurados por producto u opción cuando falle una validación de inventario en API.

## 5. Estados de stock y disponibilidad

- **RF-INV-026** El sistema debe manejar estados de stock para productos sin cantidad disponible.
- **RF-INV-027** El sistema debe mostrar disponibilidad a partir del estado de stock o de la cantidad, según configuración.
- **RF-INV-028** El sistema debe usar el identificador de estado de stock del producto cuando la cantidad sea cero.
- **RF-INV-029** El sistema debe soportar visualización configurable del stock en catálogo y comparación.

## 6. Administración y consulta de inventario

- **RF-INV-030** El sistema debe permitir listar productos en administración con su cantidad.
- **RF-INV-031** El sistema debe permitir filtrar productos por rango de cantidad en administración.
- **RF-INV-032** El sistema debe permitir ordenar productos por cantidad en administración.
- **RF-INV-033** El sistema debe mostrar productos con variantes desde administración.
- **RF-INV-034** El sistema debe permitir acceder al mantenimiento de variantes desde administración.
- **RF-INV-035** El sistema debe distinguir productos maestros de variantes en la gestión administrativa.

## 7. Integración con catálogo y búsqueda

- **RF-INV-036** El sistema debe usar el stock y la disponibilidad al mostrar productos en comparación.
- **RF-INV-037** El sistema debe mantener disponible en catálogo solo productos activos, vigentes y habilitados para la tienda.
- **RF-INV-038** El sistema debe conservar la información de cantidad y disponibilidad para el detalle de producto.

---

## Resumen cuantitativo

- Total de requisitos del módulo: **38**

---

## Observaciones

- Este README concentra la lógica funcional de stock y disponibilidad del sistema.
- Puede utilizarse como base para pruebas de inventario, catálogo y checkout.
