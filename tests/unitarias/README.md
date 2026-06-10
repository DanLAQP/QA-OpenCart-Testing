# Pruebas Unitarias - OpenCart QA Testing

Bienvenido a la suite de pruebas unitarias para OpenCart. Este directorio contiene todas las pruebas automatizadas para los módulos críticos de OpenCart.

## 📂 Contenido del Directorio

```
unitarias/
├── README.md                        # Este archivo
├── InventoryManagerTest.php         # Pruebas de Gestión de Inventario
├── LoginAndRegisterTest.php         # Pruebas de Login y Registro
├── ShoppingCartTest.php             # Pruebas de Carrito de Compras
├── CheckoutTest.php                 # [Próximamente]
├── CatalogTest.php                  # [Próximamente]
└── ReviewTest.php                   # [Próximamente]
```

## 🧪 Resumen de Pruebas

| Módulo | Archivo Test | Tests | Casos Planeados |
|--------|--------------|-------|-----------------|
| Gestión de Inventario | InventoryManagerTest.php | 54 | 80 |
| Login y Registro | LoginAndRegisterTest.php | 46 | 114 |
| Carrito de Compras | ShoppingCartTest.php | 34 | 60 |
| Checkout | CheckoutTest.php | ⏳ | 60+ |
| Catálogo | CatalogTest.php | ⏳ | 50+ |
| Reseñas | ReviewTest.php | ⏳ | 40+ |

## 🚀 Inicio Rápido

### Ejecutar Todas las Pruebas

```bash
composer test
```

### Ejecutar Pruebas de un Módulo

```bash
./vendor/bin/phpunit tests/unitarias/InventoryManagerTest.php
./vendor/bin/phpunit tests/unitarias/LoginAndRegisterTest.php
./vendor/bin/phpunit tests/unitarias/ShoppingCartTest.php
```

### Ver Reporte de Cobertura

```bash
composer run-script test:coverage
# Abre: reports/coverage/index.html
```

## 📋 Detalles de Cada Módulo

### 1. Gestión de Inventario

**Archivo:** `InventoryManagerTest.php`  
**Clase Testeada:** `Opencart\System\Library\InventoryManager`  
**Funcionalidades:**
- Validación de cantidad de producto
- Validación de cantidad mínima
- Disponibilidad de producto
- Estados de stock
- Variantes y opciones
- Validación en carrito y checkout

**Ejemplo:**
```php
public function testValidateProductQuantityFailsWhenAllowOutOfStockDisabled(): void
```

---

### 2. Login y Registro

**Archivo:** `LoginAndRegisterTest.php`  
**Clase Testeada:** `Opencart\System\Library\AuthenticationManager`  
**Funcionalidades:**
- Login con credenciales
- Validación de campos
- Generación de tokens CSRF
- Intentos fallidos
- Bloqueo de cuenta
- Registro de usuarios
- Validación de email y contraseña
- Hash de contraseña

**Ejemplo:**
```php
public function testLoginSucceedsWithValidCredentials(): void
```

---

### 3. Carrito de Compras

**Archivo:** `ShoppingCartTest.php`  
**Clase Testeada:** `Opencart\System\Library\CartManager`  
**Funcionalidades:**
- Agregar productos
- Actualizar cantidad
- Eliminar productos
- Cálculo de totales
- Estados de stock
- Mensajes de error/éxito
- Disponibilidad de checkout

**Ejemplo:**
```php
public function testAddProductToCart(): void
```

---

## 🔧 Estructura de un Test

Cada test sigue el patrón **AAA (Arrange-Act-Assert)**:

```php
/**
 * @test
 * Descripción del caso de prueba
 */
public function testNombreDelTest(): void {
    // ARRANGE: Preparar datos
    $productData = ['product_id' => 1, 'quantity' => 50];
    $mockQuery = $this->createMockQueryResult($productData, 1);
    $this->db->method('query')->willReturn($mockQuery);

    // ACT: Ejecutar la funcionalidad
    $stock = $this->inventoryManager->getProductStock(1);

    // ASSERT: Validar resultado
    $this->assertEquals(50, $stock['quantity']);
}
```

## 📊 Métodos Disponibles en BaseTestCase

### Mocks Precargados

```php
$this->db        // Mock de base de datos
$this->config    // Mock de configuración
$this->session   // Mock de sesión
$this->registry  // Mock del registry
```

### Métodos Útiles

```php
// Crear un resultado mock de query
$mockQuery = $this->createMockQueryResult($data, $numRows);

// Assertions estándar
$this->assertTrue($result);
$this->assertFalse($result);
$this->assertEquals($expected, $actual);
$this->assertArrayHasKey('key', $array);
$this->assertIsArray($result);
```

## 📝 Convenciones

### Nomenclatura

- **Clases Test:** `NombreTest` (ej: `InventoryManagerTest`)
- **Métodos Test:** `testNombreFuncionalidad` (ej: `testValidateProductQuantity`)
- **Variables:** `camelCase` (ej: `$productData`, `$expectedResult`)

### Documentación

Cada test debe incluir:

```php
/**
 * @test
 * CASO-ID: Descripción del caso de prueba
 * Verifica que...
 */
public function testNombre(): void {
```

### Organización

Los tests se agrupan por funcionalidad con comentarios:

```php
// ========== PRUEBAS: Categoría Principal (RF-MOD-001 al RF-MOD-010) ==========
```

## ✅ Checklist antes de Commit

- [ ] Todos los tests pasan: `composer test`
- [ ] Cobertura es >= 85%: `composer run-script test:coverage`
- [ ] Naming sigue convenciones
- [ ] Tests están documentados
- [ ] No hay warnings de PHP

## 🐛 Solucionar Problemas

### Tests fallan en Windows

Si los tests fallan por rutas, usar:
```bash
./vendor/bin/phpunit tests/unitarias --colors=always
```

### Autoload issues

Asegúrate que composer.json está correctamente configurado:
```bash
composer dump-autoload
```

### BD no encontrada

Los tests usan mocks, no requieren BD. Si hay error:
```bash
# Revisar que bootstrap.php define DB_PREFIX
grep "DB_PREFIX" tests/bootstrap.php
```

## 🎯 Próximos Pasos

1. ✅ Completar tests de módulos restantes
2. ⏳ Agregar pruebas de integración
3. ⏳ Integración con CI/CD
4. ⏳ Análisis de cobertura automático

## 📚 Recursos

- [PRUEBAS_UNITARIAS.md](../../PRUEBAS_UNITARIAS.md) - Guía completa
- [Plan de Pruebas Inventario](../../docs/plan-pruebas/pruebas-gestion-inventario.md)
- [Plan de Pruebas Login](../../docs/plan-pruebas/pruebas-login-y-register.md)
- [Plan de Pruebas Carrito](../../docs/plan-pruebas/pruebas-carrito-de-compras.md)

## 👥 Contacto

Para preguntas o contribuciones, contactar al equipo QA.

---

**¡Gracias por contribuir a la calidad de OpenCart!** ✨
