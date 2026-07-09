# Pruebas de Integración — Catálogo y Búsqueda

Archivo de pruebas: [`CatalogoBusquedaIntegrationTest.php`](./CatalogoBusquedaIntegrationTest.php)

## Objetivo

Verificar la interacción real entre categorías, productos, búsqueda, detalle y comparación dentro del catálogo de OpenCart, usando datos persistidos en base de datos.

## Componentes / tablas integradas

| Componente | Tabla OpenCart |
|---|---|
| Categorías | `oc_category`, `oc_category_description` |
| Productos | `oc_product`, `oc_product_description` |
| Relación categoría-producto | `oc_product_to_category` |
| Relacionados | `oc_product_related` |

## Precondiciones

- Debe existir al menos una categoría activa.
- Debe existir al menos un producto visible.
- Para la comparación se requieren al menos 5 productos visibles.

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
vendor/bin/phpunit tests/integracion/catalogo-busqueda/CatalogoBusquedaIntegrationTest.php
```

## Casos de prueba

| ID | Método | Descripción | Técnica | Resultado esperado |
|---|---|---|---|---|
| CI-CAT-01 | `testListaProductosDeUnaCategoriaActiva` | Lista productos de una categoría activa. | Flujo integrado | Se obtienen productos visibles de la categoría. |
| CI-CAT-02 | `testBusquedaPorNombreRetornaCoincidenciasReales` | Busca productos por coincidencia real de nombre. | Partición de equivalencia | El producto buscado aparece en resultados. |
| CI-CAT-03 | `testBusquedaSinCoincidenciasRetornaListaVacia` | Busca con término inexistente. | Partición de equivalencia | La lista de resultados queda vacía. |
| CI-CAT-04 | `testDetalleDeProductoExponeFabricanteYRelacionadosCuandoExisten` | Valida detalle de producto y metadatos asociados. | Flujo integrado | El detalle contiene producto, fabricante y conteo de relacionados. |
| CI-CAT-05 | `testComparacionConservaMaximoDeCuatroProductos` | Limita la lista de comparación a 4 productos. | Valor límite | La comparación conserva solo los 4 más recientes. |

## Aislamiento

Cada prueba corre dentro de una transacción y se revierte al finalizar para no contaminar la base de datos.

## Relación con CI/CD

Esta suite debe ejecutarse después de instalar OpenCart y preparar la base de datos de prueba. Complementa los smoke tests del frontend con validación funcional del catálogo.
