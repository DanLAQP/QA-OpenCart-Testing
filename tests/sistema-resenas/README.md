# Pruebas de Integración — Sistema de Reseñas

Archivo de pruebas: `SistemaResenasIntegrationTest.php`

## Objetivo

Validar la persistencia, publicación y conteo de reseñas de productos en OpenCart, asegurando que solo las reseñas aprobadas impacten la visualización del frontend.

## Componentes / tablas integradas

| Componente | Tabla OpenCart |
|---|---|
| Productos | `oc_product` |
| Reseñas | `oc_review` |

## Precondiciones

- Debe existir al menos un producto activo.
- La tabla de reseñas debe estar disponible en la instalación de pruebas.

## Variables de entorno

| Variable | Valor por defecto |
|---|---|
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `opencart` |
| `DB_USER` | `opencart` |
| `DB_PASS` | `opencart` |
| `DB_PREFIX` | `oc_` |

## Ejecución

```bash
vendor/bin/phpunit tests/integracion/sistema-resenas/SistemaResenasIntegrationTest.php
```

## Casos de prueba

| ID | Método | Descripción | Técnica | Resultado esperado |
|---|---|---|---|---|
| CI-REV-01 | `testRegistroDeResenaValidaSePersiste` | Registra una reseña válida. | Flujo integrado | La reseña queda persistida con producto y rating correctos. |
| CI-REV-02 | `testSoloResenasAprobadasSeListanEnFrontend` | Mezcla reseñas aprobadas y pendientes. | Partición de equivalencia | Solo se listan las aprobadas. |
| CI-REV-03 | `testConteoDeResenasConsideraSoloAprobadas` | Valida el conteo visible de reseñas. | Flujo integrado | El total cuenta solo reseñas aprobadas. |
| CI-REV-04 | `testAutorDemasiadoCortoEsRechazado` | Autor con longitud inválida. | Valor límite | La validación rechaza el autor. |
| CI-REV-05 | `testRatingFueraDeRangoEsRechazado` | Rating fuera del rango permitido. | Valor límite | La validación rechaza el rating. |

## Aislamiento

Todas las pruebas usan transacciones con rollback para mantener intacta la base de datos.

## Relación con CI/CD

Esta suite puede correr junto con catálogo y detalle de producto para validar el impacto real de las reseñas sobre la experiencia visible del usuario.
