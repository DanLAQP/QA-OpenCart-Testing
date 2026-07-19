# Pruebas de calidad — OpenCart

Este directorio reúne la estrategia de aseguramiento de calidad del proyecto **QA OpenCart Testing**. Incluye pruebas unitarias, de integración, de sistema, de aceptación y no funcionales, junto con sus planes, casos, evidencias y reportes de incidentes.

> **Sistema bajo prueba:** OpenCart 4.x  
> **Código utilizado para las pruebas desplegadas:** `upload/`  
> **Tecnologías principales:** PHP 8.1+, PHPUnit 11, MySQL, k6 y GitHub Actions

## Tipos de pruebas

| Nivel | Ubicación | Propósito | Ejecución |
|---|---|---|---|
| Unitarias | [`unitarias/`](unitarias/README.md) | Validar clases y reglas de negocio de forma aislada mediante mocks | Automatizada con PHPUnit |
| Integración | [`integracion/`](integracion/README.md) | Comprobar la comunicación entre módulos, persistencia y base de datos | Automatizada con PHPUnit y MySQL |
| Sistema | [`sistema/`](sistema/README.md) | Validar flujos completos E2E y requisitos técnicos del sistema | Manual y automatizada |
| Aceptación | [`aceptacion/`](aceptacion/README.md) | Verificar que el sistema satisface los criterios del negocio y del usuario | Manual, con criterios Gherkin |
| No funcionales | [`no-funcionales/`](no-funcionales/README.md) | Evaluar rendimiento, seguridad, disponibilidad, usabilidad y mantenibilidad | k6, checklists y revisión técnica |

## Módulos funcionales cubiertos

1. Login y registro.
2. Catálogo y búsqueda.
3. Carrito de compras.
4. Checkout y pago.
5. Gestión de inventario.
6. Sistema de reseñas.

Las pruebas de integración también incluyen dominios transversales como clientes y autenticación, pagos, pedidos, envíos, totales y descuentos.

## Estructura general

```text
tests/
├── README.md
├── BaseTestCase.php
├── bootstrap.php
├── unitarias/
├── integracion/
├── sistema/
├── aceptacion/
└── no-funcionales/
    ├── k6/
    ├── seguridad/
    ├── disponibilidad/
    ├── responsive/
    ├── mantenibilidad/
    ├── usabilidad/
    └── reports/
```

## Requisitos

- PHP 8.1 o superior.
- Composer.
- PHPUnit 11.
- MySQL para las suites que requieren persistencia real.
- k6 para las pruebas de rendimiento y carga.
- Una instalación funcional de OpenCart basada en `upload/`.

## Preparación del proyecto

Desde la raíz del repositorio:

```bash
composer install
```

La configuración principal de PHPUnit se encuentra en [`phpunit.xml`](../phpunit.xml) y el entorno se inicializa mediante [`bootstrap.php`](bootstrap.php).

## Ejecución

### Pruebas unitarias

```bash
vendor/bin/phpunit --configuration phpunit.xml
vendor/bin/phpunit tests/unitarias
vendor/bin/phpunit tests/unitarias/LoginAndRegisterTest.php
```

### Pruebas de integración

Pueden requerir una base de datos MySQL preparada y estas variables de entorno:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `DB_PREFIX`

```bash
vendor/bin/phpunit tests/integracion
```

Consulta [la guía de integración](integracion/README.md) antes de ejecutar suites que modifiquen datos.

### Pruebas de sistema y aceptación

Se ejecutan sobre una instalación funcional de `upload/`:

- [Plan de pruebas de sistema](sistema/README.md)
- [Plan de pruebas de aceptación](aceptacion/README.md)

Cada escenario debe registrar el resultado obtenido y adjuntar evidencia cuando corresponda. Los incumplimientos deben documentarse mediante el formato de incidente de su categoría.

### Pruebas no funcionales

| Categoría | Documento |
|---|---|
| Rendimiento y carga | [Guía de k6](no-funcionales/k6/README.md) |
| Seguridad | [Checklist OWASP](no-funcionales/seguridad/checklist-owasp.md) |
| Disponibilidad | [Checklist de disponibilidad](no-funcionales/disponibilidad/checklist-disponibilidad.md) |
| Responsive | [Checklist responsive](no-funcionales/responsive/checklist-responsive.md) |
| Mantenibilidad | [Checklist de mantenibilidad](no-funcionales/mantenibilidad/checklist-mantenibilidad.md) |
| Usabilidad | [Checklist de usabilidad](no-funcionales/usabilidad/checklist-usabilidad.md) |
| Resultados consolidados | [Reportes](no-funcionales/reports/) |

## Cobertura esperada

La cobertura del proyecto se analiza en cinco dimensiones:

1. **Estructural:** existen suites para los módulos prioritarios.
2. **Funcional:** se validan flujos principales, errores, límites y reglas de negocio.
3. **Transversal:** se comprueban procesos que atraviesan varios módulos.
4. **Código y datos reales:** las pruebas relevantes interactúan con la implementación y persistencia del proyecto.
5. **CI/CD:** las suites automatizadas se ejecutan y conservan sus resultados en el pipeline.

El reporte HTML de cobertura configurado por PHPUnit se genera en:

```text
reports/coverage/index.html
```

## Registro de resultados e incidentes

Los resultados deben indicar como mínimo:

- identificador del caso;
- fecha y ambiente de ejecución;
- datos utilizados;
- resultado esperado;
- resultado obtenido;
- estado: aprobado, fallido o bloqueado;
- evidencia asociada;
- incidente relacionado, cuando exista.

## Buenas prácticas para contribuir

- Ejecutar las suites afectadas antes de enviar cambios.
- Mantener archivos de prueba con el sufijo `Test.php`.
- Nombrar los métodos de prueba con el prefijo `test`.
- Seguir el patrón Arrange–Act–Assert.
- No incluir credenciales ni datos sensibles.
- Actualizar la documentación y las evidencias cuando cambie un escenario.
- Registrar como incidente cualquier incumplimiento reproducible.

## Documentación relacionada

- [Requisitos funcionales](../docs/Requisitos-funcionales/)
- [Resultados de pruebas funcionales](../docs/Resultado%20de%20pruebas%20funcionales/)
- [Configuración de PHPUnit](../phpunit.xml)
- [Repositorio QA OpenCart Testing](https://github.com/DanLAQP/QA-OpenCart-Testing)
