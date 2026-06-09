# Diagrama: Estructura de Variantes y Opciones - Gestión de Inventario

## Descripción

Este diagrama muestra las relaciones entre producto maestro, variantes, opciones y cómo impactan en el inventario del sistema.

---

## Estructura de Variantes y Opciones

```mermaid
graph TD
    A["📦 Producto Maestro<br/>ID: 1 | SKU: PROD-BASE<br/>Quantity: 0 (No se compra directamente)"]
    
    A --> B["Variante 1<br/>ID: 101 | SKU: PROD-RED-S<br/>Quantity: 15"]
    A --> C["Variante 2<br/>ID: 102 | SKU: PROD-RED-M<br/>Quantity: 8"]
    A --> D["Variante 3<br/>ID: 103 | SKU: PROD-BLUE-S<br/>Quantity: 0"]
    
    B --> E["Opción: Color<br/>Valor: Rojo<br/>Impacto: +$5"]
    B --> F["Opción: Talla<br/>Valor: S<br/>Stock Opción: 15"]
    
    C --> G["Opción: Color<br/>Valor: Rojo<br/>Impacto: +$5"]
    C --> H["Opción: Talla<br/>Valor: M<br/>Stock Opción: 8"]
    
    D --> I["Opción: Color<br/>Valor: Azul<br/>Impacto: +$3"]
    D --> J["Opción: Talla<br/>Valor: S<br/>Stock Opción: 0"]
    
    E --> K["💰 Precio = 100 + 5 = $105"]
    F --> K
    
    G --> L["💰 Precio = 100 + 5 = $105"]
    H --> L
    
    I --> M["💰 Precio = 100 + 3 = $103"]
    J --> N["❌ Sin Stock: Opción requerida sin disponibilidad"]
    
    K --> O["✅ Variante Comprable"]
    L --> O
    M --> P["⚠️ Variante disponible pero opción talla sin stock"]
    N --> Q["❌ Variante NO Comprable"]
    
    O --> R["🛒 Usuario puede agregar esta variante"]
    Q --> S["❌ Sistema marca variante como sin stock"]
    
    T["🔧 Configuración de Opciones"]
    T --> U["Descuento de Inventario: ON<br/>Stock se valida por opción"]
    T --> V["Precio Impactado: ON<br/>Opción suma precio a variante"]
    T --> W["Peso Impactado: ON<br/>Opción suma peso a variante"]
    
    U --> X["Cada valor de opción tiene qty"]
    V --> Y["Precio final = Base + Opciones"]
    W --> Z["Peso final = Base + Opciones"]
```

---

## Relaciones Clave

### Jerarquía de Productos

```
Producto Maestro
├── Variante 1
│   ├── Opción 1 (Color)
│   └── Opción 2 (Talla)
├── Variante 2
│   ├── Opción 1 (Color)
│   └── Opción 2 (Talla)
└── Variante 3
    ├── Opción 1 (Color)
    └── Opción 2 (Talla)
```

### Stock en Diferentes Niveles

| Nivel | Stock | Validación | Nota |
|---|---|---|---|
| **Producto Maestro** | 0 | No se valida (no se compra) | Solo gestión administrativa |
| **Variante** | 15, 8, 0 | Se valida al comprar variante | Cantidad de la variante específica |
| **Opción/Valor** | 15, 8, 0 | Se valida si descuento activo | Stock por cada valor de opción |

### Impacto en Precio, Puntos y Peso

```
Precio Final = Precio Base + Suma de Opciones
Puntos Final = Puntos Base + Suma de Opciones
Peso Final = Peso Base + Suma de Opciones

Ejemplo:
- Base: Precio=$100, Puntos=50, Peso=1kg
- Opción Talla M: +$10, +5pts, +0.5kg
- Opción Color Rojo: +$5, +0pts, +0kg
- Final: Precio=$115, Puntos=55, Peso=1.5kg
```

---

## Escenarios de Validación

### ✅ Variante Comprable
- Variante tiene `quantity > 0`
- Todas las opciones requeridas tienen stock (si descuento activo)
- Estado de variante es activo

### ⚠️ Variante Parcialmente Disponible
- Variante tiene cantidad
- Una opción requerida sin stock
- Resultado: **Sin stock** (marca toda la variante como no disponible)

### ❌ Variante No Comprable
- Variante sin cantidad (`quantity = 0`)
- O todas sus opciones requeridas sin stock
- O variante desactivada

---

## Integración con Carrito

```mermaid
graph LR
    A["Seleccionar Variante + Opciones"] 
    B["Calcular Precio + Impactos"]
    C["Validar Stock de Opción"]
    D["Agregar al Carrito"]
    
    A --> B
    B --> C
    C -->|OK| D
    C -->|Sin Stock| E["❌ Rechazar + Error"]
```

---

## Configuraciones de Opciones

- **Tipo Select**: Dropdown, cada valor con stock independiente
- **Tipo Radio**: Botones, cada valor con stock independiente
- **Tipo Checkbox**: Casillas múltiples, cada valor con stock independiente
- **Descuento de Inventario**: Si está ON, resta stock de la opción, no solo del producto
- **Impacto de Precio/Peso/Puntos**: Se suma al precio base según configuración
