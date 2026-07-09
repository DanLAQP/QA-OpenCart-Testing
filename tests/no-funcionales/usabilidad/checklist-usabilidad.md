# Checklist de Usabilidad — Evaluación Heurística (Nielsen)

Evaluación heurística de usabilidad sobre OpenCart (`upload/`), usando las
[10 heurísticas de usabilidad de Jakob Nielsen](https://www.nngroup.com/articles/ten-usability-heuristics/)
aplicadas a los 6 módulos funcionales del proyecto. A diferencia de rendimiento o seguridad,
esta evaluación depende de criterio humano — no se puede automatizar por completo, aunque
algunos hallazgos sí se verificaron de forma objetiva (mensajes de error reales, validación de
formularios).

**Ambiente evaluado**: `http://localhost/QA-OpenCart-Testing/upload`
**Fecha**: 2026-07-09
**Evidencia visual de apoyo**: capturas en [`../responsive/`](../responsive/checklist-responsive.md)
y [`docs/evidencias/no_funcionales/`](../../../docs/evidencias/no_funcionales/)

---

## Las 10 heurísticas aplicadas

### H1 — Visibilidad del estado del sistema

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H1-01 | Al agregar un producto al carrito, ¿el usuario recibe confirmación clara? | Carrito | ✅ Mensaje `"Success: You have added ... to your shopping cart!"` con nombre del producto |
| H1-02 | Al fallar el login, ¿se informa claramente que fue rechazado? | Login | ✅ Mensaje visible "Invalid token session. Please login again." (ver captura `prueba_responsive_login.png`) |
| H1-03 | ¿El carrito muestra la cantidad de ítems y total en todo momento (header)? | Carrito | ✅ El header muestra "N item(s) - $X.XX" de forma persistente en todas las páginas |
| H1-04 | ¿Al enviar una reseña, se confirma que quedó pendiente de aprobación? | Reseñas | ✅ Mensaje: "Thank you for your review. It has been submitted to the webmaster for approval." |

### H2 — Coincidencia entre el sistema y el mundo real

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H2-01 | ¿Los mensajes de error usan lenguaje natural, no códigos técnicos? | Todos | ✅ Ej. `"Product could not be found!"`, `"E-Mail Address does not appear to be valid!"` — lenguaje claro, no códigos de error crípticos |
| H2-02 | ¿La terminología del checkout es la esperada por un usuario de e-commerce (Carrito, Checkout, Envío, Pago)? | Checkout | ✅ Terminología estándar de e-commerce |

### H3 — Control y libertad del usuario

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H3-01 | ¿El usuario puede eliminar un producto del carrito fácilmente? | Carrito | ✅ Ruta `checkout/cart.remove` disponible con un solo click |
| H3-02 | ¿El usuario puede modificar la cantidad sin tener que eliminar y re-agregar? | Carrito | ✅ Ruta `checkout/cart.edit` permite actualizar cantidad in-place |
| H3-03 | ¿Hay forma de cancelar/deshacer un registro a medio completar? | Login/Registro | ⏳ Pendiente de revisión visual (verificar si hay botón "Cancelar" o solo navegación hacia atrás) |

### H4 — Consistencia y estándares

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H4-01 | ¿El diseño visual (colores, tipografía, botones) es consistente entre tienda y admin? | Todos | ⏳ Pendiente de revisión visual comparativa |
| H4-02 | ¿Los formularios de login (tienda vs. admin) siguen el mismo patrón visual? | Login | ✅ Ambos usan el mismo esquema Bootstrap con campos Username/Password centrados (ver capturas responsive) |

### H5 — Prevención de errores

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H5-01 | ¿El registro valida que `password` y `confirm` coincidan antes de crear la cuenta? | Login/Registro | 🔴 **Hallazgo — ver [INC-USAB-001](incident-reports/INC-USAB-001-sin-validacion-confirmar-password.md)**: el backend acepta el registro exitosamente aunque `confirm` no coincida con `password` |
| H5-02 | ¿El carrito impide agregar cantidades negativas o no numéricas antes de enviar? | Carrito/Inventario | ⚠️ Ya documentado en `tests/integracion/` (INC-INTEGRACION-001/002): la API castea valores inválidos en vez de rechazarlos con validación previa |
| H5-03 | ¿El formulario de registro marca visualmente los campos requeridos? | Login/Registro | ⏳ Pendiente de revisión visual (verificar asteriscos o indicadores) |

### H6 — Reconocer en lugar de recordar

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H6-01 | ¿El detalle de producto muestra el precio, stock y opciones sin que el usuario deba recordarlos de otra página? | Catálogo | ✅ Toda la información relevante está en la misma vista de detalle |
| H6-02 | ¿El checkout resume el contenido del carrito antes de confirmar (sin que el usuario deba recordarlo)? | Checkout | ✅ `checkout/confirm` muestra resumen de productos y totales antes de la confirmación final |

### H7 — Flexibilidad y eficiencia de uso

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H7-01 | ¿Existe búsqueda rápida además de la navegación por categorías? | Catálogo | ✅ Barra de búsqueda visible en el header de toda la tienda |
| H7-02 | ¿Un cliente recurrente puede reordenar más rápido (historial de pedidos, "comprar de nuevo")? | Checkout | ⏳ Pendiente de revisión (verificar `account/order` y si permite repetir un pedido) |

### H8 — Diseño estético y minimalista

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H8-01 | ¿El dashboard de admin muestra solo la información relevante sin sobrecargar la pantalla? | Inventario (admin) | ✅ Widgets resumidos (Total Orders, Sales, Customers, People Online) + gráficos, sin exceso de texto (ver captura `prueba_resposive_con_responsively.png`) |
| H8-02 | ¿La página de producto evita ruido visual innecesario? | Catálogo | ⏳ Pendiente de revisión visual |

### H9 — Ayudar a reconocer, diagnosticar y recuperarse de errores

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H9-01 | Cuando falla la validación de registro, ¿el mensaje indica exactamente qué campo y por qué? | Login/Registro | ✅ Respuesta estructurada por campo: `{"firstname": "...", "email": "...", "password": "..."}` — cada error se puede mostrar junto a su campo específico |
| H9-02 | ¿Un error 404 de OpenCart ofrece una salida clara (volver al home)? | Todos | ✅ Página 404 incluye botón "Continue" que regresa a `common/home` |
| H9-03 | Cuando la BD no está disponible, ¿el error es comprensible para un usuario final? | Todos | 🔴 **Hallazgo ya documentado en Disponibilidad** ([INC-DISP-001](../disponibilidad/incident-reports/INC-DISP-001-error-no-controlado-sin-bd.md)): se muestra un stack trace técnico, no un mensaje amigable |

### H10 — Ayuda y documentación

| # | Verificación | Módulo | Resultado |
|---|---|---|---|
| H10-01 | ¿Existen páginas de ayuda accesibles (Términos, Contacto, Envíos)? | Todos | ✅ Footer incluye "Terms & Conditions", "Delivery Information", "About Us", "Privacy Policy", "Contact Us" |
| H10-02 | ¿El checkout explica qué pasa después de confirmar el pedido? | Checkout | ⏳ Pendiente de revisión visual |

---

## Resumen de hallazgos

| ID | Severidad | Resumen |
|---|---|---|
| [INC-USAB-001](incident-reports/INC-USAB-001-sin-validacion-confirmar-password.md) | Alta | El registro de cuenta no valida que `password` y `confirm` coincidan — el backend acepta contraseñas distintas sin advertir al usuario |

## Puntos pendientes de revisión visual manual

Varios checks quedaron marcados ⏳ porque requieren juicio visual directo navegando la
aplicación (no solo inspección de HTML/API):

- H3-03: opción de cancelar un registro a medio completar
- H4-01: consistencia visual completa entre tienda y admin
- H5-03: indicadores visuales de campos requeridos en el formulario de registro
- H7-02: facilidad de reordenar un pedido anterior
- H8-02: nivel de ruido visual en la página de producto
- H10-02: claridad de los pasos posteriores a confirmar un pedido

Se recomienda completar estos puntos navegando manualmente la aplicación y actualizando este
checklist con el resultado observado.

## Conclusión

La evaluación heurística encontró una base sólida en varias dimensiones: mensajes de error
claros y en lenguaje natural (H2, H9), buena visibilidad del estado del sistema (H1), y control
razonable sobre el carrito (H3). El hallazgo más relevante es la **ausencia de validación de
confirmación de contraseña** en el registro (H5), un descuido de prevención de errores que
puede llevar a usuarios a quedar bloqueados de su propia cuenta por un typo no detectado.
