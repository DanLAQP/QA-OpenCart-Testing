# Catálogo de Escenarios de Pruebas de Integración — OpenCart

Este documento registra los **60 escenarios de pruebas de integración** definidos para OpenCart y establece su relación con las pruebas automatizadas existentes.

## Resumen

| Estado | Cantidad |
|---|---:|
| Escenarios diseñados | 60 |
| Escenarios automatizados | 55 |
| Escenarios pendientes de automatización | 5 |
| Assertions de la última ejecución | 111 |
| Errores y fallos | 0 |

La última ejecución automatizada fue realizada mediante GitHub Actions el 19/07/2026. Las 10 suites implementadas finalizaron correctamente.

## Distribución

| Integración | Diseñados | Automatizados | Pendientes |
|---|---:|---:|---:|
| Carrito, checkout e inventario | 15 | 14 | 1 |
| Clientes y autenticación | 11 | 10 | 1 |
| Catálogo y búsqueda | 6 | 5 | 1 |
| Gestión de inventario | 5 | 5 | 0 |
| Sistema de reseñas | 6 | 5 | 1 |
| Pagos | 5 | 4 | 1 |
| Pedidos | 4 | 4 | 0 |
| Envíos | 4 | 4 | 0 |
| Totales y descuentos | 4 | 4 | 0 |
| **Total** | **60** | **55** | **5** |

## Estados utilizados

- **Automatizado:** existe un método PHPUnit que implementa el escenario.
- **Pendiente:** el escenario está diseñado, pero todavía no tiene un método automatizado.
- **Aprobado:** la prueba fue ejecutada correctamente en el workflow de referencia.

## 1. Carrito, checkout e inventario

| ID | Escenario | Archivo automatizado | Método | Estado |
|---|---|---|---|---|
| INT-CAR-001 | Convertir el carrito en una orden persistida | `CarritoCheckoutIntegrationTest.php` | `testFlujoCompletoCarritoAOrden` | ✅ Automatizado |
| INT-CAR-002 | Descontar el stock al confirmar una orden | `CarritoCheckoutIntegrationTest.php` | `testConfirmarOrdenDescuentaStockDelInventario` | ✅ Automatizado |
| INT-CAR-003 | Recalcular el total al modificar una cantidad | `CarritoCheckoutIntegrationTest.php` | `testActualizarCantidadRecalculaTotalDeLinea` | ✅ Automatizado |
| INT-CAR-004 | Calcular subtotal, impuesto y envío | `CarritoCheckoutIntegrationTest.php` | `testCalculoDeTotalesConSubtotalImpuestoYEnvio` | ✅ Automatizado |
| INT-CAR-005 | Aceptar una cantidad igual al stock disponible | `CarritoCheckoutIntegrationTest.php` | `testCantidadEnLimiteExactoDelStockEsAceptada` | ✅ Automatizado |
| INT-CAR-006 | Rechazar una cantidad superior al stock | `CarritoCheckoutIntegrationTest.php` | `testCantidadSuperiorAlStockEsRechazada` | ✅ Automatizado |
| INT-CAR-007 | Rechazar un identificador de producto no numérico | `CarritoCheckoutIntegrationTest.php` | `testRechazaProductoConIdNoNumerico` | ✅ Automatizado |
| INT-CAR-008 | Rechazar el checkout con el carrito vacío | `CarritoCheckoutIntegrationTest.php` | `testRechazaCheckoutConCarritoVacio` | ✅ Automatizado |
| INT-CAR-009 | Determinar la disponibilidad del checkout según stock y estado | `CarritoCheckoutIntegrationTest.php` | `testDisponibilidadCheckoutSegunStockYEstado` | ✅ Automatizado |
| INT-CAR-010 | Manejar un timeout de la pasarela de pago | `CarritoCheckoutIntegrationTest.php` | `testResilienciaAnteTimeoutDePasarelaDePago` | ✅ Automatizado |
| INT-CHK-001 | Actualizar el inventario después del checkout | `CheckoutInventoryIntegrationTest.php` | `testFlujoCheckoutActualizaInventario` | ✅ Automatizado |
| INT-CHK-002 | Rechazar tipos de datos inválidos en el checkout | `CheckoutInventoryIntegrationTest.php` | `testFallaSintacticaConTiposInvalidos` | ✅ Automatizado |
| INT-CHK-003 | Rechazar una compra cuando la cantidad supera el stock | `CheckoutInventoryIntegrationTest.php` | `testFallaSemanticaCantidadMayorAlStock` | ✅ Automatizado |
| INT-CHK-004 | Mantener consistencia ante una latencia elevada | `CheckoutInventoryIntegrationTest.php` | `testResilienciaAnteLatenciaAlta` | ✅ Automatizado |
| INT-CHK-005 | Restaurar el stock cuando la creación de la orden falla | Por implementar | Por implementar | ⏳ Pendiente |

## 2. Clientes y autenticación

| ID | Escenario | Archivo automatizado | Método | Estado |
|---|---|---|---|---|
| INT-AUT-001 | Persistir el registro de un nuevo cliente | `ClientesAutenticacionIntegrationTest.php` | `testRegistroDeClientePersisteEnBaseDeDatos` | ✅ Automatizado |
| INT-AUT-002 | Iniciar sesión con credenciales válidas | `ClientesAutenticacionIntegrationTest.php` | `testLoginExitosoConCredencialesValidas` | ✅ Automatizado |
| INT-AUT-003 | Rechazar una contraseña incorrecta | `ClientesAutenticacionIntegrationTest.php` | `testLoginFallaConPasswordIncorrecta` | ✅ Automatizado |
| INT-AUT-004 | Rechazar un correo no registrado | `ClientesAutenticacionIntegrationTest.php` | `testLoginFallaConEmailNoRegistrado` | ✅ Automatizado |
| INT-AUT-005 | Impedir el registro de correos duplicados | `ClientesAutenticacionIntegrationTest.php` | `testEmailDebeSerUnico` | ✅ Automatizado |
| INT-AUT-006 | Rechazar un correo con formato inválido | `ClientesAutenticacionIntegrationTest.php` | `testRechazaEmailConFormatoInvalido` | ✅ Automatizado |
| INT-AUT-007 | Validar la longitud mínima de la contraseña | `ClientesAutenticacionIntegrationTest.php` | `testLongitudDePasswordEnLimiteMinimo` | ✅ Automatizado |
| INT-AUT-008 | Bloquear el acceso tras el máximo de intentos fallidos | `ClientesAutenticacionIntegrationTest.php` | `testBloqueoTrasMaximoDeIntentosFallidos` | ✅ Automatizado |
| INT-AUT-009 | Reiniciar los intentos fallidos después de un login exitoso | `ClientesAutenticacionIntegrationTest.php` | `testReinicioDeIntentosTrasLoginExitoso` | ✅ Automatizado |
| INT-AUT-010 | Manejar latencia en el servicio de autenticación | `ClientesAutenticacionIntegrationTest.php` | `testResilienciaAnteLatenciaDelServicioDeAutenticacion` | ✅ Automatizado |
| INT-AUT-011 | Conservar el carrito después de iniciar sesión | Por implementar | Por implementar | ⏳ Pendiente |

## 3. Catálogo y búsqueda

| ID | Escenario | Archivo automatizado | Método | Estado |
|---|---|---|---|---|
| INT-CAT-001 | Listar productos pertenecientes a una categoría activa | `CatalogoBusquedaIntegrationTest.php` | `testListaProductosDeUnaCategoriaActiva` | ✅ Automatizado |
| INT-CAT-002 | Buscar productos por nombre | `CatalogoBusquedaIntegrationTest.php` | `testBusquedaPorNombreRetornaCoincidenciasReales` | ✅ Automatizado |
| INT-CAT-003 | Devolver una lista vacía cuando no existen coincidencias | `CatalogoBusquedaIntegrationTest.php` | `testBusquedaSinCoincidenciasRetornaListaVacia` | ✅ Automatizado |
| INT-CAT-004 | Mostrar fabricante y productos relacionados | `CatalogoBusquedaIntegrationTest.php` | `testDetalleDeProductoExponeFabricanteYRelacionadosCuandoExisten` | ✅ Automatizado |
| INT-CAT-005 | Limitar la comparación a cuatro productos | `CatalogoBusquedaIntegrationTest.php` | `testComparacionConservaMaximoDeCuatroProductos` | ✅ Automatizado |
| INT-CAT-006 | Mantener el mismo precio entre búsqueda, detalle y carrito | Por implementar | Por implementar | ⏳ Pendiente |

## 4. Gestión de inventario

| ID | Escenario | Archivo automatizado | Método | Estado |
|---|---|---|---|---|
| INT-INV-001 | Persistir la actualización del stock | `GestionInventarioIntegrationTest.php` | `testActualizacionDeStockSePersisteEnProducto` | ✅ Automatizado |
| INT-INV-002 | Marcar como comprable un producto activo y disponible | `GestionInventarioIntegrationTest.php` | `testProductoActivoYDisponibleSeMarcaComoComprable` | ✅ Automatizado |
| INT-INV-003 | Impedir la compra de un producto con fecha futura | `GestionInventarioIntegrationTest.php` | `testProductoConFechaFuturaNoEsComprable` | ✅ Automatizado |
| INT-INV-004 | Aceptar una cantidad igual al stock disponible | `GestionInventarioIntegrationTest.php` | `testCantidadExactaAlStockDisponibleEsAceptada` | ✅ Automatizado |
| INT-INV-005 | Rechazar una cantidad superior al stock disponible | `GestionInventarioIntegrationTest.php` | `testCantidadSuperiorAlStockDisponibleEsRechazada` | ✅ Automatizado |

## 5. Sistema de reseñas

| ID | Escenario | Archivo automatizado | Método | Estado |
|---|---|---|---|---|
| INT-RES-001 | Persistir una reseña válida | `SistemaResenasIntegrationTest.php` | `testRegistroDeResenaValidaSePersiste` | ✅ Automatizado |
| INT-RES-002 | Mostrar únicamente reseñas aprobadas | `SistemaResenasIntegrationTest.php` | `testSoloResenasAprobadasSeListanEnFrontend` | ✅ Automatizado |
| INT-RES-003 | Contabilizar únicamente reseñas aprobadas | `SistemaResenasIntegrationTest.php` | `testConteoDeResenasConsideraSoloAprobadas` | ✅ Automatizado |
| INT-RES-004 | Rechazar un nombre de autor demasiado corto | `SistemaResenasIntegrationTest.php` | `testAutorDemasiadoCortoEsRechazado` | ✅ Automatizado |
| INT-RES-005 | Rechazar una calificación fuera del rango permitido | `SistemaResenasIntegrationTest.php` | `testRatingFueraDeRangoEsRechazado` | ✅ Automatizado |
| INT-RES-006 | Actualizar el promedio del producto al aprobar una reseña | Por implementar | Por implementar | ⏳ Pendiente |

## 6. Pagos

| ID | Escenario | Archivo automatizado | Método | Estado |
|---|---|---|---|---|
| INT-PAG-001 | Persistir el método de pago seleccionado | `PagoIntegrationTest.php` | `testPersisteMetodoDePagoEnLaOrden` | ✅ Automatizado |
| INT-PAG-002 | Agregar un registro al historial al confirmar el pago | `PagoIntegrationTest.php` | `testConfirmacionDePagoAgregaHistorial` | ✅ Automatizado |
| INT-PAG-003 | Evitar el historial exitoso después de un timeout | `PagoIntegrationTest.php` | `testTimeoutNoAgregaHistorialExitoso` | ✅ Automatizado |
| INT-PAG-004 | Evitar duplicados al reintentar la confirmación | `PagoIntegrationTest.php` | `testReintentoNoDuplicaHistorialEquivalente` | ✅ Automatizado |
| INT-PAG-005 | Impedir la doble confirmación mediante callbacks duplicados | Por implementar | Por implementar | ⏳ Pendiente |

## 7. Pedidos

| ID | Escenario | Archivo automatizado | Método | Estado |
|---|---|---|---|---|
| INT-PED-001 | Crear un pedido con líneas y totales | `PedidoIntegrationTest.php` | `testCreacionCompletaDePedidoConLineasYTotales` | ✅ Automatizado |
| INT-PED-002 | Registrar en el historial el cambio de estado | `PedidoIntegrationTest.php` | `testCambioDeEstadoAgregaHistorial` | ✅ Automatizado |
| INT-PED-003 | Recuperar el historial en orden cronológico | `PedidoIntegrationTest.php` | `testHistorialDelPedidoSeRecuperaEnOrdenCronologico` | ✅ Automatizado |
| INT-PED-004 | Mantener consistencia entre líneas y total del pedido | `PedidoIntegrationTest.php` | `testConsistenciaEntreLineasYTotalRegistrado` | ✅ Automatizado |

## 8. Envíos

| ID | Escenario | Archivo automatizado | Método | Estado |
|---|---|---|---|---|
| INT-ENV-001 | Persistir la dirección y el método de envío | `EnvioIntegrationTest.php` | `testPersisteDireccionYMetodoDeEnvio` | ✅ Automatizado |
| INT-ENV-002 | Registrar la cotización en los totales de la orden | `EnvioIntegrationTest.php` | `testCotizacionSimpleDeEnvioSePersisteEnOrderTotal` | ✅ Automatizado |
| INT-ENV-003 | Actualizar el importe al cambiar el método de envío | `EnvioIntegrationTest.php` | `testCambioDeMetodoDeEnvioActualizaMonto` | ✅ Automatizado |
| INT-ENV-004 | Recalcular el total después de cambiar el envío | `EnvioIntegrationTest.php` | `testRecalculoDeTotalesCuandoCambiaElEnvio` | ✅ Automatizado |

## 9. Totales y descuentos

| ID | Escenario | Archivo automatizado | Método | Estado |
|---|---|---|---|---|
| INT-TOT-001 | Persistir el subtotal y el impuesto | `TotalesDescuentosIntegrationTest.php` | `testPersisteSubtotalEImpuesto` | ✅ Automatizado |
| INT-TOT-002 | Aplicar un descuento mediante cupón | `TotalesDescuentosIntegrationTest.php` | `testAplicaDescuentoPorCupon` | ✅ Automatizado |
| INT-TOT-003 | Aplicar un voucher | `TotalesDescuentosIntegrationTest.php` | `testAplicaVoucher` | ✅ Automatizado |
| INT-TOT-004 | Verificar la consistencia del total final | `TotalesDescuentosIntegrationTest.php` | `testTotalFinalEsConsistenteConTodosLosComponentes` | ✅ Automatizado |

## Escenarios pendientes de automatización

| ID | Método sugerido | Suite de destino |
|---|---|---|
| INT-CHK-005 | `testRestauraStockCuandoFallaCreacionDeOrden` | `CheckoutInventoryIntegrationTest.php` |
| INT-AUT-011 | `testConservaCarritoDespuesDeIniciarSesion` | `ClientesAutenticacionIntegrationTest.php` |
| INT-CAT-006 | `testMantienePrecioEntreBusquedaDetalleYCarrito` | `CatalogoBusquedaIntegrationTest.php` |
| INT-RES-006 | `testActualizaPromedioAlAprobarResena` | `SistemaResenasIntegrationTest.php` |
| INT-PAG-005 | `testCallbackDuplicadoNoConfirmaPagoDosVeces` | `PagoIntegrationTest.php` |

## Relación con el README principal

El archivo principal `tests/integracion/README.md` debe conservar el panorama general, la estrategia de cobertura, las instrucciones de ejecución y el resumen de resultados.

Este catálogo debe ubicarse en:

```text
tests/integracion/escenarios/README.md
```

El README principal puede enlazarlo así:

```markdown
## Catálogo de escenarios

Se diseñaron 60 escenarios de integración: 55 automatizados y 5 pendientes.
Consulta el [catálogo y matriz de trazabilidad](escenarios/README.md).
```

## Criterio de actualización

Cuando se implemente uno de los escenarios pendientes:

1. crear el método PHPUnit sugerido;
2. ejecutar la suite correspondiente;
3. cambiar su estado de `Pendiente` a `Automatizado`;
4. actualizar los totales de este documento;
5. registrar el resultado del workflow.
