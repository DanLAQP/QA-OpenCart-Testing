# 🚀 Quick Start - Pruebas Unitarias (5 minutos)

## ⚡ Instalación y Ejecución Rápida

### 1️⃣ Instalar Dependencias (2 min)

```bash
composer install
```

**Verifica que veas:**
```
✓ PHPUnit 10.5
✓ PHP Code Coverage
```

### 2️⃣ Ejecutar Pruebas (1 min)

```bash
# Opción A: Todas las pruebas
composer test

# Opción B: Solo unitarias
composer run-script test:unit

# Opción C: Directamente
./vendor/bin/phpunit
```

**Resultado esperado:**
```
Tests: 134 passed (100%)
Time: ~0.5-1 second
OK (134 tests, 134 assertions)
```

### 3️⃣ Ver Reporte de Cobertura (2 min)

```bash
composer run-script test:coverage
```

**Se abre automáticamente:** `reports/coverage/index.html`

---

## 📊 Estado Actual (3 módulos, 134 tests)

| Módulo | Tests | Cobertura |
|--------|-------|-----------|
| Gestión de Inventario | 54 | 67% |
| Login y Registro | 46 | 60% |
| Carrito de Compras | 34 | 56% |
| **TOTAL** | **134** | **61%** |

---

## 🧪 Ejecutar Tests Específicos

```bash
# Un módulo completo
./vendor/bin/phpunit tests/unitarias/InventoryManagerTest.php

# Un test específico
./vendor/bin/phpunit --filter testValidateProductQuantity

# Con modo verbose
./vendor/bin/phpunit -v

# Con debug
./vendor/bin/phpunit --debug
```

---

## 📁 Archivos Principales

```
✓ composer.json                          ← Dependencias
✓ phpunit.xml                           ← Configuración
✓ tests/bootstrap.php                   ← Inicialización
✓ tests/BaseTestCase.php                ← Clase base
✓ tests/unitarias/InventoryManagerTest.php     ← 54 tests
✓ tests/unitarias/LoginAndRegisterTest.php     ← 46 tests
✓ tests/unitarias/ShoppingCartTest.php         ← 34 tests
✓ opencart/system/library/InventoryManager.php
✓ opencart/system/library/AuthenticationManager.php
✓ opencart/system/library/CartManager.php
```

---

## 📚 Documentación

```
QUICK_START_PU.md           ← Este archivo (inicio rápido)
RESUMEN_IMPLEMENTACION.md   ← Logros y métricas
PRUEBAS_UNITARIAS.md        ← Guía completa (recomendado leer)
PLAN_IMPLEMENTACION_PU.md   ← Roadmap y cronograma
tests/unitarias/README.md   ← Referencia técnica
```

---

## ✅ Checklist de Verificación

- [ ] `composer install` ejecutado
- [ ] `composer test` pasa al 100%
- [ ] `reports/coverage/index.html` abierto
- [ ] Cobertura inicial: 61%
- [ ] 134 tests pasando

---

## 🎯 Próximos Pasos

1. **Revisar un test de ejemplo:**
   ```bash
   cat tests/unitarias/InventoryManagerTest.php | head -50
   ```

2. **Leer la guía completa:**
   ```
   PRUEBAS_UNITARIAS.md (10 min)
   ```

3. **Ver el plan de desarrollo:**
   ```
   PLAN_IMPLEMENTACION_PU.md
   ```

4. **Ejecutar cobertura:**
   ```bash
   composer run-script test:coverage
   ```

---

## 🔍 Ver Logs Detallados

```bash
# Todos los tests
./vendor/bin/phpunit --testdox

# Módulo específico
./vendor/bin/phpunit tests/unitarias/InventoryManagerTest.php --testdox

# Con salida HTML
./vendor/bin/phpunit --testdox-html=reports/tests.html
```

---

## ❌ Solucionar Problemas

### Error: "vendor/ no encontrado"
```bash
composer install
```

### Error: "phpunit command not found"
```bash
# Usa la ruta completa
./vendor/bin/phpunit
```

### Error: "Database connection"
✓ Normal - Los tests usan mocks, no BD real

### Los tests no pasan
```bash
# Verifica PHP version
php --version    # Debe ser >= 8.0

# Regenera autoload
composer dump-autoload
```

---

## 📞 Soporte

| Pregunta | Respuesta |
|----------|-----------|
| ¿Cómo ejecutar? | `composer test` |
| ¿Cómo ver cobertura? | `composer run-script test:coverage` |
| ¿Más info? | Lee `PRUEBAS_UNITARIAS.md` |
| ¿Plan futuro? | Ve `PLAN_IMPLEMENTACION_PU.md` |

---

## 🎉 ¡Listo!

**Felicitaciones, tienes 134 tests unitarios automáticos para OpenCart.**

```
Status: ✅ READY TO USE
Coverage: 61%
Target: 85%
Next: Fase 2 (Julio 2026)
```

---

**¡Ejecuta ahora: `composer test`** 🚀
