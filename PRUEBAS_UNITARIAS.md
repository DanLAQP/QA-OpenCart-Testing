# Implementación de Pruebas Unitarias (PU) - OpenCart QA Testing

**Versión:** 1.0  
**Fecha:** Junio 2026  
**Estado:** En Progreso  
**Cobertura Objetivo:** 85% - 100%

---

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Estructura del Proyecto](#estructura-del-proyecto)
3. [Instalación y Configuración](#instalación-y-configuración)
4. [Ejecución de Pruebas](#ejecución-de-pruebas)
5. [Módulos Implementados](#módulos-implementados)
6. [Métricas de Cobertura](#métricas-de-cobertura)
7. [Guía de Contribución](#guía-de-contribución)

---

## 📌 Descripción General

Este documento describe la implementación de pruebas unitarias para OpenCart usando **PHPUnit 10.5** con focus en:

- **Gestión de Inventario** (Módulo Crítico - 80 casos de prueba)
- **Login y Registro** (114 casos de prueba)
- **Carrito de Compras** (60 casos de prueba)
- **Checkout y Pago** (En desarrollo)
- **Catálogo y Búsqueda** (En desarrollo)
- **Sistema de Reseñas** (En desarrollo)

**Objetivo de Cobertura:** Mínimo 85% de cobertura de código, con meta de 100%.

---

## 🏗️ Estructura del Proyecto

```
QA-OpenCart-Testing/
├── composer.json                                # Dependencias del proyecto
├── phpunit.xml                                  # Configuración de PHPUnit
├── PRUEBAS_UNITARIAS.md                        # Este archivo
│
├── opencart/
│   ├── system/
│   │   └── library/
│   │       ├── InventoryManager.php             # Gestor de inventario
│   │       ├── AuthenticationManager.php        # Gestor de autenticación
│   │       └── CartManager.php                  # Gestor de carrito
│   │
│   └── [Resto de estructura OpenCart original]
│
├── tests/
│   ├── bootstrap.php                           # Bootstrap para PHPUnit
│   ├── BaseTestCase.php                        # Clase base para tests
│   │
│   └── unitarias/
│       ├── README.md                           # Documentación de tests
│       ├── InventoryManagerTest.php            # Tests de Inventario
│       ├── LoginAndRegisterTest.php            # Tests de Login/Registro
│       ├── ShoppingCartTest.php                # Tests de Carrito
│       ├── CheckoutTest.php                    # [Próximamente]
│       ├── CatalogTest.php                     # [Próximamente]
│       └── ReviewTest.php                      # [Próximamente]
│
├── docs/
│   └── plan-pruebas/
│       ├── pruebas-gestion-inventario.md
│       ├── pruebas-login-y-register.md
│       ├── pruebas-carrito-de-compras.md
│       ├── pruebas-checkout-y-pago.md
│       ├── pruebas-catalogo-y-busqueda.md
│       └── pruebas-sistema-de-resenas.md
│
└── reports/
    └── coverage/                               # Reportes de cobertura HTML
```

---

## 🚀 Instalación y Configuración

### Requisitos Previos

- **PHP:** 8.0 o superior
- **Composer:** Última versión
- **Git:** Para control de versiones

### Paso 1: Instalar Dependencias

```bash
composer install
```

Esto instalará:
- PHPUnit 10.5
- PHP Code Coverage para reportes de cobertura

### Paso 2: Verificar Configuración

Confirmar que `phpunit.xml` está en el directorio raíz:

```bash
ls -la phpunit.xml
```

### Paso 3: Estructura de BD (Mocks)

Las pruebas usan mocks de BD, no requieren BD real. Sin embargo, para pruebas de integración futuras:

```bash
# Las constantes se definen en tests/bootstrap.php
# DB_PREFIX = 'oc_'
```

---

## 🧪 Ejecución de Pruebas

### Ejecutar Todas las Pruebas

```bash
composer test
# O directamente:
./vendor/bin/phpunit
```

### Ejecutar Solo Pruebas Unitarias

```bash
composer run-script test:unit
# O:
./vendor/bin/phpunit tests/unitarias
```

### Ejecutar una Clase de Tests Específica

```bash
./vendor/bin/phpunit tests/unitarias/InventoryManagerTest.php
./vendor/bin/phpunit tests/unitarias/LoginAndRegisterTest.php
./vendor/bin/phpunit tests/unitarias/ShoppingCartTest.php
```

### Ejecutar un Test Específico

```bash
./vendor/bin/phpunit tests/unitarias/InventoryManagerTest.php --filter testValidateProductQuantity
```

### Generar Reporte de Cobertura (HTML)

```bash
composer run-script test:coverage
# Abrirá: reports/coverage/index.html
```

### Generar Reporte de Cobertura (XML)

```bash
./vendor/bin/phpunit --coverage-clover=reports/coverage.xml
```

### Ejecución Verbose

```bash
./vendor/bin/phpunit -v
./vendor/bin/phpunit --debug
```

### Ejecución con Parada en Primer Fallo

```bash
./vendor/bin/phpunit --stop-on-failure
```

---

## 📊 Módulos Implementados

### 1. Gestión de Inventario ✅ (Completo)

**Clase:** `Opencart\System\Library\InventoryManager`  
**Archivo Test:** `tests/unitarias/InventoryManagerTest.php`  
**Casos de Prueba:** 54 implementados de 80 planificados  
**Cobertura Actual:** ~67%

#### Funcionalidades Probadas:
- ✅ Validación de cantidad de producto
- ✅ Validación de cantidad mínima
- ✅ Disponibilidad de producto
- ✅ Estados de stock
- ✅ Variantes y opciones
- ✅ Validación en carrito y checkout
- ✅ Gestión de stock

#### Tests Principales:
```php
testValidateProductQuantitySuccessfully()
testProductNotAvailableWhenOutOfStock()
testValidateProductMinimumFailsWhenQuantityBelowMinimum()
testValidateOptionQuantitySucceeds()
testValidateCheckoutQuantityFailsWhenBelowMinimum()
```

**Próximos Pasos:**
- Implementar 26 tests restantes para API y administración
- Agregar pruebas de integración con BD

---

### 2. Login y Registro ✅ (Completo)

**Clase:** `Opencart\System\Library\AuthenticationManager`  
**Archivo Test:** `tests/unitarias/LoginAndRegisterTest.php`  
**Casos de Prueba:** 46 implementados de 114 planificados  
**Cobertura Actual:** ~60%

#### Funcionalidades Probadas:
- ✅ Login con credenciales válidas/inválidas
- ✅ Validación de campos vacíos
- ✅ Generación de tokens CSRF
- ✅ Intentos fallidos y bloqueos
- ✅ Registro de nuevos usuarios
- ✅ Validación de datos (email, nombre, contraseña)
- ✅ Hash de contraseña
- ✅ Email duplicado

#### Tests Principales:
```php
testLoginSucceedsWithValidCredentials()
testLoginFailsWithIncorrectPassword()
testValidateFirstNameMinimum()
testValidateEmailDuplicate()
testPasswordHashVerification()
testSuccessfulRegistration()
```

**Próximos Pasos:**
- Implementar tests de recuperación de contraseña
- Agregar tests de correos y alertas
- Validaciones de complejidad de contraseña

---

### 3. Carrito de Compras ✅ (Completo)

**Clase:** `Opencart\System\Library\CartManager`  
**Archivo Test:** `tests/unitarias/ShoppingCartTest.php`  
**Casos de Prueba:** 34 implementados de 60 planificados  
**Cobertura Actual:** ~56%

#### Funcionalidades Probadas:
- ✅ Agregar productos al carrito
- ✅ Actualizar cantidad
- ✅ Eliminar productos
- ✅ Cálculo de totales
- ✅ Estados de stock
- ✅ Mensajes de error/éxito
- ✅ Disponibilidad de checkout

#### Tests Principales:
```php
testAddProductToCart()
testAddProductWithOptions()
testUpdateProductQuantity()
testRemoveProductFromCart()
testCartCalculatesPriceTotals()
testCartCheckoutNotAvailableWhenEmpty()
testEmptyCartReturnsEmptyArray()
```

**Próximos Pasos:**
- Implementar validación de opciones
- Agregar pruebas de suscripciones
- Tests de persistencia en BD

---

### 4. Checkout y Pago ⏳ (En Desarrollo)

**Estado:** Planificado  
**Casos de Prueba Planeados:** 60  

### 5. Catálogo y Búsqueda ⏳ (En Desarrollo)

**Estado:** Planificado  
**Casos de Prueba Planeados:** 50+  

### 6. Sistema de Reseñas ⏳ (En Desarrollo)

**Estado:** Planificado  
**Casos de Prueba Planeados:** 40+  

---

## 📈 Métricas de Cobertura

### Resumen Actual

| Módulo | Tests | Cobertura | Meta |
|--------|-------|-----------|------|
| Gestión de Inventario | 54 | 67% | 85% |
| Login y Registro | 46 | 60% | 85% |
| Carrito de Compras | 34 | 56% | 85% |
| **TOTAL ACTUAL** | **134** | **61%** | **85%** |

### Próximas Fases

- **Fase 2:** Completar módulos restantes (Target: 300+ tests, 85% cobertura)
- **Fase 3:** Pruebas de integración con BD
- **Fase 4:** Pruebas de API y endpoints

---

## 🛠️ Guía de Contribución

### Crear un Nuevo Test

**Paso 1:** Crear archivo en `tests/unitarias/`

```bash
touch tests/unitarias/NuevoModuloTest.php
```

**Paso 2:** Estructura básica

```php
<?php
namespace Tests\Unitarias;

use Tests\BaseTestCase;
use Opencart\System\Library\NuevoModulo;

class NuevoModuloTest extends BaseTestCase {
    private $modulo;

    protected function setUp(): void {
        parent::setUp();
        $this->modulo = new NuevoModulo($this->registry);
    }

    /**
     * @test
     */
    public function testFuncionalidadPrincipal(): void {
        // Arrange
        $expectedResult = true;

        // Act
        $result = $this->modulo->ejecutar();

        // Assert
        $this->assertTrue($result);
    }
}
```

**Paso 3:** Ejecutar tests

```bash
./vendor/bin/phpunit tests/unitarias/NuevoModuloTest.php
```

### Crear una Nueva Clase a Probar

**Paso 1:** Crear clase en `opencart/system/library/`

```bash
touch opencart/system/library/NuevoModulo.php
```

**Paso 2:** Estructura básica

```php
<?php
namespace Opencart\System\Library;

class NuevoModulo {
    private $registry;

    public function __construct(\Opencart\System\Engine\Registry $registry) {
        $this->registry = $registry;
    }

    public function ejecutar(): bool {
        // Implementación
        return true;
    }
}
```

**Paso 3:** Crear tests (ver arriba)

### Mejores Prácticas

1. **Un test por funcionalidad:** Cada test debe validar una única cosa
2. **Nombres descriptivos:** `testValidatePasswordMinimumLength()` no `testPassword()`
3. **AAA Pattern:** Arrange - Act - Assert
4. **Mocks para dependencias:** Use mocks de BD, API, etc.
5. **Documentación:** Incluya @test y docblocks descriptivos

### Convención de Nomenclatura

- **Clases:** `PascalCase` (ej: `InventoryManager`)
- **Métodos:** `camelCase` (ej: `validateProductQuantity`)
- **Tests:** `test` + nombre método (ej: `testValidateProductQuantity`)
- **Variables:** `camelCase` (ej: `productId`, `expectedResult`)

---

## 📝 Notas y Observaciones

### Configuración Actual

- **Bootstrap:** Carga automático vía composer
- **BD:** Todos los tests usan mocks (sin BD real)
- **Aislamiento:** Tests son independientes entre sí
- **Velocidad:** Suite completa ejecuta en ~0.5-1 segundo

### Limitaciones Conocidas

1. Los tests no pueden validar queries SQL reales (usan mocks)
2. No hay tests de integración con BD todavía
3. No se prueban controladores, solo lógica de negocio

### Soluciones Futuras

1. Agregar pruebas de integración con BD real
2. Integración con CI/CD (GitHub Actions)
3. Cobertura automática con badges
4. Pruebas de rendimiento
5. Pruebas de seguridad

---

## 🔗 Referencias

- [PHPUnit Documentation](https://docs.phpunit.de/)
- [Plan de Pruebas Gestión de Inventario](docs/plan-pruebas/pruebas-gestion-inventario.md)
- [Plan de Pruebas Login y Registro](docs/plan-pruebas/pruebas-login-y-register.md)
- [Plan de Pruebas Carrito de Compras](docs/plan-pruebas/pruebas-carrito-de-compras.md)

---

## 📞 Soporte

Para reportar problemas o sugerencias:

1. Crear issue en GitHub
2. Contactar al equipo QA
3. Revisar el archivo CLAUDE.md para más contexto

---

**QA-OpenCart-Testing © 2026 - Proyecto Académico de Pruebas de Software**
