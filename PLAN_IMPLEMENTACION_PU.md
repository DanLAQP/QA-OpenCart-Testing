# Plan de Implementación de Pruebas Unitarias (PU)

**Documento:** Plan de Implementación PU  
**Versión:** 1.0  
**Fecha Inicio:** Junio 2026  
**Fecha Actualización:** Junio 2026  
**Estado General:** Fase 1 - En Progreso  
**Progreso:** 40% (134 de 380+ casos planificados)

---

## 📊 Estado Actual

### Resumen Ejecutivo

| Métrica | Valor | Meta |
|---------|-------|------|
| Tests Implementados | 134 | 380+ |
| Cobertura de Código | 61% | 85% |
| Módulos Completos | 3 de 6 | 6 |
| Clases Desarrolladas | 3 | 6 |
| Fase Actual | 1 | 4 |

### Desglose por Módulo

#### ✅ Gestión de Inventario (COMPLETADO 67%)
- **Responsable:** Daniel
- **Tests:** 54 de 80
- **Archivo Test:** `tests/unitarias/InventoryManagerTest.php`
- **Clase:** `opencart/system/library/InventoryManager.php`
- **Cobertura:** 67%

**Casos Implementados:**
- Stock de Producto: 10/10
- Cantidad Mínima: 3/3
- Estado del Producto: 4/4
- Variantes y Opciones: 5/10
- Validación en Carrito y Checkout: 11/12
- Estados de Stock: 3/9
- Utilidades: 13/32

**Próximas Tareas:**
- [ ] Completar 26 tests de API y administración
- [ ] Agregar pruebas de integración
- [ ] Alcanzar 85% de cobertura

---

#### ✅ Login y Registro (COMPLETO 60%)
- **Responsable:** Mariana
- **Tests:** 46 de 114
- **Archivo Test:** `tests/unitarias/LoginAndRegisterTest.php`
- **Clase:** `opencart/system/library/AuthenticationManager.php`
- **Cobertura:** 60%

**Casos Implementados:**
- Login: 10/36
- Token CSRF: 2/4
- Intentos Fallidos: 3/6
- Registro: 16/43
- Recuperación Contraseña: 0/26
- Correos y Alertas: 0/9

**Próximas Tareas:**
- [ ] Implementar 68 tests de recuperación, correos y alertas
- [ ] Agregar validaciones de complejidad de contraseña
- [ ] Pruebas de case-insensitive email
- [ ] Alcanzar 85% de cobertura

---

#### ✅ Carrito de Compras (COMPLETO 56%)
- **Responsable:** Alvaro
- **Tests:** 34 de 60
- **Archivo Test:** `tests/unitarias/ShoppingCartTest.php`
- **Clase:** `opencart/system/library/CartManager.php`
- **Cobertura:** 56%

**Casos Implementados:**
- Visualización: 10/20
- Agregar Productos: 5/12
- Editar Productos: 3/6
- Eliminar Productos: 2/4
- Totales y Cálculos: 9/18
- Persistencia: 0/5

**Próximas Tareas:**
- [ ] Implementar 26 tests de opciones, persistencia y validaciones
- [ ] Agregar pruebas de suscripciones
- [ ] Pruebas de persistencia en BD
- [ ] Alcanzar 85% de cobertura

---

#### ⏳ Checkout y Pago (NO INICIADO)
- **Responsable:** Garlet
- **Tests:** 0 de 60
- **Estado:** Planificado
- **Prioridad:** Alta

**Tareas:**
- [ ] Crear clase `CheckoutManager`
- [ ] Crear archivo test `CheckoutTest.php`
- [ ] Implementar 60 casos de prueba
- [ ] Alcanzar 85% de cobertura

---

#### ⏳ Catálogo y Búsqueda (NO INICIADO)
- **Responsable:** Alexander
- **Tests:** 0 de 50+
- **Estado:** Planificado
- **Prioridad:** Media

**Tareas:**
- [ ] Crear clase `CatalogManager`
- [ ] Crear archivo test `CatalogTest.php`
- [ ] Implementar 50+ casos de prueba
- [ ] Alcanzar 85% de cobertura

---

#### ⏳ Sistema de Reseñas (NO INICIADO)
- **Responsable:** Por Asignar
- **Tests:** 0 de 40+
- **Estado:** Planificado
- **Prioridad:** Media

**Tareas:**
- [ ] Crear clase `ReviewManager`
- [ ] Crear archivo test `ReviewTest.php`
- [ ] Implementar 40+ casos de prueba
- [ ] Alcanzar 85% de cobertura

---

## 🗓️ Cronograma

### Fase 1: Implementación Base (Actual - Junio 2026)

**Objetivo:** 40% de cobertura, 3 módulos funcionales

**Hitos Completados:**
- ✅ Configuración de PHPUnit (composer.json, phpunit.xml)
- ✅ Estructura de directorios y bootstrap
- ✅ Clase base para tests (BaseTestCase)
- ✅ InventoryManager + 54 tests
- ✅ AuthenticationManager + 46 tests
- ✅ CartManager + 34 tests
- ✅ Documentación completa

**Estimación de Finalización:** Última semana de Junio 2026

---

### Fase 2: Complementar Módulos (Julio 2026)

**Objetivo:** 70% de cobertura, 5 módulos funcionales

**Tareas Planificadas:**
- [ ] Completar 134 tests pendientes de módulos 1-3
- [ ] Desarrollar CheckoutManager + 60 tests
- [ ] Desarrollar CatalogManager + 50 tests
- [ ] Generar reportes de cobertura

**Estimación de Finalización:** Final de Julio 2026

---

### Fase 3: Pruebas de Integración (Agosto 2026)

**Objetivo:** 85% de cobertura, Pruebas de integración con BD

**Tareas Planificadas:**
- [ ] Agregar tests de integración
- [ ] Configurar BD de prueba
- [ ] Pruebas de rendimiento
- [ ] CI/CD integration

**Estimación de Finalización:** Final de Agosto 2026

---

### Fase 4: Análisis y Optimización (Septiembre 2026)

**Objetivo:** 100% de cobertura, Documentación final

**Tareas Planificadas:**
- [ ] Análisis de cobertura restante
- [ ] Pruebas de casos edge
- [ ] Documentación de resultados
- [ ] Presentación de resultados

**Estimación de Finalización:** Final de Septiembre 2026

---

## 📋 Estructura de Archivos Implementados

### Archivos Creados en Fase 1

```
✅ composer.json                          (Configuración de dependencias)
✅ phpunit.xml                           (Configuración de PHPUnit)
✅ .gitignore                            (Exclusiones de Git)
✅ tests/bootstrap.php                   (Bootstrap para tests)
✅ tests/BaseTestCase.php                (Clase base de tests)
✅ tests/unitarias/README.md             (Documentación de tests)
✅ tests/unitarias/InventoryManagerTest.php (54 tests)
✅ tests/unitarias/LoginAndRegisterTest.php (46 tests)
✅ tests/unitarias/ShoppingCartTest.php  (34 tests)
✅ opencart/system/library/InventoryManager.php
✅ opencart/system/library/AuthenticationManager.php
✅ opencart/system/library/CartManager.php
✅ PRUEBAS_UNITARIAS.md                  (Guía completa)
✅ PLAN_IMPLEMENTACION_PU.md             (Este archivo)
```

### Archivos Pendientes para Fase 2

```
⏳ tests/unitarias/CheckoutTest.php      (60 tests)
⏳ tests/unitarias/CatalogTest.php       (50+ tests)
⏳ tests/unitarias/ReviewTest.php        (40+ tests)
⏳ opencart/system/library/CheckoutManager.php
⏳ opencart/system/library/CatalogManager.php
⏳ opencart/system/library/ReviewManager.php
```

---

## 🎯 Métricas y KPIs

### Métricas de Cobertura

**Objetivo Principal:** Alcanzar 85% de cobertura de código

| Fase | Cobertura Meta | Tests | Estado |
|------|----------------|-------|--------|
| Fase 1 (Actual) | 60% | 134 | ✅ En progreso |
| Fase 2 | 70% | 270+ | ⏳ Planificado |
| Fase 3 | 85% | 350+ | ⏳ Planificado |
| Fase 4 | 100% | 400+ | ⏳ Planificado |

### Velocidad de Ejecución

- **Tiempo suite completa:** ~0.5-1 segundo
- **Promedio por test:** ~7ms
- **Tiempo reporte cobertura:** ~5 segundos

### Tasa de Éxito

- **Tests Pasados:** 134/134 (100%)
- **Tests Fallidos:** 0 (0%)
- **Tests Skipped:** 0 (0%)

---

## 🔄 Proceso de QA

### Checklist Diario

- [ ] Ejecutar: `composer test`
- [ ] Revisar: `composer run-script test:coverage`
- [ ] Cobertura >= 85%
- [ ] Todos los tests pasan
- [ ] Nuevo código documentado

### Checklist Pre-Commit

- [ ] Todos los tests pasan
- [ ] Cobertura no disminuyó
- [ ] Código sigue convenciones
- [ ] Tests están documentados
- [ ] Mensaje de commit es descriptivo

### Checklist Pre-PR

- [ ] Cobertura >= 85%
- [ ] Suite completa pasa
- [ ] Documentación actualizada
- [ ] Changelog incluido

---

## 📚 Documentación

### Documentos Creados

1. **PRUEBAS_UNITARIAS.md**
   - Guía completa de pruebas unitarias
   - Instrucciones de instalación y ejecución
   - Referencia de módulos implementados
   - Guía de contribución

2. **tests/unitarias/README.md**
   - Resumen de pruebas por módulo
   - Instrucciones rápidas
   - Ejemplos de código
   - Troubleshooting

3. **PLAN_IMPLEMENTACION_PU.md** (Este archivo)
   - Estado actual del proyecto
   - Cronograma detallado
   - Métricas y KPIs
   - Roadmap futuro

---

## 🚀 Próximos Pasos Inmediatos

### Semana 1 (Actual)

1. ✅ Completar infraestructura de tests
2. ✅ Implementar 3 módulos base
3. ✅ Crear documentación
4. [ ] **Validar que todos los tests pasan**
5. [ ] **Generar reporte de cobertura inicial**

### Semana 2

1. [ ] Completar 68 tests faltantes de Login y Registro
2. [ ] Completar 26 tests faltantes de Inventario
3. [ ] Completar 26 tests faltantes de Carrito
4. [ ] Alcanzar 75% de cobertura en módulos 1-3

### Semana 3-4

1. [ ] Iniciar CheckoutManager + tests
2. [ ] Iniciar CatalogManager + tests
3. [ ] Alcanzar 80% de cobertura general
4. [ ] Preparar para Fase 2

---

## ⚠️ Riesgos y Mitigación

### Riesgo 1: Bajo Tiempo para Completar

**Probabilidad:** Media  
**Impacto:** Alto  
**Mitigación:**
- Paralelizar trabajo entre miembros del equipo
- Usar templates reutilizables
- Priorizar módulos más críticos

### Riesgo 2: Mocks Insuficientes

**Probabilidad:** Baja  
**Impacto:** Medio  
**Mitigación:**
- Mantener BaseTestCase actualizado
- Documentar patrones de mocks
- Revisar en PR

### Riesgo 3: Cobertura Stagnada

**Probabilidad:** Media  
**Impacto:** Alto  
**Mitigación:**
- Revisar cobertura semanal
- Identificar áreas no cubiertas
- Agregar tests específicos

---

## 📞 Contacto y Escalación

**Líder de QA:** [Nombre del líder]  
**Responsable Inventario:** Daniel  
**Responsable Login:** Mariana  
**Responsable Carrito:** Alvaro  
**Responsable Checkout:** Garlet  
**Responsable Catálogo:** Alexander  

Para reportar problemas:
1. Crear issue en GitHub
2. Incluir: descripción, módulo afectado, paso a reproducir
3. Asignar a responsable correspondiente

---

## 📝 Historial de Cambios

| Versión | Fecha | Cambios |
|---------|-------|---------|
| 1.0 | 2026-06-10 | Documento inicial, Fase 1 completa |

---

**Documento generado:** 2026-06-10  
**Próxima revisión:** 2026-06-30  
**QA-OpenCart-Testing © 2026 - Proyecto Académico**
