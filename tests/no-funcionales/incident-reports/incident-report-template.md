# Reporte de Incidente — No Funcional (Rendimiento / Carga)

**ID**: INC-RENDIMIENTO-XXX
**Fecha**: AAAA-MM-DD
**Autor**:
**Escenario k6**: [catalogo_y_carrito / listado_admin]
**Script**: `k6/inventario-carga.js`

## Resultado Esperado

Umbral (`threshold`) que debía cumplirse (ej. "p95 de `duracion_carrito` < 600ms bajo 30
usuarios virtuales concurrentes").

## Resultado Real

Valor real observado en la corrida (ej. "p95 de `duracion_carrito` = 1450ms"). Adjuntar la
tabla `TOTAL RESULTS` completa de la salida de k6, o el JSON exportado.

## Evidencia

```
Comando ejecutado:
k6 run inventario-carga.js -e ...

Salida relevante:
duracion_carrito.......: avg=... p(95)=...  ✗ 'p(95)<600' fallo
```

## Severidad

[ Crítica / Alta / Media / Baja ]

## Impacto

Qué tan grave es la degradación para el negocio (ej. checkout lento bajo carga real de
Black Friday, listado admin bloqueante para operadores, etc.)

## Causa raíz (si se identificó)

Consulta SQL, endpoint o recurso específico que causa el cuello de botella (ej. ausencia de
índice en `oc_product.quantity`, N+1 queries en el listado admin, etc.)

## Recomendación

Acción sugerida para corregir la degradación (ej. agregar índice, cachear resultado, paginar
de forma diferente, etc.)
