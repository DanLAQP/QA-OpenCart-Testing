# Pruebas de Integración — Checkout e Inventario

Archivo de pruebas: [`CheckoutInventoryIntegrationTest.php`](./CheckoutInventoryIntegrationTest.php)

## Objetivo

Verificar la **interacción real entre el proceso de checkout y el inventario** de
OpenCart, usando la base de datos real (no mocks). Se valida que al procesar una
compra el stock del producto en `oc_product` se descuente de forma consistente y
que las reglas de validación (sintaxis, stock disponible y resiliencia ante
latencia) aborten el proceso cuando corresponde.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Inventario | `oc_product` |

## Estrategia de aislamiento

> ⚠️ **Nota:** a diferencia del resto de las suites, esta prueba **no** envuelve
> cada caso en una transacción con `rollBack()`. Las operaciones de checkout
> escriben directamente sobre `oc_product` (descuento de stock). Cada prueba
> deja el stock en un valor conocido con `actualizarStock()` en su preparación,
> pero conviene ejecutarla contra una base de datos de pruebas dedicada.

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
phpunit --no-configuration tests/gestion-inventario/CheckoutInventoryIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-CHKINV-01** | `testFlujoCheckoutActualizaInventario` | Procesar un checkout válido descuenta el stock del producto en `oc_product`. | **Prueba de flujo integrado** (camino feliz / end-to-end) | Stock inicial 10, compra 2 → stock final = 8 y el proceso retorna `true`. |
| **CI-CHKINV-02** | `testFallaSintacticaConTiposInvalidos` | Enviar `product_id` y `quantity` no numéricos en el payload. | **Prueba sintáctica** (validación de tipos de entrada) | Se lanza `InvalidArgumentException` («…deben ser numericos.») antes de tocar el inventario. |
| **CI-CHKINV-03** | `testFallaSemanticaCantidadMayorAlStock` | Solicitar más unidades que el stock disponible. | **Prueba semántica** (regla de negocio: no se vende sin stock) | Stock 5, se piden 20 → se lanza `RuntimeException` («Stock insuficiente…»). |
| **CI-CHKINV-04** | `testResilienciaAnteLatenciaAlta` | La operación excede el timeout permitido por latencia de la pasarela. | **Prueba de resiliencia** (tolerancia a latencia / timeout) | El checkout retorna `false`, no se procesa y el stock permanece en 10. |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Prueba de flujo integrado | CI-CHKINV-01 |
| Prueba sintáctica | CI-CHKINV-02 |
| Prueba semántica | CI-CHKINV-03 |
| Prueba de resiliencia | CI-CHKINV-04 |
| **Total** | **4 casos** |
