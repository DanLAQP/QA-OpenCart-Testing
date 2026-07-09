# Pruebas de Integración — Sistema de Reseñas

Archivo de pruebas: [`SistemaResenasIntegrationTest.php`](./SistemaResenasIntegrationTest.php)

## Objetivo

Verificar la **interacción real entre las reseñas y el catálogo de productos** de
OpenCart, usando la base de datos real (no mocks). Se valida que una reseña válida
se persista en `oc_review`, que solo las reseñas aprobadas se listen y se cuenten
en el frontend, y que las reglas de validación de autor y rating rechacen las
entradas fuera de rango.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Catálogo | `oc_product` |
| Reseñas | `oc_review` |

## Estrategia de aislamiento

Cada prueba se ejecuta dentro de una **transacción** (`beginTransaction()` en `setUp()`)
que se **revierte** (`rollBack()` en `tearDown()`). Así la base de datos queda intacta
tras la ejecución y las pruebas son repetibles e independientes.

## Configuración (variables de entorno)

| Variable | Valor por defecto |
|----------|-------------------|
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `opencart` |
| `DB_USER` | `opencart` |
| `DB_PASS` | `opencart` |
| `DB_PREFIX` | `oc_` |

## Ejecución

```bash
phpunit --no-configuration tests/sistema-resenas/SistemaResenasIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-REV-01** | `testRegistroDeResenaValidaSePersiste` | Registrar una reseña válida sobre un producto activo la persiste en `oc_review`. | **Partición de equivalencia** (clase válida de datos de reseña) | `review_id > 0`, `product_id` correcto y `rating = 5`. |
| **CI-REV-02** | `testSoloResenasAprobadasSeListanEnFrontend` | Solo las reseñas con `status = 1` se muestran en el frontend. | **Prueba semántica** (regla de negocio: no publicar reseñas pendientes) | De 2 reseñas (aprobada + pendiente) se lista 1: la aprobada. |
| **CI-REV-03** | `testConteoDeResenasConsideraSoloAprobadas` | El conteo de reseñas considera únicamente las aprobadas. | **Prueba semántica** (regla de negocio: contar solo aprobadas) | De 3 reseñas (2 aprobadas + 1 pendiente) el conteo es 2. |
| **CI-REV-04** | `testAutorDemasiadoCortoEsRechazado` | Registrar una reseña con autor de menos de 3 caracteres. | **Análisis de valores límite** (frontera inferior del autor) | Se lanza `InvalidArgumentException` («El autor debe tener entre 3 y 25 caracteres.»). |
| **CI-REV-05** | `testRatingFueraDeRangoEsRechazado` | Registrar una reseña con rating fuera del rango 1–5. | **Análisis de valores límite** (frontera superior no válida: `rating = 6`) | Se lanza `InvalidArgumentException` («El rating debe estar entre 1 y 5.»). |

---

## Reglas de validación (registrarResena)

| Campo | Regla | Rango válido |
|-------|-------|--------------|
| `author` | Longitud | 3 – 25 caracteres |
| `text` | Longitud | 25 – 1000 caracteres |
| `rating` | Valor entero | 1 – 5 |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Partición de equivalencia | CI-REV-01 |
| Prueba semántica | CI-REV-02, CI-REV-03 |
| Análisis de valores límite | CI-REV-04, CI-REV-05 |
| **Total** | **5 casos** |
