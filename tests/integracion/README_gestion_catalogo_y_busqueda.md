# Pruebas de Integración — Catálogo y Búsqueda

Archivo de pruebas: [`CatalogoBusquedaIntegrationTest.php`](./CatalogoBusquedaIntegrationTest.php)

## Objetivo

Verificar la **interacción real entre los componentes del catálogo** de OpenCart,
utilizando la base de datos real (sin mocks). Se valida la integración entre
categorías, productos, búsqueda, detalle de productos y comparación, asegurando
que las consultas recuperen información consistente y que las reglas de negocio
del catálogo se cumplan correctamente.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Categorías | `oc_category`, `oc_category_description` |
| Productos | `oc_product`, `oc_product_description` |
| Relación categoría-producto | `oc_product_to_category` |
| Productos relacionados | `oc_product_related` |

## Estrategia de aislamiento

Cada prueba se ejecuta dentro de una **transacción** (`beginTransaction()` en
`setUp()`) que se **revierte** (`rollBack()` en `tearDown()`). De esta manera,
la base de datos permanece intacta después de cada ejecución, garantizando
pruebas repetibles e independientes.

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
vendor/bin/phpunit tests/integracion/catalogo-busqueda/CatalogoBusquedaIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-CAT-01** | `testListaProductosDeUnaCategoriaActiva` | Recuperar los productos asociados a una categoría activa del catálogo. | **Prueba de flujo integrado** | Se obtiene una lista de productos visibles pertenecientes a la categoría seleccionada. |
| **CI-CAT-02** | `testBusquedaPorNombreRetornaCoincidenciasReales` | Buscar productos mediante una coincidencia parcial del nombre. | **Partición de equivalencia** (búsqueda válida) | El producto buscado aparece dentro de los resultados obtenidos. |
| **CI-CAT-03** | `testBusquedaSinCoincidenciasRetornaListaVacia` | Realizar una búsqueda utilizando un término inexistente. | **Partición de equivalencia** (búsqueda sin coincidencias) | La consulta devuelve una lista vacía de resultados. |
| **CI-CAT-04** | `testDetalleDeProductoExponeFabricanteYRelacionadosCuandoExisten` | Consultar el detalle de un producto verificando fabricante y productos relacionados. | **Prueba de flujo integrado** | El detalle contiene el identificador del producto, su nombre, el fabricante y el número de productos relacionados. |
| **CI-CAT-05** | `testComparacionConservaMaximoDeCuatroProductos` | Agregar cinco productos a la comparación para validar el límite permitido por el catálogo. | **Análisis de valores límite** (máximo de elementos permitidos) | La lista de comparación conserva únicamente los cuatro productos más recientes. |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Prueba de flujo integrado | CI-CAT-01, CI-CAT-04 |
| Partición de equivalencia | CI-CAT-02, CI-CAT-03 |
| Análisis de valores límite | CI-CAT-05 |
| **Total** | **5 casos** |