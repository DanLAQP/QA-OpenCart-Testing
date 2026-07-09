# Reporte de Incidente — Mantenibilidad

**ID**: INC-MANT-001
**Fecha**: 2026-07-09
**Autor**: Análisis del reporte de cobertura existente (`reports/coverage/`)
**Módulo afectado**: Configuración de `phpunit.xml` / reporte de cobertura

## Resultado Esperado

Un reporte de cobertura debería medir el código que el equipo realmente escribió y es
responsable de mantener, para que el número sea una señal útil de qué tan protegido está ese
código ante regresiones.

## Resultado Real

El `<source>` de `phpunit.xml` incluye **todo** `opencart/system/library/`:

```xml
<source>
    <include>
        <directory suffix=".php">opencart/system/library</directory>
    </include>
</source>
```

Esa carpeta contiene tanto las 6 clases de negocio del proyecto (`AuthenticationManager`,
`CartManager`, `CatalogManager`, `CheckoutManager`, `InventoryManager`, `ReviewManager` — con
cobertura real de 86% a 100%) como clases de infraestructura del framework base de OpenCart
(`cache.php`, `db.php`, `mail.php`, `session.php`, `template.php`, carpetas `cart/`, `db/`,
etc. — con 0% de cobertura, porque nunca fueron objetivo de las pruebas unitarias del
proyecto).

El resultado es un **27.48%** de cobertura global, que en una lectura rápida (ej. un badge en
el README, o una revisión superficial) sugiere un proyecto pobremente probado, cuando en
realidad **el código propio del equipo (los 6 managers) tiene cobertura buena a excelente**.

## Evidencia

```
reports/coverage/dashboard.html → Total: 27.48% líneas (554/2016), 28.45% métodos (101/355)

Por clase de negocio:
AuthenticationManager.php → 89.04% líneas / 76.92% métodos
CartManager.php           → 94.44% líneas / 88.24% métodos
CatalogManager.php        → 100.00% líneas / 100.00% métodos
CheckoutManager.php       → 100.00% líneas / 100.00% métodos
InventoryManager.php      → 100.00% líneas / 100.00% métodos
ReviewManager.php         → 86.30% líneas / 72.22% métodos

Por infraestructura del framework:
cache/, cart/, db/, mail/, session/, template/ → 0.00% cada una
```

## Severidad

**Media** — no es un defecto del código en sí, pero es un problema de **comunicación de
métricas**: puede llevar a decisiones equivocadas (ej. "necesitamos escribir muchas más
pruebas" cuando en realidad el código relevante ya está bien cubierto, o pasar por alto que la
infraestructura del framework —que si bien no es código propio, si falla afecta a todo el
sistema— no tiene ninguna red de seguridad de pruebas).

## Impacto

Si este número se reporta sin contexto (por ejemplo, en un dashboard de calidad o en la
documentación pública del repo), un stakeholder no técnico podría subestimar la calidad real
del código de negocio, o el equipo podría invertir esfuerzo en aumentar un número que no refleja
el riesgo real.

## Causa raíz

Configuración de `phpunit.xml` que incluye toda la carpeta `opencart/system/library` sin
distinguir entre código de negocio propio y código de infraestructura heredado del framework.

## Recomendación

1. Separar el reporte de cobertura en dos vistas: una para las 6 clases de negocio (código
   propio) y otra para el resto de `system/library` (infraestructura), o excluir esta última
   del `<source>` si no es intención del equipo probarla.
2. Si se decide mantener el scope actual, documentar explícitamente en el README de
   `tests/unitarias/` que el "27.48%" es cobertura de *todo el árbol incluido*, no solo del
   código de negocio, para evitar interpretaciones erróneas.
3. Considerar agregar un umbral mínimo de cobertura (`coverage.min`) específicamente sobre las
   6 clases de negocio en CI, ya que esas sí son responsabilidad directa del equipo.
