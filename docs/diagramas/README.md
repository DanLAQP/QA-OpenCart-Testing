# Diagramas de Arquitectura y Flujos

Este directorio contiene diagramas visuales de los módulos y procesos del sistema QA OpenCart Testing.

## Gestión de Inventario

- [Flujo de Stock](gestion-inventario-flujo-stock.md) — Validación y actualización de stock desde catálogo hasta confirmación
- [Estructura de Variantes](gestion-inventario-estructura-variantes.md) — Relaciones entre productos, variantes, opciones e inventario
- [Procesos de Validación](gestion-inventario-procesos-validacion.md) — Árbol de decisiones para validaciones de stock
- [Arquitectura del Módulo](gestion-inventario-arquitectura.md) — Componentes y entidades del módulo

## Login y Registro

- [Flujo de Login y Registro](login-registro-flujo.md) — Login, registro, bloqueo por intentos fallidos y recuperación de contraseña
- [Estructura de Datos](login-registro-estructura.md) — Entidades de cliente, tokens, intentos fallidos y aprobación
- [Procesos de Validación](login-registro-procesos-validacion.md) — Árbol de decisiones para autenticación, registro y reset
- [Arquitectura del Módulo](login-registro-arquitectura.md) — Componentes y entidades del módulo

## Catálogo y Búsqueda

- [Flujo de Catálogo y Búsqueda](catalogo-busqueda-flujo.md) — Navegación, búsqueda, detalle de producto y comparación
- [Estructura de Datos](catalogo-busqueda-estructura.md) — Entidades de producto, categoría, fabricante y reseñas
- [Procesos de Validación](catalogo-busqueda-procesos-validacion.md) — Árbol de decisiones para reglas de visualización
- [Arquitectura del Módulo](catalogo-busqueda-arquitectura.md) — Componentes y entidades del módulo

## Carrito de Compras

- [Flujo del Carrito](carrito-flujo.md) — Agregar, editar, eliminar productos y persistencia de sesión
- [Estructura de Datos](carrito-estructura.md) — Entidades de carrito, opciones seleccionadas y suscripciones
- [Procesos de Validación](carrito-procesos-validacion.md) — Árbol de decisiones para agregar/editar/eliminar
- [Arquitectura del Módulo](carrito-arquitectura.md) — Componentes y entidades del módulo

## Checkout y Pago

- [Flujo de Checkout y Pago](checkout-pago-flujo.md) — Direcciones, envío, pago y confirmación del pedido
- [Estructura de Datos](checkout-pago-estructura.md) — Entidades de orden, totales e historial
- [Procesos de Validación](checkout-pago-procesos-validacion.md) — Árbol de decisiones para cada paso del checkout
- [Arquitectura del Módulo](checkout-pago-arquitectura.md) — Componentes y entidades del módulo

## Sistema de Reseñas

- [Flujo de Reseñas](resenas-flujo.md) — Visualización, envío y moderación de reseñas
- [Estructura de Datos](resenas-estructura.md) — Entidad de reseña y condiciones de visibilidad
- [Procesos de Validación](resenas-procesos-validacion.md) — Árbol de decisiones para envío y moderación
- [Arquitectura del Módulo](resenas-arquitectura.md) — Componentes y entidades del módulo

---

## Cómo usar estos diagramas

- Los diagramas están en formato **Mermaid** y se renderizan automáticamente en GitHub y plataformas similares.
- Cada archivo `.md` contiene un diagrama específico con su descripción.
- Úsalos como referencia para entender flujos, relaciones y procesos del sistema.
