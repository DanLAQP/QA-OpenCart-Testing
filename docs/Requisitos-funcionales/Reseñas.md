# Módulo: Sistema de Reseñas

## Descripción general

El módulo **Sistema de Reseñas** administra la creación, publicación, visualización y moderación de reseñas de productos. Incluye el flujo frontend para escribir reseñas, la persistencia y publicación de reseñas aprobadas y la gestión administrativa de las mismas.

Este módulo conecta experiencia de compra, reputación del producto y control editorial/moderación desde administración.

---

## Alcance funcional

El módulo cubre las siguientes áreas:

- Publicación y visualización de reseñas en frontend
- Validación y envío de reseñas
- Persistencia y publicación
- Integración con catálogo y producto
- Gestión administrativa de reseñas

---

## Requisitos funcionales

## 1. Publicación y visualización de reseñas en frontend

- **RF-REV-001** El sistema debe mostrar el bloque de reseñas asociado a un producto.
- **RF-REV-002** El sistema debe identificar el producto sobre el que se consultan o registran reseñas mediante `product_id`.
- **RF-REV-003** El sistema debe mostrar un listado paginado de reseñas por producto.
- **RF-REV-004** El sistema debe paginar las reseñas de producto en bloques de 5 elementos por página.
- **RF-REV-005** El sistema debe mostrar autor, texto, rating y fecha de cada reseña publicada.
- **RF-REV-006** El sistema debe mostrar el total de reseñas del producto para la paginación.
- **RF-REV-007** El sistema debe mostrar mensaje de login/registro cuando el usuario no pueda reseñar como invitado.
- **RF-REV-008** El sistema debe permitir reseñar a clientes autenticados.
- **RF-REV-009** El sistema debe permitir reseñar como invitado solo cuando la configuración lo habilite.
- **RF-REV-010** El sistema debe prellenar el nombre del cliente en el formulario de reseña cuando el usuario esté autenticado.

## 2. Validación y envío de reseñas

- **RF-REV-011** El sistema debe proteger el envío de reseñas con un token antifraude.
- **RF-REV-012** El sistema debe generar un token de reseña por sesión para prevenir envíos automatizados o repetición maliciosa.
- **RF-REV-013** El sistema debe validar que el módulo de reseñas esté habilitado antes de aceptar una reseña.
- **RF-REV-014** El sistema debe validar que el producto exista antes de aceptar una reseña.
- **RF-REV-015** El sistema debe validar la longitud del autor de la reseña.
- **RF-REV-016** El sistema debe validar la longitud del texto de la reseña.
- **RF-REV-017** El sistema debe validar que la calificación esté entre 1 y 5.
- **RF-REV-018** El sistema debe rechazar reseñas de invitados cuando la configuración no lo permita.
- **RF-REV-019** El sistema debe exigir compra previa del producto antes de permitir reseñar cuando la configuración de “reseña solo para compradores” esté habilitada.
- **RF-REV-020** El sistema debe soportar captcha en el envío de reseñas cuando esté habilitado para esa página.
- **RF-REV-021** El sistema debe devolver errores estructurados cuando falle alguna validación del envío de reseña.
- **RF-REV-022** El sistema debe registrar la reseña cuando todas las validaciones sean correctas.
- **RF-REV-023** El sistema debe devolver confirmación de éxito al registrar una reseña válida.

## 3. Persistencia y publicación

- **RF-REV-024** El sistema debe guardar autor, cliente, producto, texto, rating y fechas de creación/modificación de la reseña.
- **RF-REV-025** El sistema debe asociar la reseña al cliente autenticado cuando exista sesión.
- **RF-REV-026** El sistema debe listar en frontend solo reseñas con estado publicado/aprobado.
- **RF-REV-027** El sistema debe listar en frontend solo reseñas de productos activos y disponibles.
- **RF-REV-028** El sistema debe contar para el total del frontend solo reseñas publicadas de productos activos y vigentes.
- **RF-REV-029** El sistema debe ordenar las reseñas frontend por fecha descendente.

## 4. Integración con catálogo y producto

- **RF-REV-030** El sistema debe mostrar el total de reseñas en la ficha del producto.
- **RF-REV-031** El sistema debe mostrar el total de reseñas en la pestaña de reseñas del producto.
- **RF-REV-032** El sistema debe usar la configuración global de reseñas para habilitar o bloquear la escritura de reseñas.
- **RF-REV-033** El sistema debe usar la configuración global para exigir o no autenticación/compra previa en el flujo de reseñas.
- **RF-REV-034** El sistema debe mostrar el estado de reseñas también en comparación de productos cuando la configuración de reseñas esté habilitada.

## 5. Gestión administrativa de reseñas

- **RF-REV-035** El sistema debe permitir listar reseñas en administración.
- **RF-REV-036** El sistema debe permitir filtrar reseñas por producto.
- **RF-REV-037** El sistema debe permitir filtrar reseñas por autor.
- **RF-REV-038** El sistema debe permitir filtrar reseñas por estado.
- **RF-REV-039** El sistema debe permitir filtrar reseñas por rango de fechas.
- **RF-REV-040** El sistema debe permitir ordenar reseñas por producto, autor, rating y fecha.
- **RF-REV-041** El sistema debe permitir paginar el listado administrativo de reseñas.
- **RF-REV-042** El sistema debe permitir crear reseñas desde administración.
- **RF-REV-043** El sistema debe permitir editar reseñas desde administración.
- **RF-REV-044** El sistema debe permitir eliminar reseñas desde administración.
- **RF-REV-045** El sistema debe permitir acceder al formulario de edición de una reseña específica desde el listado administrativo.

---

## Resumen cuantitativo

- Total de requisitos del módulo: **45**

---

## Observaciones

- Este README documenta tanto el flujo público de reseñas como la moderación administrativa.
- Sirve como base para criterios de aceptación y control de publicación.
