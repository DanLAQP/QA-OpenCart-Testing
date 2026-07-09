# Documento de Plan de Pruebas: Aceptación

**Sistema Bajo Prueba (SUT):** OpenCart 4.x
**Nivel de Prueba:** Pruebas de Aceptación (User Acceptance Testing — UAT)
**Fecha:** Julio 2026

---

## 1. Introducción

### 1.1 Alcance

Este plan define los criterios de aceptación del negocio para los 6 módulos funcionales del
proyecto: **Login y Registro**, **Catálogo y Búsqueda**, **Carrito de Compras**, **Checkout y
Pago**, **Gestión de Inventario** y **Sistema de Reseñas**. A diferencia de las pruebas
unitarias, de integración y de sistema (que verifican *cómo* funciona el software
técnicamente), las pruebas de aceptación verifican que el sistema **cumple lo que el negocio
pidió**, desde la perspectiva de un usuario final o de un cliente/Product Owner que aprueba (o
rechaza) la entrega.

### 1.2 Diferencia con otros niveles de prueba ya cubiertos

| Nivel | Pregunta que responde | Quién lo valida |
|---|---|---|
| Unitarias | ¿La función/clase hace lo que debería, aislada? | El equipo de desarrollo |
| Integración | ¿Los módulos se comunican correctamente entre sí y con la BD? | El equipo de QA |
| Sistema | ¿El sistema completo (funcional + no funcional) cumple los requisitos técnicos? | El equipo de QA |
| **Aceptación** | **¿El sistema resuelve el problema de negocio tal como lo pidió el cliente?** | **El cliente / usuario final / Product Owner** |

### 1.3 Referencias

- Requisitos funcionales por módulo: [`docs/Requisitos-funcionales/`](../../docs/Requisitos-funcionales/)
- Plan de Pruebas de Sistema: [`tests/sistema/README.md`](../sistema/README.md)
- Norma ISO/IEC/IEEE 29119.

### 1.4 Glosario

- **UAT:** User Acceptance Testing (Pruebas de Aceptación del Usuario).
- **Criterio de Aceptación:** Condición específica y verificable que debe cumplirse para que
  una historia de usuario se considere terminada y aceptada por el negocio.
- **Historia de Usuario:** Descripción breve de una funcionalidad desde la perspectiva de
  quien la usa, en el formato *"Como [rol], quiero [acción], para [beneficio]"*.

---

## 2. Contexto de las pruebas

### 2.1 Módulos cubiertos

1. [Login y Registro](1-Login-y-Registro.md)
2. [Catálogo y Búsqueda](2-Catalogo-y-Busqueda.md)
3. [Carrito de Compras](3-Carrito-de-Compras.md)
4. [Checkout y Pago](4-Checkout-y-Pago.md)
5. [Gestión de Inventario](5-Gestion-de-Inventario.md)
6. [Sistema de Reseñas](6-Sistema-de-Resenas.md)

### 2.2 Ambiente de pruebas

Igual que en las pruebas de sistema y no funcionales, las pruebas de aceptación se ejecutan
contra `upload/` (no `opencart/`, que tiene plantillas desincronizadas y no renderiza HTML
real). Ver instrucciones de instalación en
[`tests/no-funcionales/k6/README.md`](../no-funcionales/k6/README.md), sección 0.

### 2.3 Rol del "cliente" en este proyecto académico

En un proyecto real, las pruebas de aceptación las ejecuta o aprueba el cliente/Product Owner.
En este contexto académico, el rol de cliente lo asume el docente o el equipo de QA actuando en
representación de un usuario final, validando cada criterio de aceptación de forma manual sobre
el ambiente `upload/` y registrando el resultado en la tabla de **Registro de Ejecución** al
final de cada documento de módulo.

### 2.4 Técnica de diseño

Cada módulo se documenta como un conjunto de **historias de usuario**, cada una con sus
**criterios de aceptación** en formato Gherkin (`Dado / Cuando / Entonces`), trazables a los
requisitos funcionales técnicos ya documentados en `docs/Requisitos-funcionales/`.

### 2.5 Criterio de finalización

Se considera finalizado un módulo cuando el 100% de sus criterios de aceptación de prioridad
alta han sido ejecutados y marcados como **Cumple** o **No Cumple** con evidencia, y cualquier
hallazgo de **No Cumple** ha sido documentado como incidente.

---

## 3. Resumen de historias de usuario por módulo

| Módulo | Historias de usuario | Prioridad predominante |
|---|---|---|
| Login y Registro | 5 | Alta |
| Catálogo y Búsqueda | 5 | Alta |
| Carrito de Compras | 5 | Alta |
| Checkout y Pago | 5 | Alta |
| Gestión de Inventario | 5 | Alta |
| Sistema de Reseñas | 4 | Media |

**Total: 29 historias de usuario** con sus respectivos criterios de aceptación.

---

## 4. Reportes de incidente

Cualquier criterio de aceptación que no se cumpla se documenta en
[`incident-reports/`](incident-reports/) usando
[`incident-report-template.md`](incident-reports/incident-report-template.md).
