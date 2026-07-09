# Pruebas No Funcionales

Pruebas no funcionales sobre OpenCart local (`upload/`), organizadas por tipo. A diferencia de
las pruebas funcionales (unitarias, integración, sistema, aceptación), que verifican **qué tan
correcto** es el comportamiento, estas pruebas verifican atributos de calidad transversales:
rendimiento, seguridad, usabilidad, responsive, disponibilidad y mantenibilidad.

## Ambiente común: `upload/`, no `opencart/`

Este proyecto tiene dos copias del código de OpenCart: `opencart/` y `upload/`. **Todas las
pruebas de esta carpeta apuntan a `upload/`** — es la única de las dos con las plantillas
`.twig` correctas y sincronizadas con el motor Twig del framework (`opencart/` tiene plantillas
`.html` desincronizadas y nunca renderiza HTML real). Ver la sección 0 de
[`k6/README.md`](k6/README.md) para los pasos completos de instalación de `upload/`.

## Categorías

| Categoría | Carpeta | Estado | Herramienta |
|---|---|---|---|
| **Rendimiento / Carga** | [`k6/`](k6/README.md) | ✅ Completo | k6 |
| **Seguridad** | [`seguridad/`](seguridad/checklist-owasp.md) | ✅ Completo | Checklist OWASP Top 10 + script Node |
| **Disponibilidad** | [`disponibilidad/`](disponibilidad/checklist-disponibilidad.md) | ✅ Completo | Prueba de recuperación ante caída (MySQL) |
| **Responsive** | [`responsive/`](responsive/checklist-responsive.md) | ✅ Completo | Capturas con Responsively App |
| **Mantenibilidad** | [`mantenibilidad/`](mantenibilidad/checklist-mantenibilidad.md) | ✅ Completo | Cobertura PHPUnit existente + revisión de CI |
| **Usabilidad** | [`usabilidad/`](usabilidad/checklist-usabilidad.md) | ✅ Completo | Evaluación heurística (Nielsen) |

## Reportes de incidente

Cada categoría tiene su propia subcarpeta `incident-reports/` con hallazgos documentados según
la plantilla correspondiente a ese tipo de prueba.

## Reportes de resultados

Los resultados consolidados (baselines, corridas de referencia) se guardan en
[`reports/`](reports/).
