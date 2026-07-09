# Pruebas de Aceptación: Checkout y Pago

Trazable a [`docs/Requisitos-funcionales/Checkout_pago.md`](../../docs/Requisitos-funcionales/Checkout_pago.md).

---

## Historia de Usuario 1: Iniciar el proceso de compra desde el carrito

**Como** cliente con productos en mi carrito,
**quiero** avanzar al proceso de compra,
**para** completar mi pedido y recibir mis productos.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CHK-001 a RF-CHK-011

```gherkin
Escenario: Avanzar a checkout con un carrito válido
  Dado que tengo productos con stock suficiente en mi carrito
  Cuando hago clic en "Checkout"
  Entonces accedo a la pantalla principal de checkout
  Y veo los pasos que debo completar (dirección, envío, pago, confirmación)

Escenario: No se puede avanzar a checkout con un carrito inválido
  Dado que mi carrito está vacío, o algún producto ya no tiene stock suficiente
  Cuando intento acceder a checkout
  Entonces el sistema me redirige de vuelta al carrito
  Y me explica por qué no puedo continuar
```

**Verificado**: ✅ confirmado contra `upload/` — `checkout/checkout` responde correctamente y
redirige a `checkout/cart` cuando no se cumplen las condiciones mínimas.

---

## Historia de Usuario 2: Ingresar direcciones de envío y facturación

**Como** cliente,
**quiero** indicar a dónde debo recibir mi pedido y qué datos de facturación usar,
**para** que el pedido llegue al lugar correcto.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CHK-012 a RF-CHK-039

```gherkin
Escenario: Registrar una nueva dirección de envío durante el checkout
  Dado que estoy en el paso de dirección de envío
  Cuando completo nombre, apellido, dirección, ciudad, país y código postal válidos
  Entonces el sistema acepta la dirección
  Y avanzo al siguiente paso (método de envío)

Escenario: Reutilizar una dirección ya guardada
  Dado que tengo una dirección guardada de una compra anterior
  Cuando llego al paso de dirección durante el checkout
  Entonces puedo seleccionar esa dirección existente en lugar de escribirla de nuevo
```

**Estado de este criterio**: ⏳ Pendiente de verificación manual completa (requiere una cuenta
de cliente con dirección previamente guardada; verificable con navegación manual, no solo con
peticiones HTTP aisladas).

---

## Historia de Usuario 3: Elegir método de envío y de pago

**Como** cliente,
**quiero** elegir cómo quiero recibir mi pedido y cómo quiero pagarlo,
**para** ajustar la compra a mis preferencias y posibilidades.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CHK-040 a RF-CHK-065

```gherkin
Escenario: Seleccionar un método de envío disponible
  Dado que mi dirección de envío es válida
  Cuando llego al paso de método de envío
  Entonces veo las opciones de envío disponibles para mi dirección
  Y puedo seleccionar una de ellas para continuar

Escenario: No hay métodos de envío disponibles
  Dado que mi dirección no tiene ninguna zona de envío configurada en la tienda
  Cuando llego al paso de método de envío
  Entonces el sistema me informa que no hay opciones de envío disponibles
  Y no me deja avanzar hasta resolver esto
```

**Estado de este criterio**: ⏳ Pendiente de verificación manual (requiere configuración de
zonas de envío y métodos de pago habilitados en el ambiente, fuera del alcance de una
verificación por HTTP aislada).

---

## Historia de Usuario 4: Confirmar y finalizar el pedido

**Como** cliente,
**quiero** revisar un resumen final antes de confirmar mi compra,
**para** asegurarme de que todo está correcto antes de pagar.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CHK-066 a RF-CHK-080

```gherkin
Escenario: Ver el resumen final antes de confirmar
  Dado que completé dirección, método de envío y método de pago
  Cuando llego al paso de confirmación
  Entonces veo un resumen con todos los productos, cantidades, costos de envío, impuestos y total final
  Y puedo confirmar el pedido desde esa misma pantalla

Escenario: El sistema revalida el stock justo antes de confirmar
  Dado que un producto de mi carrito se agotó mientras yo completaba el checkout
  Cuando intento confirmar el pedido
  Entonces el sistema me avisa que ese producto ya no está disponible
  Y no genera un pedido con productos que no puede entregar
```

**Verificado**: ✅ parcialmente confirmado — la revalidación de stock antes de confirmar ya se
verificó en las pruebas funcionales del módulo de Gestión de Inventario (RF-INV-018).

---

## Historia de Usuario 5: Ver confirmación de éxito o de fallo al terminar

**Como** cliente,
**quiero** saber claramente si mi compra se completó o no,
**para** tener certeza de si debo esperar mi pedido o intentar de nuevo.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CHK-081 a RF-CHK-088

```gherkin
Escenario: Pantalla de éxito tras completar la compra
  Dado que completé exitosamente todos los pasos del checkout
  Cuando confirmo el pedido
  Entonces veo una pantalla de éxito con un mensaje de agradecimiento
  Y mi carrito queda vacío

Escenario: Pantalla de fallo con información de contacto
  Dado que ocurre un error al procesar mi pago
  Cuando el checkout no puede completarse
  Entonces veo una pantalla de fallo, no un error técnico
  Y veo información de contacto para pedir ayuda
```

**Estado de este criterio**: ⚠️ **Riesgo identificado** — relacionado con
[INC-DISP-001](../no-funcionales/disponibilidad/incident-reports/INC-DISP-001-error-no-controlado-sin-bd.md):
si la base de datos falla justo durante el checkout (un escenario de "fallo" real), el sistema
actualmente expone un stack trace técnico en vez de la pantalla de fallo amigable descrita en
este criterio. Este es el mismo hallazgo de disponibilidad, pero desde la perspectiva de
negocio: un cliente que intenta pagar y la base de datos falla en ese momento vería un error
técnico confuso en vez de un mensaje claro con opción de contacto.

---

## Registro de Ejecución

| Historia | Prioridad | Resultado | Evidencia / Incidente |
|---|---|---|---|
| 1. Iniciar checkout desde el carrito | Alta | ✅ Cumple | Verificado contra `upload/` |
| 2. Ingresar direcciones | Alta | ⏳ Pendiente | Requiere prueba manual con cuenta y direcciones guardadas |
| 3. Elegir envío y pago | Alta | ⏳ Pendiente | Requiere configuración de zonas de envío |
| 4. Confirmar pedido | Alta | ✅ Cumple (parcial) | Revalidación de stock ya verificada en RF-INV-018 |
| 5. Pantalla de éxito/fallo | Alta | ⚠️ Riesgo identificado | [INC-DISP-001](../no-funcionales/disponibilidad/incident-reports/INC-DISP-001-error-no-controlado-sin-bd.md) |
