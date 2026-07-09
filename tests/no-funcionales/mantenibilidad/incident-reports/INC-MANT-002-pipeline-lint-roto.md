# Reporte de Incidente — Mantenibilidad

**ID**: INC-MANT-002
**Fecha**: 2026-07-09
**Autor**: Revisión de `.github/workflows/Lint-4.yml`
**Módulo afectado**: Pipeline de CI (análisis estático de código)

## Resultado Esperado

El workflow `Lint-4.yml` debería ejecutar exitosamente PHPStan y php-cs-fixer en cada push/PR,
proveyendo una señal automática de calidad de código (errores de tipos, código muerto, estilo)
antes de fusionar cambios.

## Resultado Real

El workflow referencia dos binarios que no existen en el repositorio ni se descargan en ningún
paso previo:

```yaml
- name: Check Code Style
  if: matrix.php == '8.3'
  run: |
    php tools/php-cs-fixer.phar fix --dry-run --diff --ansi || true
    php tools/php-cs-fixer.phar fix --dry-run --format=checkstyle | cs2pr

- name: Run PHPStan
  run: php tools/phpstan.phar analyze --no-progress
```

Se verificó que:
- No existe una carpeta `tools/` en el working directory local del proyecto.
- `git ls-tree -r HEAD --name-only | grep tools/` no devuelve ningún archivo `.phar`.
- No hay ningún paso anterior en el mismo workflow (ni en otros) que haga `wget`, `curl`, o
  `composer require` para materializar `tools/phpstan.phar` o `tools/php-cs-fixer.phar` antes
  de invocarlos.

## Evidencia

```bash
$ find . -maxdepth 1 -iname "tools"
# (sin resultados)

$ git log --all --oneline -- tools/
# (sin resultados — la carpeta nunca existió en el historial)
```

## Severidad

**Alta** — el job `Run PHPStan` (y presumiblemente `Check Code Style`) debe estar fallando en
cada ejecución de CI con un error de "archivo no encontrado", lo que significa que **este
pipeline de calidad de código nunca ha aportado valor real**: o bien el equipo lo ignora
sistemáticamente (badge rojo permanente), o GitHub Actions nunca disparó este workflow
correctamente por otra razón que no se investigó en este ciclo.

## Impacto

Sin análisis estático funcional, errores de tipos, código muerto, y violaciones de estilo
pueden colarse a `main` sin ninguna verificación automática, incrementando el costo de
mantenimiento a largo plazo (bugs que un linter habría detectado en segundos, ahora se
descubren en producción o en code review manual).

## Causa raíz

El workflow fue escrito asumiendo que `tools/phpstan.phar` y `tools/php-cs-fixer.phar` ya
estarían presentes (posiblemente commiteados manualmente en algún punto, o descargados por un
paso que se eliminó después), pero esa expectativa no se cumple en el estado actual del
repositorio.

## Recomendación

1. Agregar un paso explícito de descarga/instalación antes de usar las herramientas, por ejemplo:
   ```yaml
   - name: Install PHPStan
     run: |
       composer require --dev phpstan/phpstan
       composer require --dev friendsofphp/php-cs-fixer
   ```
   y reemplazar `php tools/phpstan.phar analyze` por `vendor/bin/phpstan analyze`.
2. Alternativamente, si se prefiere mantener el patrón de `.phar` standalone, agregar un paso
   que los descargue desde sus releases oficiales y los guarde en `tools/` antes de ejecutarlos.
3. Verificar en el historial de GitHub Actions (pestaña "Actions" del repo) si este workflow
   efectivamente ha estado fallando, para confirmar el impacto real y desde cuándo.
4. Una vez corregido, considerar que el job falle el build (`if: matrix.php == '8.3'` sin
   `|| true`) para que realmente bloquee merges con problemas de estilo/tipos, en vez de solo
   reportar.
