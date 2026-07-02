# Reporte de Incidente — Integración API

**ID**: INC-INTEGRACION-XXX
**Fecha**: AAAA-MM-DD
**Autor**:
**Frontera afectada**: api/cart.php (Subsistema A) → api/order.php::confirm() (Subsistema B)
**Caso de prueba**: [Sintáctico / Semántico / Resiliencia]
**Request de Postman**: [nombre exacto del request en la colección]

## Resultado Esperado

Descripción de lo que la interfaz debería hacer según el contrato esperado
(ej. "debe devolver un error 4xx con detalle del campo inválido, sin afectar el estado del carrito").

## Resultado Real

Descripción de lo observado (código HTTP, cuerpo de la respuesta, estado del carrito/pedido tras
la llamada). Adjuntar el JSON de respuesta completo.

## Evidencia

```
Request:
POST {{base_url}}/index.php?route=...
Body: ...

Response (HTTP xxx):
{ ... }
```

## Severidad

[ Crítica / Alta / Media / Baja ]

## Impacto

Qué tan grave es la discrepancia para el negocio (ej. pedido corrupto en BD, doble cobro,
carrito bloqueado, información expuesta, etc.)

## Causa raíz (si se identificó)

Archivo y línea del código donde ocurre el comportamiento.

## Recomendación

Acción sugerida para corregir la discrepancia.
