# Diagrama: Procesos de Validación - Sistema de Reseñas

## Descripción

Árbol de decisiones para la visualización de reseñas en frontend, el envío de una reseña
nueva, y la moderación administrativa.

---

## Árbol de Decisiones de Validación

```mermaid
graph TD
    START["🚀 Interacción con Reseñas"]

    START --> A{¿Contexto?}

    A -->|Ver reseñas| VIEW["👁️ VISUALIZACIÓN"]
    A -->|Enviar reseña| SEND["✍️ ENVÍO DE RESEÑA"]
    A -->|Moderar (admin)| MOD["👨‍💼 MODERACIÓN"]

    %% VISUALIZACION
    VIEW --> V1{¿product_id válido?}
    V1 -->|No| V2["❌ No mostrar bloque"]
    V1 -->|Sí| V3["🔍 Consultar reseñas<br/>status=true del producto"]

    V3 --> V4{¿Producto activo<br/>y vigente?}
    V4 -->|No| V5["📭 No contar ni mostrar<br/>ninguna reseña"]
    V4 -->|Sí| V6{¿Hay reseñas<br/>aprobadas?}

    V6 -->|No| V7["📭 Mostrar vacío,<br/>sin error"]
    V6 -->|Sí| V8["📋 Paginar de 5 en 5<br/>orden descendente por fecha"]

    %% ENVIO
    SEND --> S1{¿review_token válido<br/>de la sesión actual?}
    S1 -->|No| S2["❌ Rechazar"]
    S1 -->|Sí| S3{¿Módulo de reseñas<br/>habilitado?}

    S3 -->|No| S2
    S3 -->|Sí| S4{¿Producto existe?}

    S4 -->|No| S2
    S4 -->|Sí| S5{¿Usuario autenticado<br/>o invitado permitido?}

    S5 -->|No| S6["❌ Exigir login/registro"]
    S5 -->|Sí| S7{¿Requiere compra previa<br/>y no la tiene?}

    S7 -->|Sí| S8["❌ Exigir compra previa"]
    S7 -->|No, o cumplida| S9{¿Autor y texto<br/>cumplen longitud?}

    S9 -->|No| S10["❌ Errores por campo"]
    S9 -->|Sí| S11{¿Rating entre 1 y 5?}

    S11 -->|No| S10
    S11 -->|Sí| S12{¿Captcha resuelto<br/>(si requerido)?}

    S12 -->|No| S10
    S12 -->|Sí| S13["✅ Guardar en estado<br/>pendiente"]

    S13 --> S14["✅ Confirmar al usuario"]

    %% MODERACION
    MOD --> M1["📋 Listar reseñas<br/>(filtrable por producto,<br/>autor, estado, fecha)"]

    M1 --> M2{¿Acción del<br/>administrador?}
    M2 -->|Aprobar| M3["✅ status = true"]
    M2 -->|Editar| M4["✏️ Modificar autor/texto/rating"]
    M2 -->|Eliminar| M5["🗑️ Eliminar registro"]

    M3 --> M6["📢 Visible en frontend<br/>(si producto activo/vigente)"]
    M4 --> M6
    M5 --> M7["🚫 Ya no visible<br/>ni cuenta en el total"]

    style VIEW fill:#e1f5ff
    style SEND fill:#fff3e0
    style MOD fill:#f3e5f5
```

---

## Matriz de Validación por Contexto

| Contexto | Valida | Rechazo si falla | Resultado si pasa |
|---|---|---|---|
| **Visualización** | `product_id`, `status` del producto y de la reseña | Bloque vacío o no se muestra | Lista paginada de reseñas aprobadas |
| **Envío** | Token antifraude, módulo habilitado, producto existe, autenticación/compra previa, longitud, rating, captcha | Error estructurado por campo o motivo | Reseña guardada en estado pendiente |
| **Moderación** | Permisos de administrador | Acción no permitida | Reseña aprobada/editada/eliminada |

---

## Flujos Críticos

### 🔴 Flujo: Reseña de invitado no permitida
```
Usuario no autenticado intenta reseñar → config_review_guest = false →
Sistema exige login/registro → Usuario no puede enviar hasta autenticarse
```

### 🟡 Flujo: Reseña solo para compradores
```
Usuario intenta reseñar un producto que no compró → config_review_only_buyers = true →
Sistema rechaza y exige compra previa → Usuario compra el producto →
Usuario puede reseñar exitosamente
```

### 🟢 Flujo: Ciclo completo de moderación
```
Usuario envía reseña válida → Estado pendiente → Administrador revisa →
Aprueba → Reseña visible en frontend → Cuenta para el total y promedio del producto
```

---

## Puntos de Tolerancia

### Reseñas de productos desactivados
Una reseña aprobada de un producto que luego se desactiva **no se elimina**, simplemente deja
de mostrarse y de contar en el total — se reactiva automáticamente si el producto vuelve a
estar activo.

### Token antifraude por sesión, no por reseña
El `review_token` se genera una vez por sesión (no por cada reseña), lo que significa que un
usuario puede enviar múltiples reseñas en la misma sesión sin regenerar el token, siempre que
las demás validaciones (compra previa, longitud, rating) se cumplan para cada una.

### Verificación pendiente
⏳ El flujo de aprobación real desde el panel admin (cambio de `status` y su efecto inmediato en
el frontend) no se verificó de punta a punta en este ciclo de pruebas — ver
[Historia de Usuario 3 y 4](../../tests/aceptacion/6-Sistema-de-Resenas.md) del documento de
aceptación.
