# Pruebas de Integración — Clientes y Autenticación

Archivo de pruebas: [`ClientesAutenticacionIntegrationTest.php`](./ClientesAutenticacionIntegrationTest.php)

## Objetivo

Verificar la **interacción real** entre los procesos de registro y autenticación de
clientes de OpenCart utilizando la base de datos real (sin mocks). La suite valida
el flujo completo **registro → persistencia → autenticación → control de intentos
fallidos → bloqueo → reinicio de intentos**, asegurando que las reglas de negocio
y los mecanismos de seguridad se ejecuten correctamente.

## Componentes / Tablas integradas

| Componente | Tabla OpenCart |
|------------|----------------|
| Clientes | `oc_customer` |
| Intentos de autenticación | `oc_customer_login` |

## Estrategia de aislamiento

Cada prueba se ejecuta dentro de una **transacción** (`beginTransaction()` en `setUp()`)
que se **revierte** (`rollBack()` en `tearDown()`). De esta forma, los cambios
realizados durante la ejecución no afectan permanentemente la base de datos,
garantizando pruebas repetibles, independientes y consistentes.

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
| **CI-CLI-01** | `testRegistroDeClientePersisteEnBaseDeDatos` | Registrar un cliente nuevo verificando que la información quede almacenada correctamente en `oc_customer` y que la contraseña se almacene mediante hash. | **Prueba de flujo integrado** (camino feliz) | El cliente se persiste correctamente; la contraseña no se almacena en texto plano y `password_verify()` valida el hash generado. |
| **CI-CLI-02** | `testLoginExitosoConCredencialesValidas` | Autenticar un cliente previamente registrado utilizando credenciales válidas. | **Prueba de flujo integrado** (camino feliz) | El inicio de sesión es exitoso y se obtiene un `customer_id` válido. |
| **CI-CLI-03** | `testLoginFallaConPasswordIncorrecta` | Intentar iniciar sesión con una contraseña distinta a la registrada. | **Partición de equivalencia** (credencial inválida) | La autenticación es rechazada devolviendo el error `credenciales_invalidas`. |
| **CI-CLI-04** | `testLoginFallaConEmailNoRegistrado` | Intentar autenticarse utilizando un correo electrónico inexistente. | **Partición de equivalencia** (usuario inexistente) | El acceso es rechazado devolviendo el error `credenciales_invalidas`. |
| **CI-CLI-05** | `testEmailDebeSerUnico` | Registrar dos clientes utilizando el mismo correo electrónico. | **Prueba semántica** (regla de integridad: unicidad del email) | El segundo registro genera una `RuntimeException` indicando que el correo ya se encuentra registrado. |
| **CI-CLI-06** | `testRechazaEmailConFormatoInvalido` | Intentar registrar un cliente utilizando un correo electrónico con formato inválido. | **Prueba sintáctica** (validación del formato de entrada) | Se lanza una `InvalidArgumentException` antes de realizar operaciones sobre la base de datos. |
| **CI-CLI-07** | `testLongitudDePasswordEnLimiteMinimo` | Validar la longitud mínima permitida para la contraseña comparando un caso válido y uno inválido. | **Análisis de valores límite** (mínimo válido = 4 caracteres) | Una contraseña de 4 caracteres se acepta; una de 3 caracteres produce una `InvalidArgumentException`. |
| **CI-CLI-08** | `testBloqueoTrasMaximoDeIntentosFallidos` | Registrar múltiples intentos fallidos de autenticación para comprobar el mecanismo de bloqueo. | **Tabla de decisión / Transición de estados** (Libre → Bloqueado) | Con 4 intentos el usuario permanece habilitado; al quinto intento se activa el bloqueo. |
| **CI-CLI-09** | `testReinicioDeIntentosTrasLoginExitoso` | Verificar que un inicio de sesión exitoso elimine los intentos fallidos previamente registrados. | **Transición de estados** (Bloqueado → Libre) | El contador de intentos almacenado en `oc_customer_login` vuelve a cero después de autenticarse correctamente. |
| **CI-CLI-10** | `testResilienciaAnteLatenciaDelServicioDeAutenticacion` | Simular una demora superior al tiempo permitido durante el proceso de autenticación. | **Prueba de resiliencia** (tolerancia a latencia / timeout) | El inicio de sesión finaliza con error `timeout` y el acceso no es concedido. |

---

## Tabla de decisión (CI-CLI-08)

| Intentos registrados | Límite permitido | ¿Usuario bloqueado? |
|----------------------|------------------|---------------------|
| 4 | 5 |  No |
| 5 | 5 |  Sí |

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