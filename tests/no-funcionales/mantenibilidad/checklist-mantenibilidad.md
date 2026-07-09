# Checklist de Mantenibilidad

Evaluación de qué tan fácil es entender, modificar y verificar el código del proyecto sin
introducir regresiones — medida principalmente a través de cobertura de pruebas existente y el
estado de las herramientas de análisis estático configuradas en CI.

**Ambiente evaluado**: código fuente en `opencart/system/library/` (clases de negocio
`*Manager.php` cubiertas por [`tests/unitarias/`](../../unitarias/)) y configuración de CI del repo.
**Fecha**: 2026-07-09
**Fuente de datos**: [`reports/coverage/`](../../../reports/coverage/dashboard.html) (reporte
HTML de PHPUnit ya generado en el repo) — no se instalaron herramientas nuevas para este ciclo,
ver sección "Limitaciones" al final.

---

## 1. Cobertura de pruebas (mantenibilidad vía red de seguridad para refactors)

Cobertura global del proyecto según el último reporte HTML disponible:

| Métrica | Cobertura | Detalle |
|---|---|---|
| Líneas | **27.48%** | 554 / 2016 |
| Métodos | **28.45%** | 101 / 355 |
| Clases | **7.32%** | 3 / 41 |

### Cobertura por módulo (clases `*Manager.php`, una por módulo funcional)

| Módulo | Clase | Cobertura (líneas) | Cobertura (métodos) | Estado |
|---|---|---|---|---|
| Login y Registro | `AuthenticationManager.php` | 89.04% | 76.92% | 🟢 Buena |
| Carrito de compras | `CartManager.php` | 94.44% | 88.24% | 🟢 Buena |
| Catálogo y Búsqueda | `CatalogManager.php` | 100.00% | 100.00% | 🟢 Excelente |
| Checkout y Pago | `CheckoutManager.php` | 100.00% | 100.00% | 🟢 Excelente |
| Gestión de Inventario | `InventoryManager.php` | 100.00% | 100.00% | 🟢 Excelente |
| Reseñas | `ReviewManager.php` | 86.30% | 72.22% | 🟢 Buena |

### Cobertura de infraestructura del framework (fuera del alcance de las 6 clases de negocio)

| Carpeta | Cobertura | Estado |
|---|---|---|
| `cache/` | 0.00% | 🔴 Sin pruebas |
| `cart/` | 0.00% | 🔴 Sin pruebas |
| `db/` | 0.00% | 🔴 Sin pruebas |
| `mail/` | 0.00% | 🔴 Sin pruebas |
| `session/` | 0.00% | 🔴 Sin pruebas |
| `template/` | 0.00% | 🔴 Sin pruebas |

**Lectura de estos números**: las 6 clases de negocio (`*Manager.php`), que son las que
concentran la lógica específica de cada módulo funcional del proyecto, tienen cobertura buena a
excelente (86%–100%). El **27.48% global** se explica porque el `<source>` de `phpunit.xml`
incluye *todo* `opencart/system/library/` (cache, sesiones, plantillas, base de datos, correo,
etc.), que son componentes de infraestructura del framework base de OpenCart, no código propio
del equipo, y no fueron objetivo de las pruebas unitarias de este proyecto. Ver
[INC-MANT-001](incident-reports/INC-MANT-001-cobertura-global-enganosa.md) para el detalle de
por qué esto puede ser una métrica engañosa si se reporta sin este contexto.

## 2. Complejidad ciclomática y calidad de código estático

| Verificación | Resultado | Evidencia |
|---|---|---|
| ¿Existe una herramienta de análisis estático configurada? | ✅ Sí — PHPStan, referenciado en `.github/workflows/Lint-4.yml` | `php tools/phpstan.phar analyze --no-progress` |
| ¿La herramienta es ejecutable localmente? | ❌ No — ver [INC-MANT-002](incident-reports/INC-MANT-002-pipeline-lint-roto.md) | `tools/phpstan.phar` no existe en el repo ni se descarga en ningún paso del workflow |
| ¿Existe un estándar de estilo de código (PSR-12, etc.) verificado en CI? | ⚠️ Parcialmente — `php-cs-fixer` referenciado, mismo problema que PHPStan | `tools/php-cs-fixer.phar` tampoco existe en el repo |
| ¿El código de los 6 managers sigue una estructura consistente (nomenclatura, inyección de dependencias)? | ✅ Sí | Los 6 archivos `*Manager.php` siguen el mismo patrón: constructor con `Registry`, métodos con nombres consistentes (`validate*`, `get*`, `is*`) |

## 3. Duplicación de código

| Verificación | Resultado | Evidencia |
|---|---|---|
| ¿Hay una herramienta de detección de duplicados configurada (PHPCPD, PHPMD)? | ❌ No configurada | No aparece en `composer.json` ni en ningún workflow |
| Revisión manual de los 6 managers — patrones repetidos evidentes | ⏳ Pendiente de revisión manual detallada | Los 6 archivos comparten un patrón de validación similar (`quantity <= 0` → error), candidato a extraerse a un helper común si se detecta duplicación real |

## 4. Documentación del código

| Verificación | Resultado | Evidencia |
|---|---|---|
| ¿Los métodos públicos de los 6 managers tienen PHPDoc? | ⏳ Pendiente de revisión | Verificar manualmente en `opencart/system/library/*.php` |
| ¿Existe documentación de arquitectura del proyecto? | ✅ Sí | `docs/diagramas/`, `docs/plan-pruebas/`, este mismo árbol de `tests/` documentado con READMEs |

## 5. Gestión de dependencias

| Verificación | Resultado | Evidencia |
|---|---|---|
| ¿Las dependencias de Composer están fijadas a versiones específicas o rangos razonables? | ✅ Sí | `composer.json`: `twig/twig: ^3.24.0`, `scssphp/scssphp: ^2.1.0` (rangos con caret, razonable) |
| ¿Hay un `composer.lock` commiteado para reproducibilidad? | ⏳ Pendiente de verificar | Confirmar existencia y que esté en control de versiones |
| ¿Se ejecuta `composer audit` en CI para detectar CVEs? | ❌ No configurado | No aparece en ningún workflow (mismo hallazgo que en el checklist de Seguridad, A06-02) |

---

## Resumen de hallazgos

| ID | Severidad | Resumen |
|---|---|---|
| [INC-MANT-002](incident-reports/INC-MANT-002-pipeline-lint-roto.md) | Alta | El workflow de CI `Lint-4.yml` referencia `tools/phpstan.phar` y `tools/php-cs-fixer.phar`, que no existen en el repo ni se descargan en ningún paso — el pipeline de calidad de código nunca ha corrido exitosamente |
| [INC-MANT-001](incident-reports/INC-MANT-001-cobertura-global-enganosa.md) | Media | La cobertura global reportada (27.48%) mezcla código propio bien probado (86%–100%) con infraestructura del framework sin pruebas (0%), lo que puede llevar a conclusiones erróneas si se cita el número sin contexto |

## Limitaciones de este ciclo

No se instalaron herramientas nuevas de análisis estático (PHPStan, PHPCPD, PHPMD) ni se
regeneró el reporte de cobertura — se trabajó únicamente con el reporte HTML ya presente en
`reports/coverage/` del repo. Si se requiere un análisis más profundo (complejidad ciclomática
por método, duplicación exacta de líneas, PHPDoc faltante), el siguiente paso sería:

```bash
composer require --dev phpstan/phpstan phpmd/phpmd sebastian/phpcpd
vendor/bin/phpstan analyze opencart/system/library --level=5
vendor/bin/phpmd opencart/system/library text cleancode,codesize,design
vendor/bin/phpcpd opencart/system/library
```
