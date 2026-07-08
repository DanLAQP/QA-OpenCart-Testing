# Plan de Pruebas de Sistema — OpenCart

## Descripción general

Este documento define el plan de pruebas de sistema para los módulos seleccionados del proyecto OpenCart. Cubre pruebas funcionales de extremo a extremo (E2E), pruebas no funcionales (rendimiento, estrés, compatibilidad, usabilidad, seguridad y adaptabilidad) y la clasificación de Requisitos No Funcionales (RNF) aplicables al proyecto.

**Stack tecnológico:** PHP · PHPUnit · OpenCart · XAMPP · GitHub  
**Módulos cubiertos:**

---

## 1. Objetivos del plan

- Validar que los módulos funcionan correctamente de forma integrada y como sistema completo.
- Detectar errores de integración entre módulos que las pruebas unitarias no cubren.
- Verificar que el sistema cumple los requisitos no funcionales (rendimiento, seguridad, compatibilidad).
- Documentar los flujos E2E críticos del sistema.
- Determinar si el sistema soporta condiciones extremas de carga (estrés).

---

## 2. Tipos de pruebas aplicables al proyecto

| Tipo | ¿Aplica? | Justificación |
|---|---|---|
| **Funcional E2E** | ✅ Sí | Flujos completos de usuario entre módulos |
| **Rendimiento** | ✅ Sí | OpenCart es un e-commerce; la velocidad impacta ventas |
| **Estrés** | ✅ Sí | Simula picos de tráfico en sesiones concurrentes |
| **Compatibilidad** | ✅ Sí | Debe funcionar en distintos navegadores y dispositivos |
| **Seguridad** | ✅ Sí | Maneja credenciales, sesiones y datos personales |
| **Usabilidad** | ✅ Sí | Es una tienda para usuarios finales |
| **Adaptabilidad** | ✅ Sí | Debe ser responsive en móvil, tablet y escritorio |
| **Regresión** | ✅ Sí | Cambios en un módulo pueden romper otro |
| **Volumen** | ⚠️ Parcial | Aplicable si se carga la BD con muchos productos |
| **Recuperación** | ⚠️ Parcial | Verificar comportamiento ante caída del servidor local |

---

## 3. Pruebas Funcionales E2E (End-to-End)

Las pruebas E2E simulan el recorrido completo de un usuario real interactuando con el sistema desplegado, desde la interfaz hasta la base de datos.

### 3.1 Flujos E2E principales

#### E2E-01 — Registro e inicio de sesión completo

**Módulos:** Login y Registro  
**Precondiciones:** Usuario no registrado, sistema desplegado en XAMPP.

| Paso | Acción | Resultado esperado |
|---|---|---|
| 1 | Acceder a `https://github.com/DanLAQP/QA-OpenCart-Testing/actions/runs/` | Se muestra la página principal de la tienda |
| 2 | Ir a `My Account → Register` | Se muestra el formulario de registro |
| 3 | Completar First Name, Last Name, E-Mail, Password | Campos aceptan los datos ingresados |
| 4 | Activar toggle de Privacy Policy | Toggle se activa visualmente |
| 5 | Hacer clic en `Continue` | Cuenta creada, redirige a `account/success` |
| 6 | Ir a `My Account → Login` | Se muestra el formulario de login |
| 7 | Ingresar email y password registrados | Login exitoso, redirige a `account/account` |
| 8 | Hacer clic en `Logout` | Sesión cerrada, redirige a la página principal |

---

#### E2E-02 — Búsqueda y visualización de producto

**Módulos:** Login y Registro + Catálogo y Búsqueda  
**Precondiciones:** Usuario registrado, productos cargados en el sistema.

| Paso | Acción | Resultado esperado |
|---|---|---|
| 1 | Iniciar sesión con credenciales válidas | Sesión iniciada correctamente |
| 2 | Usar la barra de búsqueda con término `'laptop'` | Se muestran resultados relacionados |
| 3 | Hacer clic en un producto de los resultados | Se muestra la ficha completa del producto |
| 4 | Verificar nombre, precio, descripción e imagen | Todos los campos se muestran correctamente |
| 5 | Navegar a una categoría desde el menú | Se muestran los productos de esa categoría |
| 6 | Aplicar ordenamiento por precio | Los productos se reordenan correctamente |

---

#### E2E-03 — Agregar producto al carrito

**Módulos:** Catálogo y Búsqueda + Carrito de Compras  
**Precondiciones:** Usuario autenticado, producto disponible en inventario.

| Paso | Acción | Resultado esperado |
|---|---|---|
| 1 | Buscar un producto disponible | Producto encontrado en resultados |
| 2 | Ingresar al detalle del producto | Se muestra ficha del producto con botón `Add to Cart` |
| 3 | Seleccionar cantidad 1 y hacer clic en `Add to Cart` | Producto agregado, notificación de éxito |
| 4 | Ir al carrito (`Shopping Cart`) | El producto aparece en el carrito con precio y cantidad correctos |
| 5 | Modificar la cantidad a 2 | El subtotal se actualiza correctamente |
| 6 | Eliminar el producto del carrito | El carrito queda vacío |

---

#### E2E-04 — Flujo completo de compra (Carrito → Checkout → Pago)

**Módulos:** Carrito + Checkout / Pago  
**Precondiciones:** Usuario autenticado con dirección configurada, producto en carrito.

| Paso | Acción | Resultado esperado |
|---|---|---|
| 1 | Agregar producto al carrito | Producto en carrito |
| 2 | Ir a `Checkout` | Se muestra el proceso de checkout |
| 3 | Confirmar o ingresar dirección de envío | Dirección aceptada |
| 4 | Seleccionar método de envío | Método de envío seleccionado con costo |
| 5 | Seleccionar método de pago | Método de pago seleccionado |
| 6 | Confirmar el pedido | Orden creada, redirige a página de confirmación con número de orden |
| 7 | Verificar en `My Account → Order History` | La orden aparece en el historial |

---

#### E2E-05 — Dejar reseña de producto tras compra

**Módulos:** Login + Catálogo + Sistema de Reseñas  
**Precondiciones:** Usuario autenticado que ha comprado el producto.

| Paso | Acción | Resultado esperado |
|---|---|---|
| 1 | Iniciar sesión | Sesión activa |
| 2 | Ir al detalle del producto comprado | Se muestra ficha del producto |
| 3 | Ir a la pestaña `Reviews` | Se muestra el formulario de reseña |
| 4 | Ingresar nombre, reseña y calificación (1-5 estrellas) | Campos aceptan los datos |
| 5 | Enviar la reseña | Mensaje de confirmación, reseña en espera de aprobación |
| 6 | Verificar que la reseña aparece tras aprobación del admin | Reseña visible en el producto |

---

#### E2E-06 — Gestión de inventario y visibilidad en catálogo

**Módulos:** Gestión de Inventario + Catálogo y Búsqueda  
**Precondiciones:** Acceso al panel de administración.

| Paso | Acción | Resultado esperado |
|---|---|---|
| 1 | Iniciar sesión en el panel admin | Acceso al dashboard admin |
| 2 | Ir a `Catalog → Products → Add New` | Se muestra formulario de nuevo producto |
| 3 | Completar nombre, precio, categoría y stock=10 | Producto guardado exitosamente |
| 4 | Ir a la tienda frontend y buscar el producto | El producto aparece en resultados de búsqueda |
| 5 | Editar el producto en admin y establecer stock=0 | Producto actualizado |
| 6 | Verificar que en el frontend muestra `Out of Stock` | El estado de stock se refleja correctamente |

---

#### E2E-07 — Recuperación de contraseña y reingreso al sistema

**Módulos:** Login y Registro  
**Precondiciones:** Usuario registrado con email válido configurado.

| Paso | Acción | Resultado esperado |
|---|---|---|
| 1 | Ir a la página de login | Se muestra el formulario de login |
| 2 | Hacer clic en `Forgotten Password` | Redirige a `account/forgotten` |
| 3 | Ingresar email registrado y enviar | Mensaje de confirmación de envío |
| 4 | Acceder al enlace de recuperación recibido | Se muestra formulario de nueva contraseña |
| 5 | Ingresar nueva contraseña válida | Contraseña actualizada, redirige al login |
| 6 | Iniciar sesión con la nueva contraseña | Login exitoso |

---

## 4. Requisitos No Funcionales (RNF)

Los RNF definen la calidad del sistema más allá de sus funciones. Para OpenCart los más relevantes son:

### RNF-01 — Rendimiento

| ID | Requisito | Criterio de aceptación |
|---|---|---|
| RNF-01-01 | La página de login debe cargar en menos de 3 segundos | Medido en Chrome con conexión local |
| RNF-01-02 | La búsqueda de productos debe responder en menos de 2 segundos | Con catálogo de al menos 50 productos |
| RNF-01-03 | El proceso de checkout completo no debe superar 5 segundos por paso | Medido en condiciones normales |
| RNF-01-04 | El registro de un nuevo cliente debe completarse en menos de 3 segundos | Desde el envío del formulario hasta la redirección |

### RNF-02 — Seguridad

| ID | Requisito | Criterio de aceptación |
|---|---|---|
| RNF-02-01 | Las contraseñas deben almacenarse como hash bcrypt | Verificable en la tabla `customer` de la BD |
| RNF-02-02 | Los formularios de login y registro deben protegerse con tokens CSRF | `login_token` y `register_token` presentes en cada sesión |
| RNF-02-03 | El sistema debe bloquear accesos tras intentos fallidos configurados | `error_attempts` mostrado al superar `config_login_attempts` |
| RNF-02-04 | Las URLs de redirección deben validarse contra el dominio del sitio | Redireccionamiento externo es ignorado |
| RNF-02-05 | Los tokens de recuperación deben expirar en 10 minutos | `getTokenByCode()` elimina tokens expirados automáticamente |

### RNF-03 — Usabilidad

| ID | Requisito | Criterio de aceptación |
|---|---|---|
| RNF-03-01 | Los mensajes de error deben ser claros e indicar el campo afectado | Cada error señala el campo específico sin mensajes genéricos innecesarios |
| RNF-03-02 | El flujo de registro no debe requerir más de 5 pasos para completarse | Contar pasos desde acceso hasta `account/success` |
| RNF-03-03 | El carrito debe actualizar precios en tiempo real al cambiar cantidades | Sin necesidad de recargar la página |
| RNF-03-04 | El sistema debe mantener los datos del formulario tras un error de validación | No se pierden los campos ya llenados al mostrar un error |

### RNF-04 — Compatibilidad

| ID | Requisito | Criterio de aceptación |
|---|---|---|
| RNF-04-01 | El sistema debe funcionar correctamente en Chrome (última versión) | Todos los flujos E2E ejecutados sin errores |
| RNF-04-02 | El sistema debe funcionar correctamente en Firefox (última versión) | Todos los flujos E2E ejecutados sin errores |
| RNF-04-03 | El sistema debe funcionar correctamente en Microsoft Edge | Todos los flujos E2E ejecutados sin errores |
| RNF-04-04 | El sistema debe ser compatible con PHP 8.x (versión del servidor XAMPP) | Sin errores de deprecación o incompatibilidad |
| RNF-04-05 | El sistema debe ser compatible con MySQL 8.x | Consultas ejecutadas sin errores de sintaxis |

### RNF-05 — Adaptabilidad / Responsive

| ID | Requisito | Criterio de aceptación |
|---|---|---|
| RNF-05-01 | La tienda debe visualizarse correctamente en escritorio (1920x1080) | Sin desbordamientos ni elementos superpuestos |
| RNF-05-02 | La tienda debe visualizarse correctamente en tablet (768x1024) | Menú y formularios adaptados al tamaño |
| RNF-05-03 | La tienda debe visualizarse correctamente en móvil (375x667) | Navegación accesible, formularios utilizables en pantalla pequeña |
| RNF-05-04 | El menú de categorías debe colapsar correctamente en móvil | Menú hamburguesa funcional en pantallas pequeñas |

### RNF-06 — Disponibilidad

| ID | Requisito | Criterio de aceptación |
|---|---|---|
| RNF-06-01 | El sistema debe estar disponible durante toda la sesión de pruebas sin reinicios | Sin caídas en condiciones normales de uso local |
| RNF-06-02 | El sistema debe recuperarse correctamente tras un reinicio de XAMPP | Los datos persisten en BD y el sistema vuelve a funcionar |

### RNF-07 — Mantenibilidad

| ID | Requisito | Criterio de aceptación |
|---|---|---|
| RNF-07-01 | El código sigue la arquitectura MVC de OpenCart | Controllers en `catalog/controller/`, Models en `catalog/model/` |
| RNF-07-02 | Las pruebas unitarias deben ejecutarse en verde con PHPUnit | `./vendor/bin/phpunit` retorna 0 errores |
| RNF-07-03 | Los archivos de prueba siguen la convención de nomenclatura PHPUnit | Archivos terminan en `Test.php`, métodos en `test*` |

---

## 5. Pruebas de Rendimiento

**Herramienta sugerida:** JMeter (gratuito) o simplemente cronómetro manual para entorno académico.

### 5.1 Prueba de carga básica (Load Test)

**Objetivo:** Verificar que el sistema responde correctamente bajo uso normal.

| Escenario | Usuarios simulados | Duración | Métrica objetivo |
|---|---|---|---|
| Navegación por catálogo | 5 usuarios concurrentes | 2 minutos | Respuesta < 3 seg |
| Login simultáneo | 5 usuarios concurrentes | 1 minuto | Respuesta < 3 seg |
| Búsqueda de productos | 5 usuarios concurrentes | 2 minutos | Respuesta < 2 seg |
| Proceso de checkout | 3 usuarios concurrentes | 2 minutos | Sin errores de sesión |

### 5.2 Procedimiento manual (sin JMeter)

Para el entorno académico con XAMPP local se puede hacer de forma simplificada:

```
1. Abrir 5 pestañas del navegador simultáneamente
2. En cada una ejecutar el flujo E2E-01 (registro y login)
3. Medir el tiempo de respuesta de cada paso con el cronómetro del navegador (F12 → Network)
4. Registrar si alguna pestaña falla o se ralentiza
5. Documentar los tiempos obtenidos
```

---

## 6. Pruebas de Estrés (Stress Testing)

**Objetivo:** Determinar el punto de quiebre del sistema bajo condiciones extremas.

### 6.1 Escenarios de estrés

| ID | Escenario | Condición extrema | Resultado esperado |
|---|---|---|---|
| EST-01 | Múltiples registros simultáneos | 20 registros en paralelo en menos de 10 segundos | El sistema no crea cuentas duplicadas ni colapsa |
| EST-02 | Intentos masivos de login fallidos | 50 intentos fallidos consecutivos al mismo email | El bloqueo `error_attempts` se activa correctamente |
| EST-03 | Búsquedas simultáneas masivas | 20 búsquedas concurrentes con diferentes términos | El sistema responde sin timeout ni error 500 |
| EST-04 | Carrito con muchos productos | Agregar 50 productos distintos al mismo carrito | El carrito calcula totales correctamente sin errores |
| EST-05 | Sesiones concurrentes activas | 15 usuarios autenticados simultáneamente navegando | Las sesiones no se mezclan entre usuarios |
| EST-06 | Carga masiva de catálogo | Buscar en un catálogo con 500+ productos cargados | Los resultados paginan correctamente sin lentitud extrema |

### 6.2 Prueba de Spike (Pico repentino)

**Objetivo:** Verificar el comportamiento ante un aumento brusco y repentino de tráfico.

```
Escenario: El sistema está en uso normal (2 usuarios)
→ De repente 15 usuarios acceden simultáneamente en 5 segundos
→ Se mide si el sistema mantiene respuesta o genera errores
→ Tras el pico, los usuarios bajan a 2 nuevamente
→ Se verifica que el sistema se recupera normalmente
```

### 6.3 Prueba de Resistencia (Endurance)

**Objetivo:** Verificar que el sistema no degrada su rendimiento en uso prolongado.

```
Escenario: 3 usuarios navegan continuamente durante 30 minutos
→ Ejecutan flujos E2E-01, E2E-02 y E2E-03 repetidamente
→ Se mide si los tiempos de respuesta aumentan con el tiempo
→ Se verifica que no haya fugas de memoria (memory leaks) en PHP
→ Se verifica que la BD no acumule locks o conexiones huérfanas
```

---

## 7. Pruebas de Compatibilidad

### 7.1 Compatibilidad de navegadores

| Navegador | Versión | Flujos a ejecutar | Estado |
|---|---|---|---|
| Google Chrome | Última | E2E-01 al E2E-07 | Por ejecutar |
| Mozilla Firefox | Última | E2E-01 al E2E-07 | Por ejecutar |
| Microsoft Edge | Última | E2E-01 al E2E-07 | Por ejecutar |
| Brave | Última | E2E-01, E2E-02 | Por ejecutar |

### 7.2 Compatibilidad de dispositivos / resoluciones

| Dispositivo | Resolución | Flujos a verificar | Aspecto clave |
|---|---|---|---|
| Escritorio | 1920×1080 | Todos | Layout completo, menús visibles |
| Laptop | 1366×768 | E2E-01, E2E-02, E2E-03 | Sin scroll horizontal innecesario |
| Tablet | 768×1024 | E2E-01, E2E-02 | Menú adaptado, formularios usables |
| Móvil | 375×667 | E2E-01, E2E-02 | Menú hamburguesa, botones táctiles |

### 7.3 Compatibilidad de stack

| Componente | Versión probada | Resultado esperado |
|---|---|---|
| PHP | 8.x (XAMPP) | Sin errores de deprecación |
| MySQL | 8.x | Consultas ejecutadas correctamente |
| Apache | 2.4.x | Servidor responde en todos los módulos |
| OpenCart | Versión del repo | Sin errores de inicialización |

---

## 8. Pruebas de Adaptabilidad (Responsive Design)

**Objetivo:** Verificar que la interfaz de OpenCart se adapta correctamente a distintos tamaños de pantalla.

**Herramienta:** Chrome DevTools → Toggle Device Toolbar (F12 → Ctrl+Shift+M)

| ID | Pantalla | Resolución | Qué verificar |
|---|---|---|---|
| ADP-01 | Escritorio | 1920×1080 | Header completo, categorías visibles, sidebar funcional |
| ADP-02 | Laptop pequeña | 1280×720 | Sin desbordamientos horizontales |
| ADP-03 | Tablet landscape | 1024×768 | Menú adaptado, grid de productos correcto |
| ADP-04 | Tablet portrait | 768×1024 | Formularios de registro/login usables |
| ADP-05 | Móvil grande | 414×896 | Menú hamburguesa funcional, botones táctiles grandes |
| ADP-06 | Móvil estándar | 375×667 | Login y registro completables sin zoom |
| ADP-07 | Móvil pequeño | 320×568 | Sin elementos cortados, scroll vertical fluido |

---

## 9. Pruebas de Seguridad

| ID | Escenario | Método | Resultado esperado |
|---|---|---|---|
| SEG-01 | Intento de CSRF en login | Enviar POST sin `login_token` | Sistema rechaza y redirige |
| SEG-02 | Intento de CSRF en registro | Enviar POST sin `register_token` | Sistema rechaza y redirige |
| SEG-03 | Acceso a rutas protegidas sin sesión | Acceder a `account/account` sin login | Redirige a `account/login` |
| SEG-04 | Inyección SQL en campo email del login | `email = ' OR '1'='1` | Sistema rechaza, no expone datos |
| SEG-05 | XSS en campo de búsqueda | `search=<script>alert(1)</script>` | El script no se ejecuta, se escapa correctamente |
| SEG-06 | Reutilización de token de recuperación | Usar el mismo link de reset dos veces | Segundo uso rechazado, token eliminado |
| SEG-07 | Verificar hash en BD | Inspeccionar campo `password` en tabla `customer` | Valor es hash bcrypt, no texto plano |

---

## 10. Pruebas de Regresión

**Objetivo:** Verificar que los cambios en un módulo no rompen la funcionalidad de otros.

| Escenario de regresión | Módulos afectados | Cuándo ejecutar |
|---|---|---|
| Cambio en el modelo `customer.php` | Login, Registro, Checkout | Tras cualquier modificación al modelo |
| Cambio en el sistema de sesiones | Login, Carrito, Checkout | Tras cambios en manejo de sesión |
| Actualización de precios en inventario | Catálogo, Carrito, Checkout | Tras cambios en gestión de precios |
| Cambio en el sistema de categorías | Catálogo, Búsqueda | Tras modificar la jerarquía de categorías |
| Cambio en el proceso de checkout | Carrito, Pago, Inventario | Tras modificar el flujo de pago |

---

## 11. Resumen general del plan

| Tipo de prueba | Cantidad de escenarios | Prioridad | Herramienta |
|---|---|---|---|
| E2E funcional | 7 flujos | Alta | Manual / Navegador |
| Rendimiento (carga) | 4 escenarios | Alta | Manual / JMeter |
| Estrés | 6 escenarios + spike + endurance | Media | Manual / JMeter |
| Compatibilidad navegadores | 4 navegadores | Alta | Chrome DevTools |
| Compatibilidad dispositivos | 4 resoluciones | Alta | Chrome DevTools |
| Adaptabilidad responsive | 7 resoluciones | Media | Chrome DevTools |
| Seguridad | 7 escenarios | Alta | Manual |
| Regresión | 5 escenarios | Media | PHPUnit + Manual |
| **RNF documentados** | **28 requisitos** | — | — |

---

## 12. Criterios de entrada y salida

### Criterios de entrada (para iniciar las pruebas)
- El sistema está desplegado y accesible en `https://github.com/DanLAQP/QA-OpenCart-Testing/actions/runs/`
- La base de datos está inicializada con datos de prueba
- Las pruebas unitarias (PHPUnit) ejecutan en verde
- Todos los módulos del equipo están integrados en el repositorio

### Criterios de salida (para dar por finalizadas las pruebas)
- Todos los flujos E2E ejecutados y documentados
- Los RNF críticos (seguridad y rendimiento) verificados
- Los defectos encontrados están registrados en GitHub Issues
- El catálogo de pruebas está actualizado con los resultados obtenidos