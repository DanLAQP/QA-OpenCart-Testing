# Pruebas de Aceptación: Catálogo y Búsqueda

Trazable a [`docs/Requisitos-funcionales/Catalogo_Busquedas..md`](../../docs/Requisitos-funcionales/Catalogo_Busquedas..md).

---

## Historia de Usuario 1: Navegar productos por categoría

**Como** cliente,
**quiero** explorar productos organizados por categorías y subcategorías,
**para** encontrar lo que busco sin necesidad de conocer el nombre exacto del producto.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CAT-001 a RF-CAT-012

```gherkin
Escenario: Navegar a una categoría y ver sus productos
  Dado que la tienda tiene la categoría "Cameras" con productos asociados
  Cuando accedo a esa categoría desde el menú de navegación
  Entonces veo el listado de productos de esa categoría
  Y veo un camino de migas de pan (breadcrumbs) que indica dónde estoy

Escenario: Filtrar y ordenar productos dentro de una categoría
  Dado que estoy viendo el listado de una categoría con varios productos
  Cuando cambio el criterio de ordenamiento (por ejemplo, de menor a mayor precio)
  Entonces el listado se reordena según el criterio elegido
```

**Verificado**: ✅ confirmado contra `upload/` — `product/category&path=33` responde
correctamente y lista productos de la categoría Cameras.

---

## Historia de Usuario 2: Buscar productos por texto

**Como** cliente,
**quiero** buscar productos escribiendo palabras clave,
**para** encontrar rápidamente lo que necesito sin navegar por categorías.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CAT-013, RF-CAT-018, RF-CAT-021 a RF-CAT-029

```gherkin
Escenario: Búsqueda exitosa con resultados
  Dado que existen productos con la palabra "Camera" en su nombre
  Cuando busco "Camera" en la barra de búsqueda
  Entonces veo un listado de productos que coinciden
  Y cada resultado muestra imagen, descripción resumida y precio

Escenario: Búsqueda sin resultados
  Dado que ningún producto coincide con mi búsqueda
  Cuando busco un término inexistente como "zzznoexiste123"
  Entonces el sistema me muestra un mensaje indicando que no hay productos que coincidan
  Y no me muestra un error técnico ni una página en blanco
```

**Verificado**: ✅ confirmado contra `upload/` — la búsqueda vacía muestra el mensaje "no
product that match" de forma controlada, sin error.

---

## Historia de Usuario 3: Ver el detalle completo de un producto

**Como** cliente,
**quiero** ver toda la información relevante de un producto en una sola página,
**para** decidir si quiero comprarlo sin tener que buscar información en otro lugar.

**Prioridad**: Alta
**Requisitos relacionados**: RF-CAT-031 a RF-CAT-055

```gherkin
Escenario: Ver el detalle de un producto disponible
  Dado que el producto "HTC Touch HD" existe y tiene stock
  Cuando accedo a su página de detalle
  Entonces veo su nombre, precio, imágenes, descripción, fabricante y disponibilidad
  Y puedo agregarlo al carrito directamente desde esa página

Escenario: Ver el detalle de un producto sin stock
  Dado que un producto no tiene unidades disponibles
  Cuando accedo a su página de detalle
  Entonces el sistema me indica claramente que no hay stock
  Y no me permite completar la compra de ese producto (salvo que la tienda permita venta sin stock)
```

**Verificado**: ✅ confirmado contra `upload/` — el detalle de producto responde correctamente
y refleja el estado de stock real.

---

## Historia de Usuario 4: Comparar varios productos entre sí

**Como** cliente indeciso entre varias opciones,
**quiero** comparar hasta 4 productos lado a lado,
**para** decidir cuál se ajusta mejor a lo que busco.

**Prioridad**: Media
**Requisitos relacionados**: RF-CAT-056 a RF-CAT-067

```gherkin
Escenario: Agregar productos a comparación y verlos lado a lado
  Dado que agrego 3 productos distintos a mi lista de comparación
  Cuando accedo a la pantalla de comparación
  Entonces veo los 3 productos lado a lado con precio, disponibilidad y características

Escenario: Límite de 4 productos en comparación
  Dado que ya tengo 4 productos en mi lista de comparación
  Cuando intento agregar un quinto producto
  Entonces el sistema reemplaza el producto más antiguo por el nuevo
  Y sigo viendo un máximo de 4 productos en la comparación
```

**Verificado**: ✅ confirmado contra `upload/` — `product/compare` responde correctamente.

---

## Historia de Usuario 5: Explorar productos por marca/fabricante

**Como** cliente que prefiere una marca específica,
**quiero** ver todos los productos de un fabricante en un solo lugar,
**para** encontrar más opciones de la marca que ya conozco y confío.

**Prioridad**: Media
**Requisitos relacionados**: RF-CAT-068 a RF-CAT-076

```gherkin
Escenario: Ver productos de un fabricante específico
  Dado que el fabricante "Apple" tiene productos en la tienda
  Cuando accedo al listado de "Apple" desde la sección de marcas
  Entonces veo únicamente los productos de esa marca
  Y puedo ordenar y paginar ese listado igual que en una categoría normal
```

**Verificado**: ✅ confirmado contra `upload/` — `product/manufacturer` responde correctamente.

---

## Registro de Ejecución

| Historia | Prioridad | Resultado | Evidencia / Incidente |
|---|---|---|---|
| 1. Navegar por categoría | Alta | ✅ Cumple | Verificado contra `upload/` |
| 2. Buscar productos por texto | Alta | ✅ Cumple | Verificado contra `upload/` |
| 3. Ver detalle completo de producto | Alta | ✅ Cumple | Verificado contra `upload/` |
| 4. Comparar productos | Media | ✅ Cumple | Verificado contra `upload/` |
| 5. Explorar por marca/fabricante | Media | ✅ Cumple | Verificado contra `upload/` |
