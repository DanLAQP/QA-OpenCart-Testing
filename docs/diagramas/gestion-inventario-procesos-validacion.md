# Diagrama: Procesos de Validación - Gestión de Inventario

## Descripción

Este diagrama presenta un árbol de decisiones completo para todas las validaciones de stock en diferentes puntos del sistema.

---

## Árbol de Decisiones de Validación

```mermaid
graph TD
    START["🚀 Usuario Interactúa con Producto"]
    
    START --> A{¿Dónde está el usuario?}
    
    A -->|Catálogo| CAT["📋 VALIDACIÓN EN CATÁLOGO"]
    A -->|Carrito| CART["🛒 VALIDACIÓN EN CARRITO"]
    A -->|Checkout| CHECK["💳 VALIDACIÓN EN CHECKOUT"]
    A -->|API| API["🔌 VALIDACIÓN EN API"]
    
    %% CATÁLOGO
    CAT --> C1{¿Producto existe?}
    C1 -->|No| C2["❌ No mostrar en catálogo"]
    C1 -->|Sí| C3{¿status = 1?}
    C3 -->|No| C2
    C3 -->|Sí| C4{¿date_available <= hoy?}
    C4 -->|No| C5["⏳ Mostrar pero deshabilitado<br/>Mostrar fecha de disponibilidad"]
    C4 -->|Sí| C6{¿Tiene variantes?}
    C6 -->|Sí| C7{¿Alguna variante tiene qty > 0?}
    C6 -->|No| C8{¿quantity > 0 OR<br/>allow_out_of_stock=true?}
    C7 -->|Sí| C9["✅ Habilitar Compra"]
    C7 -->|No| C10{¿allow_out_of_stock=true?}
    C8 -->|Sí| C9
    C8 -->|No| C11["❌ Deshabilitar<br/>Mostrar estado"]
    C10 -->|Sí| C9
    C10 -->|No| C11
    
    %% CARRITO
    CART --> CC1["👤 Usuario agrega producto<br/>al carrito"]
    CC1 --> CC2{¿Es variante o<br/>producto simple?}
    CC2 -->|Variante| CC3["🔍 Validar stock de variante"]
    CC2 -->|Simple| CC4["🔍 Validar stock de producto"]
    
    CC3 --> CC5{¿Tiene opciones<br/>con descuento?}
    CC5 -->|Sí| CC6["🔍 Validar stock de cada opción"]
    CC5 -->|No| CC7{¿quantity_requerida <=<br/>quantity_disponible?}
    
    CC4 --> CC7
    CC6 --> CC8{¿Todas las opciones<br/>tienen stock?}
    CC8 -->|Sí| CC7
    CC8 -->|No| CC9["❌ Rechazar agregación<br/>Mostrar qué opción sin stock"]
    
    CC7 -->|Sí| CC10{¿quantity >= minimum?}
    CC7 -->|No| CC11["❌ Rechazar<br/>Mostrar cantidad máxima disponible"]
    
    CC10 -->|Sí| CC12{¿Producto ya está<br/>en carrito?}
    CC10 -->|No| CC13["❌ Rechazar<br/>Mostrar cantidad mínima requerida"]
    
    CC12 -->|Sí| CC14{¿total_nuevo <=<br/>stock_disponible?}
    CC12 -->|No| CC15["✅ Agregar al carrito"]
    
    CC14 -->|Sí| CC15
    CC14 -->|No| CC16["❌ Rechazar<br/>Stock total insuficiente"]
    
    %% CHECKOUT
    CHECK --> CHK1["💳 Usuario inicia checkout"]
    CHK1 --> CHK2["🔍 Recalcular stock actual"]
    CHK2 --> CHK3{¿Stock cambió<br/>desde que se agregó?}
    CHK3 -->|Aumentó| CHK4["✅ Sin problema"]
    CHK3 -->|Disminuyó| CHK5{¿Sigue habiendo<br/>suficiente stock?}
    CHK3 -->|Item sin stock ahora| CHK6["⚠️ Eliminar item del carrito<br/>Notificar usuario"]
    
    CHK5 -->|Sí| CHK7{¿Cantidad aún >= mínimo?}
    CHK5 -->|No| CHK8["❌ Rechazar checkout<br/>Reducir cantidad automática"]
    
    CHK7 -->|Sí| CHK9["✅ Permitir continuar"]
    CHK7 -->|No| CHK8
    
    CHK4 --> CHK10["📝 Usuario confirma orden"]
    CHK9 --> CHK10
    CHK6 --> CHK10
    
    %% CONFIRMACIÓN (dentro de checkout)
    CHK10 --> CONF["🔐 VALIDACIÓN FINAL PRE-CONFIRMACIÓN"]
    CONF --> CFM1["🔍 Validar stock UNA VÍA MÁS"]
    CFM1 --> CFM2{¿Stock sigue<br/>disponible?}
    CFM2 -->|Sí| CFM3["✅ Crear orden"]
    CFM2 -->|No| CFM4["❌ Cancelar transacción<br/>Informar al usuario"]
    
    CFM3 --> CFM5["📊 Restar stock del inventario"]
    CFM5 --> CFM6["📧 Enviar confirmación"]
    CFM4 --> CFM7["🔄 Volver a carrito"]
    
    %% API
    API --> A1{¿Operación?}
    A1 -->|GET /products/:id| AP1["🔍 Validar existencia"]
    A1 -->|POST /cart/items| AP2["🔍 Validar stock de producto"]
    A1 -->|POST /orders| AP3["🔍 Validar stock final"]
    A1 -->|PATCH /orders/:id/items| AP4["🔍 Validar cantidad adicional<br/>+ stock ya comprometido"]
    
    AP1 --> AP5{¿Producto existe?}
    AP5 -->|Sí| APR1["✅ Devolver datos<br/>Incluir stock si está configurado"]
    AP5 -->|No| APR2["❌ Error 404"]
    
    AP2 --> AP6{¿Stock disponible?}
    AP6 -->|Sí| APR1
    AP6 -->|No| APR3["❌ Error 400<br/>Respuesta estructurada con<br/>product_id, available_qty"]
    
    AP3 --> AP7{¿Stock final OK?}
    AP7 -->|Sí| APR4["✅ Crear orden<br/>API retorna order_id"]
    AP7 -->|No| APR5["❌ Error 400<br/>Detallar qué producto sin stock"]
    
    AP4 --> AP8{¿Stock = stock_original -<br/>cantidad_en_orden +<br/>cantidad_nueva?}
    AP8 -->|Sí| APR1
    AP8 -->|No| APR5
    
    style CAT fill:#e1f5ff
    style CART fill:#fff3e0
    style CHECK fill:#f3e5f5
    style CONF fill:#fce4ec
    style API fill:#e8f5e9
```

---

## Matriz de Validación por Punto

| Punto | Valida | Stock | Cantidad | Mínimo | Opciones | Resultado |
|---|---|---|---|---|---|---|
| **Catálogo** | Existencia, Status, Fecha, Variantes | ✅ | ❌ | ❌ | ✅ (disponibilidad) | Habilitar/Deshabilitar |
| **Carrito** | Existencia, Variante, Stock, Cantidad total | ✅ | ✅ | ✅ | ✅ (cada valor) | Agregar/Rechazar |
| **Checkout** | Cambios de stock, Disponibilidad actual | ✅ | ✅ | ✅ | ✅ | Continuar/Notificar |
| **Confirmación** | Validación final antes de restar stock | ✅ | ✅ | ✅ | ✅ | Crear orden/Cancelar |
| **API** | Según endpoint (GET/POST/PATCH) | ✅ | ✅ | ✅ | ✅ | 200/400/404 |

---

## Flujos Críticos

### 🔴 Flujo: Stock Insuficiente
```
Usuario agrega → Stock validado ✅ → Stock baja durante checkout → 
Validación en checkout ❌ → Notificar → Usuario decide → Volver a carrito
```

### 🟡 Flujo: Opción sin Stock
```
Usuario selecciona opción → Opción sin stock ❌ → Error específico de opción → 
Usuario selecciona otra opción → Validar nueva opción ✅ → Agregar
```

### 🟢 Flujo: Compra Exitosa
```
Catálogo ✅ → Carrito ✅ → Checkout ✅ → Confirmación ✅ → 
Orden creada → Stock restado → Confirmación enviada
```

---

## Puntos de Tolerancia

### Stock reservado en órdenes pendientes
- Al crear orden, stock se reserva
- Si orden se cancela, stock se libera
- PATCH en orden considera stock ya comprometido

### Cambios concurrentes
- Si dos usuarios compran simultáneamente el último item
- El primero que confirma logra la compra
- El segundo recibe error en validación final

### Configuración de tiempo de vida de carrito
- Carrito se valida al iniciar checkout
- Carrito se valida nuevamente antes de confirmar
- Carrito desactualizado se notifica al usuario
