# Diagrama: Procesos de Validación - Checkout y Pago

## Descripción

Árbol de decisiones para las validaciones en cada paso del checkout: entrada, direcciones,
métodos de envío/pago, y confirmación final.

---

## Árbol de Decisiones de Validación

```mermaid
graph TD
    START["🚀 Usuario entra a Checkout"]

    START --> A{¿Carrito tiene<br/>productos válidos?}
    A -->|No| B["❌ Redirigir al carrito"]
    A -->|Sí| C{¿Stock suficiente para<br/>todos los productos?}

    C -->|No, sin permitir<br/>venta sin stock| B
    C -->|Sí, o permitido| D{¿Cantidades mínimas<br/>cumplidas?}

    D -->|No| B
    D -->|Sí| E["✅ Entrar a checkout"]

    E --> F{¿Requiere dirección<br/>de pago?}
    F -->|Sí| G{¿Campos válidos?<br/>(nombre, dirección, ciudad,<br/>país, código postal)}
    F -->|No| H["⏭️ Omitir paso"]

    G -->|No| I["❌ Error específico<br/>por campo"]
    G -->|Sí| J["✅ Dirección de pago guardada"]
    H --> J

    J --> K{¿Carrito requiere<br/>envío físico?}
    K -->|No| L["⏭️ Omitir dirección/método de envío"]
    K -->|Sí| M{¿Dirección de envío<br/>válida?}

    M -->|No| I
    M -->|Sí| N["🚚 Cotizar métodos de envío"]

    N --> O{¿Hay métodos<br/>disponibles?}
    O -->|No| P["❌ Sin opciones de envío"]
    O -->|Sí| Q{¿Método seleccionado<br/>existe entre cotizados?}

    Q -->|No| R["❌ Método inválido"]
    Q -->|Sí| S["✅ Método de envío confirmado"]

    L --> T["💳 Obtener métodos de pago"]
    S --> T

    T --> U{¿Hay métodos<br/>de pago disponibles?}
    U -->|No| V["❌ Sin opciones de pago"]
    U -->|Sí| W{¿Método seleccionado<br/>existe entre disponibles?}

    W -->|No| R
    W -->|Sí| X{¿Términos requieren<br/>aceptación?}

    X -->|Sí, no aceptados| Y["❌ Exigir aceptación"]
    X -->|No, o aceptados| Z["✅ Listo para confirmar"]

    Z --> AA["🔐 VALIDACIÓN FINAL"]
    AA --> AB{¿Info de cliente<br/>completa?}
    AB -->|No| AC["❌ Rechazar"]
    AB -->|Sí| AD{¿Stock/mínimos<br/>siguen válidos?}

    AD -->|No| AE["❌ Informar producto<br/>ya no disponible"]
    AD -->|Sí| AF{¿Dirección y método<br/>de envío válidos<br/>(si aplica)?}

    AF -->|No| AC
    AF -->|Sí| AG{¿Dirección de pago<br/>válida (si aplica)?}

    AG -->|No| AC
    AG -->|Sí| AH{¿Método de pago<br/>seleccionado?}

    AH -->|No| AC
    AH -->|Sí| AI["✅ Generar orden"]

    AI --> AJ{¿Pago procesado?}
    AJ -->|Sí| AK["🎉 Éxito"]
    AJ -->|No| AL["⚠️ Fallo"]

    style B fill:#fce4ec
    style AC fill:#fce4ec
    style AK fill:#e8f5e9
    style AL fill:#fff3e0
```

---

## Matriz de Validación por Paso

| Paso | Valida | Rechazo si falla | Continúa si pasa |
|---|---|---|---|
| **Entrada a checkout** | Carrito, stock, mínimos | Redirige al carrito | Muestra pantalla de checkout |
| **Dirección de pago** | Campos obligatorios, formato | Error por campo | Guarda dirección |
| **Dirección de envío** | Igual que pago (si aplica) | Error por campo | Guarda dirección |
| **Método de envío** | Disponibilidad, existencia en cotización | Sin opciones / método inválido | Confirma selección |
| **Método de pago** | Disponibilidad, existencia, términos | Sin opciones / falta aceptar términos | Confirma selección |
| **Confirmación final** | Todo lo anterior + revalidación de stock | Rechaza confirmación | Genera orden |

---

## Flujos Críticos

### 🔴 Flujo: Stock cambia durante el checkout
```
Usuario agrega producto al carrito → Stock suficiente en ese momento →
Usuario completa direcciones y métodos (tarda varios minutos) →
Otro cliente compra las últimas unidades → Usuario confirma pedido →
Validación final detecta stock insuficiente → Rechaza confirmación →
Usuario vuelve al carrito con la información actualizada
```

### 🟡 Flujo: Cambio de dirección invalida cotizaciones previas
```
Usuario cotiza envío con Dirección A → Selecciona método de envío →
Usuario regresa y cambia a Dirección B → Sistema limpia método de envío/pago previos →
Usuario debe recotizar envío y volver a seleccionar método de pago
```

### 🟢 Flujo: Compra exitosa de punta a punta
```
Carrito válido → Direcciones completas → Envío cotizado y seleccionado →
Pago seleccionado y términos aceptados → Validación final OK →
Orden generada → Pago procesado → Pantalla de éxito → Carrito vaciado
```

---

## Puntos de Tolerancia

### Checkout sin envío físico
Si todos los productos del carrito son digitales/sin necesidad de envío, los pasos de dirección
y método de envío se omiten por completo, simplificando el flujo.

### Recuperación de orden incompleta
Si el usuario abandona el checkout con una orden ya generada en estado "incompleta"
(`order_status_id = 0`), el sistema la recupera de la sesión en vez de crear una nueva orden
duplicada al volver.

### Riesgo conocido: manejo de errores de infraestructura
⚠️ Si la base de datos falla durante cualquier paso del checkout, el sistema actualmente expone
un error técnico (stack trace) en vez de la "pantalla de fallo" descrita en este flujo — ver
[INC-DISP-001](../../tests/no-funcionales/disponibilidad/incident-reports/INC-DISP-001-error-no-controlado-sin-bd.md).
