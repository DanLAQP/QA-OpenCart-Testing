# Pruebas de Aceptación: Sistema de Reseñas

Trazable a [`docs/Requisitos-funcionales/Reseñas.md`](../../docs/Requisitos-funcionales/Reseñas.md).

---

## Historia de Usuario 1: Ver las reseñas de otros clientes antes de comprar

**Como** cliente potencial,
**quiero** leer las opiniones de otros compradores sobre un producto,
**para** tomar una decisión de compra más informada.

**Prioridad**: Alta
**Requisitos relacionados**: RF-REV-001 a RF-REV-006, RF-REV-026 a RF-REV-029

```gherkin
Escenario: Ver reseñas publicadas de un producto
  Dado que un producto tiene reseñas ya aprobadas
  Cuando visito la página de ese producto
  Entonces veo la pestaña de reseñas con el autor, texto, calificación y fecha de cada una
  Y las reseñas más recientes aparecen primero

Escenario: Un producto sin reseñas no muestra errores
  Dado que un producto no tiene ninguna reseña todavía
  Cuando visito su página
  Entonces veo la sección de reseñas vacía, con un mensaje amigable
  Y no veo ningún error técnico
```

**Verificado**: ✅ confirmado contra `upload/` — `product/review.list` responde correctamente,
incluso sin reseñas.

---

## Historia de Usuario 2: Escribir una reseña de un producto que compré

**Como** cliente que ya recibió un producto,
**quiero** dejar mi opinión y calificación sobre él,
**para** compartir mi experiencia con otros compradores.

**Prioridad**: Alta
**Requisitos relacionados**: RF-REV-008 a RF-REV-023

```gherkin
Escenario: Enviar una reseña válida
  Dado que estoy en la página de un producto
  Cuando completo el formulario de reseña con un nombre, un comentario de al menos 25 caracteres y una calificación entre 1 y 5
  Entonces el sistema confirma que mi reseña fue recibida
  Y me indica que quedará pendiente de aprobación antes de publicarse

Escenario: Rechazo por calificación fuera de rango
  Dado que intento enviar una reseña
  Cuando indico una calificación de 6 (fuera del rango permitido de 1 a 5)
  Entonces el sistema rechaza el envío
  Y me indica que la calificación debe estar entre 1 y 5
```

**Verificado**: ✅ confirmado contra `upload/` — el envío de una reseña válida responde
`"Thank you for your review. It has been submitted to the webmaster for approval."`.

---

## Historia de Usuario 3: Las reseñas no aparecen públicamente hasta ser aprobadas

**Como** dueño de la tienda,
**quiero** revisar las reseñas antes de que se publiquen,
**para** evitar contenido ofensivo, spam o falso en mi tienda.

**Prioridad**: Alta
**Requisitos relacionados**: RF-REV-022, RF-REV-026

```gherkin
Escenario: Una reseña recién enviada no es visible de inmediato
  Dado que acabo de enviar una reseña nueva
  Cuando reviso la sección de reseñas del producto sin que un administrador la haya aprobado
  Entonces mi reseña todavía no aparece en el listado público

Escenario: Una reseña aprobada por el administrador sí se hace visible
  Dado que un administrador aprobó una reseña pendiente
  Cuando cualquier cliente visita la página de ese producto
  Entonces la reseña aprobada ya aparece en el listado público
```

**Estado de este criterio**: ⏳ Pendiente de verificación manual completa (requiere acceso al
panel admin para aprobar la reseña y confirmar el cambio de visibilidad; el envío y el mensaje
de "pendiente de aprobación" ya se verificaron).

---

## Historia de Usuario 4: Moderar reseñas desde el panel de administración

**Como** administrador de la tienda,
**quiero** filtrar, editar y eliminar reseñas desde el panel administrativo,
**para** mantener el contenido de mi tienda apropiado y actualizado.

**Prioridad**: Media
**Requisitos relacionados**: RF-REV-035 a RF-REV-045

```gherkin
Escenario: Filtrar reseñas por estado
  Dado que hay reseñas pendientes y aprobadas en el sistema
  Cuando filtro el listado administrativo por estado "Pendiente"
  Entonces veo únicamente las reseñas que aún no se han aprobado

Escenario: Eliminar una reseña inapropiada
  Dado que una reseña contiene contenido inapropiado
  Cuando la elimino desde el panel administrativo
  Entonces esa reseña deja de existir tanto en el panel como en el frontend
```

**Estado de este criterio**: ⏳ Pendiente de verificación manual (requiere navegación real del
panel admin de reseñas, no cubierto por las pruebas HTTP automatizadas de este ciclo).

---

## Registro de Ejecución

| Historia | Prioridad | Resultado | Evidencia / Incidente |
|---|---|---|---|
| 1. Ver reseñas antes de comprar | Alta | ✅ Cumple | Verificado contra `upload/` |
| 2. Escribir una reseña | Alta | ✅ Cumple | Verificado contra `upload/` |
| 3. Reseñas requieren aprobación | Alta | ⏳ Pendiente (parcial) | Envío verificado, aprobación pendiente de prueba manual |
| 4. Moderar reseñas en admin | Media | ⏳ Pendiente | Requiere navegación manual del panel admin |
