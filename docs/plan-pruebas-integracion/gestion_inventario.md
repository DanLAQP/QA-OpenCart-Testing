# 4.2.1 Solicitud de una Cuenta de Instructor - Gestión de Inventario

## Información General

| Atributo | Valor |
|----------|-------|
| ID | INV-0001 |
| Funcionalidad | Gestión de Inventario - Stock de Producto |
| Descripción | Módulo que permite la visualización, validación y control del inventario de productos, incluyendo stock disponible, variantes y opciones con impacto en inventario |
| Requisito Asociado | RF-INV-001 al RF-INV-038 |
| Precondiciones | Estar autenticado como administrador. Acceder a la sección de productos en el panel administrativo. |
| Datos de Entrada | Cantidad de producto, SKU, variantes, opciones, fechas de disponibilidad, estado de producto |
| Pasos de Ejecución | 1. Acceder a panel administrativo. 2. Navegar a Catálogo > Productos. 3. Seleccionar o crear un producto. 4. Validar campos de inventario |
| Técnicas de Pruebas | Partición de Equivalencia, Valores Límites, Tablas de Decisión, Transición de Estados |
| Prioridad | Alta |

---

## Técnicas de Pruebas Implementadas

### 1. Partición de Equivalencia

| Cod. | Campo | Clase Válida | Clases No Válidas |
|------|-------|--------------|-------------------|
| INV-PE-001 | Cantidad (stock) | 0 ≤ cantidad ≤ 999999, números enteros | Valores negativos, decimales, caracteres alfabéticos |
| INV-PE-002 | SKU | 1-64 caracteres, alfanuméricos y guiones | Vacío, > 64 caracteres, caracteres especiales |
| INV-PE-003 | Cantidad Mínima de Compra | 0 < mínimo ≤ cantidad total, números enteros | Vacío, valores negativos, mayor a stock total |
| INV-PE-004 | Fecha de Disponibilidad | Formato válido (YYYY-MM-DD), fecha futura o presente | Formato inválido, vacío, caracteres no válidos |
| INV-PE-005 | Estado del Producto | Activo (1) o Inactivo (0) | Valores diferentes a 0 o 1, vacío |
| INV-PE-006 | Precio Base | Número decimal positivo ≤ 999999.99 | Valores negativos, caracteres alfabéticos |
| INV-PE-007 | Cantidad de Variantes | 0 ≤ variantes ≤ 1000, números enteros | Valores negativos, decimales |
| INV-PE-008 | Stock por Opción | 0 ≤ stock ≤ 999999, números enteros | Valores negativos, caracteres alfabéticos |

---

### 2. Análisis de Valores Límite

| Cod. | Campo | Límite Inferior Válido | Límite Inferior No Válido | Límite Superior Válido | Límite Superior No Válido |
|------|-------|------------------------|--------------------------|------------------------|--------------------------|
| INV-VL-001 | Cantidad (stock) | 0 | -1 | 999999 | 1000000 |
| INV-VL-002 | SKU | 1 carácter | 0 caracteres (vacío) | 64 caracteres | 65 caracteres |
| INV-VL-003 | Cantidad Mínima | 1 | 0 | Igual a cantidad total | Mayor a cantidad total |
| INV-VL-004 | Precio Base | 0.01 | 0 o negativo | 999999.99 | 1000000 |
| INV-VL-005 | Variantes | 0 | N/A (no aplica negativo) | 1000 | 1001 |
| INV-VL-006 | Stock por Opción | 0 | -1 | 999999 | 1000000 |

---

### 3. Tablas de Decisión

#### 3.1 Validación de Stock en Agregar al Carrito

| Cod. | Cantidad en Stock | Cantidad Solicitada | Estado Producto | Permitir Sin Stock | Acción Sistema |
|------|-------------------|-------------------|-----------------|-------------------|----------------|
| INV-TD-001 | 10 | 5 | Activo | No | "Producto agregado al carrito" |
| INV-TD-002 | 0 | 5 | Activo | No | "Error: Stock insuficiente" |
| INV-TD-003 | 0 | 5 | Activo | Sí | "Producto agregado al carrito" |
| INV-TD-004 | 5 | 10 | Activo | No | "Error: Cantidad superior al stock" |
| INV-TD-005 | 5 | 5 | Activo | No | "Producto agregado al carrito" |
| INV-TD-006 | 10 | 5 | Inactivo | No | "Error: Producto no disponible" |

#### 3.2 Validación de Cantidad Mínima

| Cod. | Cantidad Mínima | Cantidad Solicitada | Cantidad en Stock | Acción Sistema |
|------|-----------------|-------------------|------------------|----------------|
| INV-TD-007 | 5 | 3 | 10 | "Error: Cantidad mínima requerida: 5" |
| INV-TD-008 | 5 | 5 | 10 | "Producto agregado al carrito" |
| INV-TD-009 | 5 | 8 | 10 | "Producto agregado al carrito" |
| INV-TD-010 | 1 | 1 | 5 | "Producto agregado al carrito" |

#### 3.3 Validación de Stock por Opción

| Cod. | Opción Requerida | Stock Opción | Cantidad Solicitada | Acción Sistema |
|------|-----------------|-------------|-------------------|----------------|
| INV-TD-011 | Sí | 5 | 3 | "Producto agregado al carrito" |
| INV-TD-012 | Sí | 0 | 1 | "Error: Opción sin stock" |
| INV-TD-013 | Sí | 10 | 15 | "Error: Stock insuficiente para esta opción" |
| INV-TD-014 | No | 0 | 1 | "Producto agregado al carrito" |

---

### 4. Transición de Estados

#### 4.1 Ciclo de Stock de Producto

```
[Producto Creado] 
    ↓ (Agregar cantidad)
[Stock Disponible]
    ↓ (Venta realizada)
[Stock Reducido]
    ↓ (Cantidad llega a 0)
[Sin Stock] 
    ↓ (Reabastecimiento)
[Stock Disponible]
    ↓ (Estado = Inactivo)
[Producto Inactivo]
```

#### 4.2 Disponibilidad por Fecha

```
[Fecha Futura] 
    ↓ (Fecha actual = fecha_disponibilidad - 1 día)
[Próxima a Disponibilidad]
    ↓ (Fecha actual ≥ fecha_disponibilidad)
[Disponible para Compra]
```

#### 4.3 Estado de Opción

```
[Opción Creada]
    ↓ (Asignar stock)
[Con Stock]
    ↓ (Stock agotado)
[Sin Stock]
    ↓ (Reabastecer)
[Con Stock]
```

---

## Catálogo de Pruebas

### Sección 1: Stock Básico de Producto (CP-INV-001 al CP-INV-010)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-001 | Cantidad: 50, SKU: "PROD-001", Estado: Activo | Stock se almacena correctamente en BD, cantidad visible en admin | f+ |
| CP-INV-002 | Cambiar cantidad de 50 a 30 en producto existente | Stock se actualiza en BD y se refleja en catálogo | f+ |
| CP-INV-003 | Producto con cantidad: 0, estado: Activo | Botón "Comprar" deshabilitado, se muestra "Sin stock" | f- |
| CP-INV-004 | Producto con cantidad: 5, estado: Activo | Botón "Comprar" habilitado, permite agregar al carrito | f+ |
| CP-INV-005 | Cantidad: 0, "Permitir compra sin stock": Deshabilitado | Sistema rechaza agregar al carrito, muestra error de stock | f- |
| CP-INV-006 | Cantidad: 0, "Permitir compra sin stock": Habilitado | Sistema permite agregar al carrito a pesar de cantidad 0 | f+ |
| CP-INV-007 | Cantidad mínima: 5, cantidad solicitada: 3, stock: 10 | Sistema rechaza, muestra "Cantidad mínima requerida: 5" | f- |
| CP-INV-008 | Cantidad mínima: 5, cantidad solicitada: 5, stock: 10 | Sistema acepta, agrega al carrito | f+ |
| CP-INV-009 | Cantidad mínima: 5, stock: 4 | Producto no disponible para compra (stock < mínimo) | f- |
| CP-INV-010 | Cantidad: 100, SKU: "PROD-010", cantidad mínima: 1 | Producto agregado correctamente al carrito | f+ |

---

### Sección 2: Valores Límite - Cantidad (CP-INV-011 al CP-INV-020)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-011 | Cantidad: 0 (límite inferior válido) | Stock se almacena, producto muestra "Sin stock" | f+ |
| CP-INV-012 | Cantidad: 1 (límite inferior + 1) | Stock se almacena, producto disponible para compra | f+ |
| CP-INV-013 | Cantidad: 999999 (límite superior válido) | Stock se almacena correctamente | f+ |
| CP-INV-014 | Cantidad: 1000000 (límite superior + 1) | Sistema rechaza, muestra error "Cantidad excede límite" | f- |
| CP-INV-015 | Cantidad: -1 (valor negativo) | Sistema rechaza, muestra error "Cantidad debe ser ≥ 0" | f- |
| CP-INV-016 | Cantidad: 99.99 (decimal) | Sistema rechaza o redondea a 99 | f- |
| CP-INV-017 | Cantidad: "abc" (caracteres alfabéticos) | Sistema rechaza, muestra error "Debe ser un número" | f- |
| CP-INV-018 | Cantidad: 500000, cantidad solicitada: 500000 | Permite agregar al carrito cantidad exacta | f+ |
| CP-INV-019 | Cantidad: 500000, cantidad solicitada: 500001 | Sistema rechaza, stock insuficiente | f- |
| CP-INV-020 | Cantidad: 999999, cantidad solicitada: 999999 | Permite agregar cantidad exacta al carrito | f+ |

---

### Sección 3: SKU - Partición de Equivalencia (CP-INV-021 al CP-INV-030)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-021 | SKU: "PROD-001" (válido, 9 caracteres) | SKU se almacena y es único en la BD | f+ |
| CP-INV-022 | SKU: "" (vacío) | Sistema rechaza, muestra error "SKU requerido" | f- |
| CP-INV-023 | SKU: "A" (1 carácter, válido) | SKU se almacena correctamente | f+ |
| CP-INV-024 | SKU: (65 caracteres) | Sistema rechaza, muestra error "SKU no puede exceder 64 caracteres" | f- |
| CP-INV-025 | SKU: "PROD-001" (SKU duplicado) | Sistema rechaza, muestra error "SKU ya existe" | f- |
| CP-INV-026 | SKU: "PROD@001" (caracteres especiales) | Sistema rechaza, muestra error "SKU solo puede contener alfanuméricos y guiones" | f- |
| CP-INV-027 | SKU: "PROD-001" (mayúsculas) | SKU se almacena como se introduce | f+ |
| CP-INV-028 | SKU: "prod-001" (minúsculas) | SKU se almacena como se introduce | f+ |
| CP-INV-029 | SKU: "PROD-001-V1" (con guión) | SKU se almacena correctamente | f+ |
| CP-INV-030 | SKU: "PROD 001" (con espacio) | Sistema rechaza, muestra error "No se permiten espacios" | f- |

---

### Sección 4: Cantidad Mínima de Compra (CP-INV-031 al CP-INV-040)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-031 | Cantidad mínima: 1, cantidad solicitada: 1, stock: 10 | Permite agregar al carrito | f+ |
| CP-INV-032 | Cantidad mínima: 5, cantidad solicitada: 4, stock: 10 | Rechaza, muestra error de cantidad mínima | f- |
| CP-INV-033 | Cantidad mínima: 5, cantidad solicitada: 5, stock: 10 | Permite agregar al carrito | f+ |
| CP-INV-034 | Cantidad mínima: 5, cantidad solicitada: 6, stock: 10 | Permite agregar al carrito | f+ |
| CP-INV-035 | Cantidad mínima: 100, cantidad en stock: 50 | Producto no disponible para compra | f- |
| CP-INV-036 | Cantidad mínima: 0 (inválido) | Sistema rechaza, muestra error "Mínimo debe ser ≥ 1" | f- |
| CP-INV-037 | Cantidad mínima: -5 (negativo) | Sistema rechaza, muestra error "Cantidad mínima debe ser positiva" | f- |
| CP-INV-038 | Cantidad mínima: 999999, cantidad en stock: 1000000 | Permite compra si cantidad solicitada ≥ 999999 | f+ |
| CP-INV-039 | Cantidad mínima: vacío | Sistema usa valor por defecto (1) | f+ |
| CP-INV-040 | Cantidad mínima: 5, cantidad solicitada en carrito múltiple: 3+2 = 5 total | Permite agregar si total es ≥ mínimo | f+ |

---

### Sección 5: Estado del Producto (CP-INV-041 al CP-INV-050)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-041 | Estado: Activo (1), stock: 10 | Producto visible en catálogo y disponible para compra | f+ |
| CP-INV-042 | Estado: Inactivo (0), stock: 10 | Producto no visible en catálogo, no disponible para compra | f- |
| CP-INV-043 | Estado: Activo, visita de admin | Producto editable con todos los campos disponibles | f+ |
| CP-INV-044 | Estado: Inactivo, visita de admin | Producto editable, todas las opciones disponibles | f+ |
| CP-INV-045 | Cambiar estado de Activo a Inactivo | Producto desaparece del catálogo inmediatamente | f+ |
| CP-INV-046 | Cambiar estado de Inactivo a Activo | Producto reaparece en catálogo | f+ |
| CP-INV-047 | Estado: 2 (inválido) | Sistema rechaza, muestra error "Estado debe ser 0 o 1" | f- |
| CP-INV-048 | Estado: "Activo" (texto) | Sistema rechaza, muestra error "Valor no válido" | f- |
| CP-INV-049 | Estado: vacío | Sistema usa valor por defecto (Activo = 1) | f+ |
| CP-INV-050 | Estado: Inactivo, cantidad mínima: 5 | Producto no disponible para compra | f- |

---

### Sección 6: Fecha de Disponibilidad (CP-INV-051 al CP-INV-060)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-051 | Fecha disponible: hoy, estado: Activo | Producto disponible para compra | f+ |
| CP-INV-052 | Fecha disponible: ayer, estado: Activo | Producto disponible para compra | f+ |
| CP-INV-053 | Fecha disponible: mañana, estado: Activo | Producto no disponible, muestra fecha de disponibilidad | f- |
| CP-INV-054 | Fecha disponible: +30 días, estado: Activo | Producto no disponible hasta esa fecha | f- |
| CP-INV-055 | Fecha disponible: vacío, estado: Activo | Usa fecha actual por defecto, producto disponible | f+ |
| CP-INV-056 | Fecha disponible: formato inválido (DD-MM-YYYY) | Sistema rechaza, muestra error de formato | f- |
| CP-INV-057 | Fecha disponible: "2099-12-31" (fecha muy lejana) | Producto no disponible hasta esa fecha | f- |
| CP-INV-058 | Fecha disponible: "1900-01-01" (fecha pasada) | Producto disponible (fecha pasada) | f+ |
| CP-INV-059 | Cambiar fecha de futura a hoy | Producto se vuelve disponible inmediatamente | f+ |
| CP-INV-060 | Fecha disponible: hoy, stock: 0, permitir sin stock: No | Producto no disponible (sin stock) | f- |

---

### Sección 7: Variantes de Producto (CP-INV-061 al CP-INV-075)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-061 | Crear variante con SKU: "PROD-001-RED" | Variante se crea como registro dependiente del maestro | f+ |
| CP-INV-062 | Crear variante con cantidad: 30, maestro: 100 | Variante independiente, no afecta stock del maestro | f+ |
| CP-INV-063 | Maestro con variantes, intento de agregar maestro al carrito | Sistema rechaza, pide seleccionar variante | f- |
| CP-INV-064 | Maestro con 3 variantes, agregar variante específica | Se agrega variante, no el maestro | f+ |
| CP-INV-065 | Variante 1: sin stock, Variante 2: con stock | Se muestra solo Variante 2 disponible | f+ |
| CP-INV-066 | Crear 100 variantes de un maestro | Sistema crea todas correctamente | f+ |
| CP-INV-067 | Variante con cantidad: 0 | Se muestra como "Sin stock" en selector | f- |
| CP-INV-068 | Variante con cantidad mínima: 5 | Cumple validación de cantidad mínima por variante | f+ |
| CP-INV-069 | Editar cantidad de variante de 30 a 10 | Stock se actualiza solo en esa variante | f+ |
| CP-INV-070 | Eliminar variante con stock: 5 | Variante se elimina, stock se libera | f+ |
| CP-INV-071 | Variante con estado: Inactivo | Variante no se muestra en selector | f- |
| CP-INV-072 | Variante con estado: Activo, maestro: Inactivo | Variante no disponible (maestro inactivo) | f- |
| CP-INV-073 | Variante con fecha disponible futura | Variante no disponible hasta esa fecha | f- |
| CP-INV-074 | Crear variante con SKU duplicado a otra variante | Sistema rechaza, muestra error "SKU duplicado" | f- |
| CP-INV-075 | Variante con precio: 150, maestro: 100 | Precio de variante se aplica en carrito | f+ |

---

### Sección 8: Opciones con Stock (CP-INV-076 al CP-INV-090)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-076 | Opción "Talla": S (5), M (10), L (15) con descuento de inventario | Cada valor tiene stock independiente | f+ |
| CP-INV-077 | Opción "Color": Rojo (0), Azul (5) | Rojo no disponible, Azul disponible | f- |
| CP-INV-078 | Opción "Talla": sin descuento de inventario | Stock no se decrementa por opción | f+ |
| CP-INV-079 | Seleccionar opción sin stock | Sistema rechaza, muestra error "Opción sin stock" | f- |
| CP-INV-080 | Seleccionar opción con stock suficiente | Permite agregar al carrito | f+ |
| CP-INV-081 | Opción múltiple (checkbox): Protector (5), Cable (8) | Cada valor tiene stock independiente | f+ |
| CP-INV-082 | Opción múltiple: Protector (0), Cable (8), seleccionar solo Cable | Permite agregar (Cable tiene stock) | f+ |
| CP-INV-083 | Opción múltiple: Protector (0), Cable (0), seleccionar ambos | Sistema rechaza, una o ambas sin stock | f- |
| CP-INV-084 | Opción requerida sin stock | Producto no disponible | f- |
| CP-INV-085 | Opción opcional sin stock | Producto disponible, opción deshabilitada | f+ |
| CP-INV-086 | Agregar opción múltiple con cantidad: 2 | Decrementa stock de cada opción seleccionada | f+ |
| CP-INV-087 | Cantidad de opción: 0 | Se muestra como "Agotado" | f- |
| CP-INV-088 | Cantidad de opción: 1 | Se muestra como disponible | f+ |
| CP-INV-089 | Cantidad de opción: 999999 | Se almacena correctamente | f+ |
| CP-INV-090 | Stock por opción se agota durante checkout | Sistema valida y rechaza si cantidad no disponible | f- |

---

### Sección 9: Validación en Carrito (CP-INV-091 al CP-INV-110)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-091 | Producto: stock 10, cantidad solicitada: 5 | Producto agregado correctamente al carrito | f+ |
| CP-INV-092 | Producto: stock 0, permitir sin stock: No | Sistema rechaza, muestra error de stock | f- |
| CP-INV-093 | Producto: stock 0, permitir sin stock: Sí | Producto agregado al carrito | f+ |
| CP-INV-094 | Cantidad solicitada: 15, stock: 10 | Sistema rechaza, muestra cantidad máxima disponible | f- |
| CP-INV-095 | Cantidad solicitada: 10, stock: 10 | Permite agregar cantidad exacta | f+ |
| CP-INV-096 | Cantidad mínima: 5, cantidad en carrito: 3 | Carrito muestra advertencia de cantidad mínima | f- |
| CP-INV-097 | Agregar mismo producto dos veces: 3 unidades c/u = 6 total | Carrito suma a 6 unidades del mismo producto | f+ |
| CP-INV-098 | Producto A: stock 5, carrito ya tiene 3, intenta agregar 3 más | Sistema rechaza, solo permite 2 más | f- |
| CP-INV-099 | Producto con opción sin stock | Sistema rechaza, opción no disponible | f- |
| CP-INV-100 | Producto con opción con stock | Permite agregar al carrito | f+ |
| CP-INV-101 | Cambiar cantidad en carrito de 5 a 10, stock: 10 | Permite actualizar | f+ |
| CP-INV-102 | Cambiar cantidad en carrito de 5 a 15, stock: 10 | Sistema rechaza, cantidad excede stock | f- |
| CP-INV-103 | Agregar producto varias veces hasta agotar stock | Última agregación rechazada | f- |
| CP-INV-104 | Carrito con 3 items de stock: 5 | Valida cantidad total en checkout | f+ |
| CP-INV-105 | Carrito vacío, intentar checkout | Sistema rechaza, carrito vacío | f- |
| CP-INV-106 | Producto sin stock se agrega a carrito con "permitir sin stock" | Se crea orden sin validación de stock | f+ |
| CP-INV-107 | Actualizar página con carrito activo | Carrito se reconstruye, stock se revalida | f+ |
| CP-INV-108 | Cerrar sesión con carrito, volver a entrar | Carrito se recupera (si es persistente) | f+ |
| CP-INV-109 | Stock se reduce durante navegación (otra pestaña compra) | Carrito se revalida antes de checkout | f+ |
| CP-INV-110 | Agregar producto con cantidad mínima: 5, cantidad en carrito: 3 | Error: cantidad mínima no cumplida | f- |

---

### Sección 10: Validación en Checkout (CP-INV-111 al CP-INV-125)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-111 | Carrito: producto stock 10, cantidad: 8, intentar checkout | Checkout procede, valida stock nuevamente | f+ |
| CP-INV-112 | Stock se reduce a 5 durante checkout (stock inicial: 10) | Sistema rechaza si cantidad (8) > nuevo stock (5) | f- |
| CP-INV-113 | Stock se reduce a 8 durante checkout | Checkout procede (cantidad exacta al nuevo stock) | f+ |
| CP-INV-114 | Cantidad mínima: 5, carrito: 3 items | Checkout rechaza, cantidad menor a mínimo | f- |
| CP-INV-115 | Cantidad mínima: 5, carrito: 5 items | Checkout procede sin error | f+ |
| CP-INV-116 | Cantidad mínima: 5, carrito: 10 items | Checkout procede sin error | f+ |
| CP-INV-117 | Múltiples productos en carrito, uno sin stock | Sistema rechaza carrito completo o solo ese item | f- |
| CP-INV-118 | Múltiples productos en carrito, todos con stock | Checkout procede | f+ |
| CP-INV-119 | Confirmar orden, stock se resta correctamente | Stock se decrementa en BD | f+ |
| CP-INV-120 | Confirmar orden con múltiples items | Stock se resta por cada item | f+ |
| CP-INV-121 | Confirmar orden con opción específica | Stock de opción se decrementa | f+ |
| CP-INV-122 | Intentar confirmar orden dos veces (doble clic) | Solo se crea una orden | f+ |
| CP-INV-123 | Orden confirmada, carrito se vacía | Carrito muestra como vacío | f+ |
| CP-INV-124 | Ver orden creada, verificar cantidad en orden | Cantidad reflejada correctamente en orden | f+ |
| CP-INV-125 | Producto agotado tras confirmación | Stock: 0, producto muestra "Sin stock" | f- |

---

### Sección 11: Administración de Inventario (CP-INV-126 al CP-INV-140)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-126 | Acceder a Admin > Catálogo > Productos | Lista muestra todos los productos con cantidad | f+ |
| CP-INV-127 | Filtrar productos por rango: cantidad_min=5, cantidad_max=20 | Se muestran solo productos con 5-20 en stock | f+ |
| CP-INV-128 | Filtrar productos: cantidad_max=0 | Se muestran solo productos sin stock | f+ |
| CP-INV-129 | Filtrar productos: cantidad_min=100 | Se muestran solo productos con 100+ en stock | f+ |
| CP-INV-130 | Ordenar por cantidad: ASC | Productos ordenados de menor a mayor cantidad | f+ |
| CP-INV-131 | Ordenar por cantidad: DESC | Productos ordenados de mayor a menor cantidad | f+ |
| CP-INV-132 | Listar productos maestros con variantes | Maestros expandibles mostrando variantes | f+ |
| CP-INV-133 | Identificar producto maestro en listado | Icono o indicador especial en maestro | f+ |
| CP-INV-134 | Identificar variante en listado | Indentación o icono diferente para variante | f+ |
| CP-INV-135 | Editar cantidad de producto desde listado | Permite editar cantidad en línea (si aplica) | f+ |
| CP-INV-136 | Editar variante desde listado maestro | Click en variante abre editor | f+ |
| CP-INV-137 | Editar múltiples productos (operación en lote) | Permite cambiar cantidad/estado de varios | f+ |
| CP-INV-138 | Cambiar estado de producto en listado | Estado se actualiza sin abrir editor | f+ |
| CP-INV-139 | Buscar producto por nombre en admin | Filtro retorna productos coincidentes | f+ |
| CP-INV-140 | Buscar producto por SKU en admin | Filtro retorna producto específico | f+ |

---

### Sección 12: API de Inventario (CP-INV-141 al CP-INV-155)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-141 | GET /api/products/5 | API retorna producto si existe, 404 si no | f+ |
| CP-INV-142 | GET /api/products/5?include=stock | Respuesta incluye campo "quantity" con valor actual | f+ |
| CP-INV-143 | GET /api/products/5/options | API retorna opciones del producto | f+ |
| CP-INV-144 | GET /api/products/5/options/3 | Respuesta incluye stock de cada valor de opción | f+ |
| CP-INV-145 | POST /api/cart/items con quantity=15, stock=10 | API rechaza (400), muestra error de stock insuficiente | f- |
| CP-INV-146 | POST /api/cart/items con quantity=2, minimum=5 | API rechaza, solicita cantidad mínima | f- |
| CP-INV-147 | POST /api/cart/items con quantity=5, stock=5 | API acepta agregación | f+ |
| CP-INV-148 | POST /api/orders con quantity=8, stock=5 | API rechaza, stock insuficiente para orden | f- |
| CP-INV-149 | POST /api/orders con datos válidos | Orden se crea, stock se resta | f+ |
| CP-INV-150 | PATCH /api/orders/123/items/1 con cantidad adicional | Sistema valida stock comprometido en orden | f+ |
| CP-INV-151 | POST /api/cart/items múltiples productos, uno sin stock | Error especifica cuál producto no tiene stock | f- |
| CP-INV-152 | POST /api/cart/items con opción sin stock | Error especifica opción_id problemática | f- |
| CP-INV-153 | GET /api/products/5 sin autenticación (si aplica) | API responde según configuración de permisos | f+ |
| CP-INV-154 | POST /api/cart/items con token inválido | API rechaza (401) | f- |
| CP-INV-155 | GET /api/inventory/low-stock?threshold=10 | API retorna productos con stock < threshold | f+ |

---

### Sección 13: Integración Catálogo y Búsqueda (CP-INV-156 al CP-INV-170)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-156 | Producto activo, stock: 10, en página de catálogo | Producto visible con cantidad mostrada | f+ |
| CP-INV-157 | Producto inactivo, en página de catálogo | Producto no visible | f- |
| CP-INV-158 | Producto activo, fecha_disponibilidad: futura | Producto no visible hasta esa fecha | f- |
| CP-INV-159 | Búsqueda por nombre, producto existe | Producto retornado en resultados con stock | f+ |
| CP-INV-160 | Búsqueda por nombre, producto no existe | Sin resultados | f- |
| CP-INV-161 | Búsqueda por SKU, producto existe | Producto retornado | f+ |
| CP-INV-162 | Filtro "En stock" en búsqueda | Muestra solo productos con cantidad > 0 | f+ |
| CP-INV-163 | Filtro "Sin stock" en búsqueda | Muestra solo productos con cantidad = 0 | f- |
| CP-INV-164 | Página de detalle muestra cantidad disponible | Cantidad visible para cliente | f+ |
| CP-INV-165 | Página de detalle sin stock | Muestra "Sin stock", botón deshabilitado | f- |
| CP-INV-166 | Página de detalle con variantes | Muestra stock específico por variante | f+ |
| CP-INV-167 | Página de detalle con opciones con stock | Muestra disponibilidad por opción | f+ |
| CP-INV-168 | Página de comparación incluye stock | Columna de stock visible en comparativa | f+ |
| CP-INV-169 | Producto sin stock en comparación | Muestra "Sin stock" | f- |
| CP-INV-170 | Cantidad se actualiza en detalle tras reabastecimiento | Detalle refleja cantidad actualizada | f+ |

---

### Sección 14: Reportes y Auditoría (CP-INV-171 al CP-INV-180)

| #CP | Datos de Entrada | Resultado Esperado | Obs |
|-----|-----------------|-------------------|-----|
| CP-INV-171 | Generar reporte: productos por cantidad | Reporte lista todos los productos ordenados por stock | f+ |
| CP-INV-172 | Reporte: productos con bajo stock | Muestra productos con cantidad < umbral definido | f+ |
| CP-INV-173 | Reporte: movimientos de inventario (últimos 30 días) | Lista todas las transacciones de stock | f+ |
| CP-INV-174 | Reporte: historial de cambios de cantidad | Auditoría de quién y cuándo cambió stock | f+ |
| CP-INV-175 | Historial muestra usuario que realizó cambio | Usuario identificado en auditoría | f+ |
| CP-INV-176 | Historial muestra fecha y hora de cambio | Timestamp registrado correctamente | f+ |
| CP-INV-177 | Historial muestra cantidad anterior y nueva | Diferencial visible en auditoría | f+ |
| CP-INV-178 | Reporte exportable a CSV | Archivo se genera correctamente | f+ |
| CP-INV-179 | Reporte exportable a PDF | Documento se genera correctamente | f+ |
| CP-INV-180 | Reporte de reconciliación: stock teórico vs. real | Identifica discrepancias | f+ |

---

## Resumen por Técnica

| Técnica | Cantidad |
|---------|----------|
| Partición de Equivalencia (PE) | 128 |
| Análisis de Valores Límite (AVL) | 32 |
| Tablas de Decisión (TD) | 14 |
| Transición de Estados (TE) | 6 |
| **Total** | **180** |

---

## Observaciones

- Este documento cubre **180 casos de prueba** funcionales diseñados para validar exhaustivamente el módulo de **Gestión de Inventario**.
- Los casos incluyen validaciones de stock, variantes, opciones, carrito, checkout, administración, API e integración con catálogo.
- Las pruebas abarcan tanto el flujo del cliente como las operaciones administrativas.
- Se recomienda ejecutar estos casos en combinación con pruebas de rendimiento y seguridad.
- Los casos marcados con **f+** son de éxito esperado, y los marcados con **f-** son de fallo esperado (validación de restricciones).

---

**Fecha de Creación:** 2026-06-12  
**Versión:** 1.0  
**Estado:** Diseño Completado
