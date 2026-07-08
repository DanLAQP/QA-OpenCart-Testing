# Pruebas de Integración — Clientes y Autenticación

Archivo de pruebas: [`ClientesAutenticacionIntegrationTest.php`](./ClientesAutenticacionIntegrationTest.php)

## Objetivo

Verificar la **interacción real** entre la lógica de registro / autenticación y las
tablas persistidas de OpenCart, usando la base de datos real (no mocks). Se cubre el
ciclo completo: **registro → persistencia → login → control de intentos fallidos →
bloqueo → reinicio**.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Clientes | `oc_customer` |
| Intentos de login | `oc_customer_login` |

## Estrategia de aislamiento

Cada prueba se ejecuta dentro de una **transacción** (`beginTransaction()` en `setUp()`)
que se **revierte** (`rollBack()` en `tearDown()`). Así la base de datos queda intacta
tras la ejecución y las pruebas son repetibles e independientes.

## Configuración (variables de entorno)

| Variable | Valor por defecto |
|----------|-------------------|
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `opencart` |
| `DB_USER` | `opencart` |
| `DB_PASS` | `opencart` |
| `DB_PREFIX` | `oc_` |

## Ejecución

```bash
vendor/bin/phpunit tests/integracion/clientes-autenticacion/ClientesAutenticacionIntegrationTest.php
```

---

## Casos de Prueba

| # ID | Método | Descripción | Técnica | Resultado esperado |
|------|--------|-------------|---------|--------------------|
| **CI-CLI-01** | `testRegistroDeClientePersisteEnBaseDeDatos` | El registro crea el cliente en `oc_customer` con la contraseña almacenada como hash. | **Prueba de flujo integrado** (camino feliz) | El cliente se persiste; la contraseña no está en texto plano y `password_verify()` es verdadero. |
| **CI-CLI-02** | `testLoginExitosoConCredencialesValidas` | Ciclo registro → autenticación con email y contraseña correctos. | **Prueba de flujo integrado** (camino feliz) | Login exitoso con `customer_id` válido. |
| **CI-CLI-03** | `testLoginFallaConPasswordIncorrecta` | Cliente existente intenta autenticarse con contraseña equivocada. | **Partición de equivalencia** (clase inválida: contraseña que no coincide) | Login rechazado con error `credenciales_invalidas`. |
| **CI-CLI-04** | `testLoginFallaConEmailNoRegistrado` | Autenticación con un email que no está en `oc_customer`. | **Partición de equivalencia** (clase inválida: email inexistente) | Login rechazado con error `credenciales_invalidas`. |
| **CI-CLI-05** | `testEmailDebeSerUnico` | Registrar dos veces el mismo email. | **Prueba semántica** (regla de integridad: unicidad del email) | El segundo registro lanza `RuntimeException` («El email ya se encuentra registrado.»). |
| **CI-CLI-06** | `testRechazaEmailConFormatoInvalido` | Registrar con un email mal formado (sin `@`). | **Prueba sintáctica** (validación de formato de entrada) | Se lanza `InvalidArgumentException` antes de persistir. |
| **CI-CLI-07** | `testLongitudDePasswordEnLimiteMinimo` | Contraseña de 4 caracteres (válida) vs 3 caracteres (inválida). | **Análisis de valores límite** (mínimo válido = 4, no válido = 3) | 4 caracteres se acepta; 3 caracteres lanza `InvalidArgumentException`. |
| **CI-CLI-08** | `testBloqueoTrasMaximoDeIntentosFallidos` | Acumular intentos fallidos en `oc_customer_login` para un email/IP. | **Tabla de decisión / Transición de estados** (Libre → Bloqueado) | Con 4 intentos: no bloqueado; con 5 (límite): bloqueado. |
| **CI-CLI-09** | `testReinicioDeIntentosTrasLoginExitoso` | Un login exitoso limpia los intentos fallidos acumulados. | **Transición de estados** (Bloqueado → Libre) | Tras el login exitoso, los intentos en `oc_customer_login` quedan en 0. |
| **CI-CLI-10** | `testResilienciaAnteLatenciaDelServicioDeAutenticacion` | El servicio de autenticación excede el timeout. | **Prueba de resiliencia** (tolerancia a latencia / timeout) | El login se aborta de forma controlada con error `timeout`, sin conceder acceso. |

---

## Tabla de decisión (CI-CLI-08)

| Intentos registrados | Límite | ¿Bloqueado? |
|----------------------|--------|-------------|
| 4 | 5 | ❌ No |
| 5 | 5 | ✅ Sí |

---

## Resumen por técnica

| Técnica | Casos |
|---------|-------|
| Prueba de flujo integrado | CI-CLI-01, CI-CLI-02 |
| Partición de equivalencia | CI-CLI-03, CI-CLI-04 |
| Prueba semántica | CI-CLI-05 |
| Prueba sintáctica | CI-CLI-06 |
| Análisis de valores límite | CI-CLI-07 |
| Tabla de decisión / Transición de estados | CI-CLI-08 |
| Transición de estados | CI-CLI-09 |
| Prueba de resiliencia | CI-CLI-10 |
| **Total** | **10 casos** |