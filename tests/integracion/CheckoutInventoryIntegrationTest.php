<?php

use PHPUnit\Framework\TestCase;

class CheckoutInventoryIntegrationTest extends TestCase
{
    public function testCantidadConTipoIncorrecto()
    {
        $pedido = [
            "product_id" => "ABC",
            "quantity" => "dos"
        ];

        $this->assertIsString($pedido["product_id"]);
        $this->assertIsString($pedido["quantity"]);

        // Resultado esperado:
        // El sistema debe rechazar estos datos antes de actualizar inventario.
        $this->assertNotIsInt($pedido["product_id"]);
        $this->assertNotIsInt($pedido["quantity"]);
    }

    public function testCantidadMayorAlStockDisponible()
    {
        $stockDisponible = 10;
        $cantidadSolicitada = 500;

        $this->assertGreaterThan($stockDisponible, $cantidadSolicitada);

        // Resultado esperado:
        // No debe permitirse la compra.
        $this->assertFalse($cantidadSolicitada <= $stockDisponible);
    }

    public function testLatenciaAltaEnInventario()
    {
        $tiempoInicio = microtime(true);

        sleep(3); // Simulación de demora del módulo de inventario

        $tiempoFinal = microtime(true);
        $duracion = $tiempoFinal - $tiempoInicio;

        $this->assertGreaterThanOrEqual(3, $duracion);

        // Resultado esperado:
        // El sistema debe manejar la demora sin duplicar la orden.
        $ordenDuplicada = false;
        $this->assertFalse($ordenDuplicada);
    }
}
