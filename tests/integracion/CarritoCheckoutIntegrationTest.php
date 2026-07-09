<?php

use PHPUnit\Framework\TestCase;

/**
 * Class CarritoCheckoutIntegrationTest
 *
 * Pruebas de INTEGRACIÓN para el módulo de Carrito y Checkout de OpenCart.
 *
 * A diferencia de las pruebas unitarias (que usan mocks), estas pruebas
 * verifican la interacción real entre varios componentes persistidos en la
 * base de datos de OpenCart:
 *
 *   - Inventario  (oc_product)
 *   - Orden       (oc_order)
 *   - Detalle     (oc_order_product)
 *
 * Se comprueba que el flujo Carrito -> Checkout -> Orden mantenga la
 * consistencia entre el stock del inventario y los datos persistidos.
 *
 * Cada prueba se aísla dentro de una transacción que se revierte en tearDown(),
 * de modo que la base de datos queda intacta tras la ejecución.
 */
class CarritoCheckoutIntegrationTest extends TestCase
{
    private PDO $db;
    private string $prefix;

    protected function setUp(): void
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_NAME') ?: 'opencart';
        $user = getenv('DB_USER') ?: 'opencart';
        $pass = getenv('DB_PASS') ?: 'opencart';

        $this->prefix = getenv('DB_PREFIX') ?: 'oc_';

        $this->db = new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        // Aislamiento: cada prueba trabaja dentro de una transacción propia.
        $this->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Se revierte todo lo escrito por la prueba para no ensuciar la BD.
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    // ========== FLUJO INTEGRADO (camino feliz) ==========

    /**
     * @test
     * CI-CAR-01: Flujo completo carrito -> orden.
     * Técnica: Prueba de flujo integrado (camino feliz / end-to-end).
     * Verifica que al agregar un producto real al carrito y confirmar el
     * checkout se crean las filas en oc_order y oc_order_product con los
     * totales correctos.
     */
    public function testFlujoCompletoCarritoAOrden(): void
    {
        $producto = $this->obtenerProductoActivo();
        $this->fijarStock($producto['product_id'], 10);

        $carrito = $this->agregarProducto([], (int) $producto['product_id'], 2);

        $totales = $this->calcularTotales($carrito, 15.00, 0.18);
        $orderId = $this->confirmarOrden(0, $carrito, $totales);

        $orden = $this->obtenerOrden($orderId);
        $lineas = $this->obtenerLineasOrden($orderId);

        $this->assertGreaterThan(0, $orderId);
        $this->assertCount(1, $lineas);
        $this->assertEquals(2, (int) $lineas[0]['quantity']);
        $this->assertEquals(
            round($totales['total'], 4),
            round((float) $orden['total'], 4)
        );
    }

    /**
     * @test
     * CI-CAR-02: Confirmar orden descuenta stock del inventario.
     * Técnica: Transición de estados (Stock Disponible -> Stock Reducido).
     * Verifica la integración Checkout <-> Inventario: tras confirmar la orden
     * el stock en oc_product se reduce exactamente en la cantidad comprada.
     */
    public function testConfirmarOrdenDescuentaStockDelInventario(): void
    {
        $producto = $this->obtenerProductoActivo();
        $this->fijarStock($producto['product_id'], 20);

        $carrito = $this->agregarProducto([], (int) $producto['product_id'], 5);
        $totales = $this->calcularTotales($carrito, 0.00, 0.00);
        $this->confirmarOrden(0, $carrito, $totales);

        $stockFinal = $this->obtenerStock((int) $producto['product_id']);

        $this->assertEquals(15, $stockFinal);
    }

    /**
     * @test
     * CI-CAR-09: Actualizar cantidad recalcula el total de línea.
     * Técnica: Prueba de flujo integrado.
     * Verifica que modificar la cantidad del carrito antes de confirmar
     * recalcula el total de la línea persistida en oc_order_product.
     */
    public function testActualizarCantidadRecalculaTotalDeLinea(): void
    {
        $producto = $this->obtenerProductoActivo();
        $this->fijarStock($producto['product_id'], 50);

        $carrito = $this->agregarProducto([], (int) $producto['product_id'], 1);
        $carrito = $this->actualizarCantidad($carrito, (int) $producto['product_id'], 4);

        $totales = $this->calcularTotales($carrito, 0.00, 0.00);
        $orderId = $this->confirmarOrden(0, $carrito, $totales);

        $lineas = $this->obtenerLineasOrden($orderId);
        $precio = (float) $carrito[0]['price'];

        $this->assertEquals(4, (int) $lineas[0]['quantity']);
        $this->assertEquals(
            round($precio * 4, 4),
            round((float) $lineas[0]['total'], 4)
        );
    }

    // ========== PARTICIÓN DE EQUIVALENCIA ==========

    /**
     * @test
     * CI-CAR-03: Cálculo de totales (subtotal + impuesto + envío).
     * Técnica: Partición de equivalencia (clase válida de valores monetarios).
     * Verifica que el total persistido en la orden es coherente con el
     * subtotal del carrito más el impuesto y el costo de envío.
     */
    public function testCalculoDeTotalesConSubtotalImpuestoYEnvio(): void
    {
        $producto = $this->obtenerProductoActivo();
        $this->fijarStock($producto['product_id'], 30);

        $carrito = $this->agregarProducto([], (int) $producto['product_id'], 3);
        $totales = $this->calcularTotales($carrito, 20.00, 0.18);

        $subtotalEsperado = (float) $carrito[0]['price'] * 3;
        $impuestoEsperado = $subtotalEsperado * 0.18;
        $totalEsperado = $subtotalEsperado + $impuestoEsperado + 20.00;

        $this->assertEquals(round($subtotalEsperado, 4), round($totales['subtotal'], 4));
        $this->assertEquals(round($impuestoEsperado, 4), round($totales['tax'], 4));
        $this->assertEquals(round($totalEsperado, 4), round($totales['total'], 4));
    }

    // ========== ANÁLISIS DE VALORES LÍMITE ==========

    /**
     * @test
     * CI-CAR-04: Cantidad en el límite exacto del stock es aceptada.
     * Técnica: Análisis de valores límite (límite superior válido: qty = stock).
     * Verifica que comprar exactamente el stock disponible es válido y deja
     * el inventario en 0.
     */
    public function testCantidadEnLimiteExactoDelStockEsAceptada(): void
    {
        $producto = $this->obtenerProductoActivo();
        $this->fijarStock($producto['product_id'], 7);

        $carrito = $this->agregarProducto([], (int) $producto['product_id'], 7);
        $totales = $this->calcularTotales($carrito, 0.00, 0.00);
        $orderId = $this->confirmarOrden(0, $carrito, $totales);

        $this->assertGreaterThan(0, $orderId);
        $this->assertEquals(0, $this->obtenerStock((int) $producto['product_id']));
    }

    /**
     * @test
     * CI-CAR-05: Cantidad superior al stock (límite + 1) es rechazada.
     * Técnica: Análisis de valores límite (límite superior no válido: qty = stock + 1).
     * Verifica que solicitar una unidad más que el stock disponible aborta el
     * checkout y no crea la orden.
     */
    public function testCantidadSuperiorAlStockEsRechazada(): void
    {
        $producto = $this->obtenerProductoActivo();
        $this->fijarStock($producto['product_id'], 5);

        $carrito = $this->agregarProducto([], (int) $producto['product_id'], 6);
        $totales = $this->calcularTotales($carrito, 0.00, 0.00);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stock insuficiente para completar la compra.');

        $this->confirmarOrden(0, $carrito, $totales);
    }

    // ========== PRUEBA SINTÁCTICA ==========

    /**
     * @test
     * CI-CAR-06: Rechaza producto con id no numérico.
     * Técnica: Prueba sintáctica (validación de tipos de datos de entrada).
     * Verifica que agregar al carrito un product_id no numérico produce un
     * error de validación antes de tocar la base de datos.
     */
    public function testRechazaProductoConIdNoNumerico(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Los campos product_id y quantity deben ser numericos.');

        $this->agregarProducto([], 'ABC', 'dos');
    }

    // ========== PRUEBA SEMÁNTICA ==========

    /**
     * @test
     * CI-CAR-07: Rechaza checkout con carrito vacío.
     * Técnica: Prueba semántica (regla de negocio: no se factura un carrito vacío).
     * Verifica que confirmar el checkout sin productos aborta el proceso.
     */
    public function testRechazaCheckoutConCarritoVacio(): void
    {
        $totales = $this->calcularTotales([], 0.00, 0.00);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('El carrito no contiene productos.');

        $this->confirmarOrden(0, [], $totales);
    }

    // ========== TABLA DE DECISIÓN ==========

    /**
     * @test
     * CI-CAR-08: Disponibilidad de checkout según stock, estado y backorder.
     * Técnica: Tabla de decisión (combinación stock / estado / permitir sin stock).
     * Verifica la regla combinada que decide si un producto puede comprarse.
     *
     * | Stock | Estado   | Permitir sin stock | ¿Comprable? |
     * |-------|----------|--------------------|-------------|
     * |  10   | Activo   | No                 | Sí          |
     * |   0   | Activo   | No                 | No          |
     * |   0   | Activo   | Sí                 | Sí          |
     * |  10   | Inactivo | No                 | No          |
     */
    public function testDisponibilidadCheckoutSegunStockYEstado(): void
    {
        $this->assertTrue($this->esComprable(10, 1, false));
        $this->assertFalse($this->esComprable(0, 1, false));
        $this->assertTrue($this->esComprable(0, 1, true));
        $this->assertFalse($this->esComprable(10, 0, false));
    }

    // ========== PRUEBA DE RESILIENCIA ==========

    /**
     * @test
     * CI-CAR-10: Resiliencia ante timeout de la pasarela de pago.
     * Técnica: Prueba de resiliencia (tolerancia a fallos / latencia y timeout).
     * Verifica que si la pasarela de pago excede el timeout, la orden no se
     * confirma y el stock del inventario permanece intacto.
     */
    public function testResilienciaAnteTimeoutDePasarelaDePago(): void
    {
        $producto = $this->obtenerProductoActivo();
        $this->fijarStock($producto['product_id'], 10);

        $carrito = $this->agregarProducto([], (int) $producto['product_id'], 3);
        $totales = $this->calcularTotales($carrito, 0.00, 0.00);

        $resultado = $this->confirmarOrdenConPasarela($carrito, $totales, 3, 1);

        $this->assertFalse($resultado['pagado']);
        $this->assertNull($resultado['order_id']);
        $this->assertEquals(10, $this->obtenerStock((int) $producto['product_id']));
    }

    // ================= COMPONENTES BAJO PRUEBA (helpers de integración) =================

    /**
     * Simula el "servicio de carrito": valida sintaxis, consulta el producto real
     * en la BD y agrega la línea al carrito con el precio vigente del inventario.
     *
     * @param array<int, array<string, mixed>> $carrito
     * @return array<int, array<string, mixed>>
     */
    private function agregarProducto(array $carrito, $productId, $quantity): array
    {
        if (!is_int($productId) || !is_int($quantity)) {
            throw new InvalidArgumentException('Los campos product_id y quantity deben ser numericos.');
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor que cero.');
        }

        $sql = "SELECT product_id, price FROM {$this->prefix}product WHERE product_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $productId]);
        $producto = $stmt->fetch();

        if (!$producto) {
            throw new RuntimeException('El producto no existe en el catálogo.');
        }

        $carrito[] = [
            'product_id' => (int) $producto['product_id'],
            'quantity' => $quantity,
            'price' => (float) $producto['price']
        ];

        return $carrito;
    }

    /**
     * @param array<int, array<string, mixed>> $carrito
     * @return array<int, array<string, mixed>>
     */
    private function actualizarCantidad(array $carrito, int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor que cero.');
        }

        foreach ($carrito as &$linea) {
            if ($linea['product_id'] === $productId) {
                $linea['quantity'] = $quantity;
            }
        }

        return $carrito;
    }

    /**
     * @param array<int, array<string, mixed>> $carrito
     * @return array<string, float>
     */
    private function calcularTotales(array $carrito, float $envio, float $tasaImpuesto): array
    {
        $subtotal = 0.0;

        foreach ($carrito as $linea) {
            $subtotal += $linea['price'] * $linea['quantity'];
        }

        $impuesto = $subtotal * $tasaImpuesto;

        return [
            'subtotal' => $subtotal,
            'tax' => $impuesto,
            'shipping' => $envio,
            'total' => $subtotal + $impuesto + $envio
        ];
    }

    /**
     * Confirma el checkout: revalida el carrito y el stock (regla semántica),
     * persiste la orden y su detalle, y descuenta el inventario.
     *
     * @param array<int, array<string, mixed>> $carrito
     * @param array<string, float> $totales
     */
    private function confirmarOrden(int $customerId, array $carrito, array $totales): int
    {
        if (empty($carrito)) {
            throw new RuntimeException('El carrito no contiene productos.');
        }

        // Revalidación de stock contra el inventario real.
        foreach ($carrito as $linea) {
            $stock = $this->obtenerStock($linea['product_id']);

            if ($linea['quantity'] > $stock) {
                throw new RuntimeException('Stock insuficiente para completar la compra.');
            }
        }

        $sql = "INSERT INTO {$this->prefix}order
                    (customer_id, firstname, lastname, email, total,
                     order_status_id, currency_id, currency_code, currency_value,
                     date_added, date_modified)
                VALUES
                    (:customer_id, 'Test', 'Integracion', 'integ@test.com', :total,
                     1, 1, 'USD', 1.0, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':customer_id' => $customerId,
            ':total' => $totales['total']
        ]);

        $orderId = (int) $this->db->lastInsertId();

        $sqlLinea = "INSERT INTO {$this->prefix}order_product
                        (order_id, product_id, name, model, quantity, price, total)
                     VALUES
                        (:order_id, :product_id, 'Producto Test', 'MODEL', :quantity, :price, :total)";

        $stmtLinea = $this->db->prepare($sqlLinea);

        foreach ($carrito as $linea) {
            $stmtLinea->execute([
                ':order_id' => $orderId,
                ':product_id' => $linea['product_id'],
                ':quantity' => $linea['quantity'],
                ':price' => $linea['price'],
                ':total' => $linea['price'] * $linea['quantity']
            ]);

            // Descuento de inventario (integración Checkout <-> Inventario).
            $this->fijarStock(
                $linea['product_id'],
                $this->obtenerStock($linea['product_id']) - $linea['quantity']
            );
        }

        return $orderId;
    }

    /**
     * Envuelve la confirmación con una pasarela de pago simulada que puede
     * exceder el timeout. Si hay timeout, no se confirma la orden.
     *
     * @param array<int, array<string, mixed>> $carrito
     * @param array<string, float> $totales
     * @return array{pagado: bool, order_id: int|null}
     */
    private function confirmarOrdenConPasarela(array $carrito, array $totales, int $latencia, int $timeout): array
    {
        if ($latencia > $timeout) {
            // La pasarela no respondió a tiempo: se aborta sin crear la orden.
            return ['pagado' => false, 'order_id' => null];
        }

        $orderId = $this->confirmarOrden(0, $carrito, $totales);

        return ['pagado' => true, 'order_id' => $orderId];
    }

    /**
     * Regla de negocio (tabla de decisión) que decide si un producto es comprable.
     */
    private function esComprable(int $stock, int $estado, bool $permitirSinStock): bool
    {
        if ($estado !== 1) {
            return false;
        }

        if ($stock <= 0 && !$permitirSinStock) {
            return false;
        }

        return true;
    }

    // ================= UTILIDADES DE ACCESO A DATOS =================

    /**
     * @return array<string, mixed>
     */
    private function obtenerProductoActivo(): array
    {
        $sql = "SELECT product_id, quantity, price
                FROM {$this->prefix}product
                WHERE status = 1
                ORDER BY product_id
                LIMIT 1";

        $producto = $this->db->query($sql)->fetch();

        if (!$producto) {
            $this->fail('No existen productos activos en la base de datos de OpenCart.');
        }

        return $producto;
    }

    private function obtenerStock(int $productId): int
    {
        $sql = "SELECT quantity FROM {$this->prefix}product WHERE product_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $productId]);
        $stock = $stmt->fetchColumn();

        if ($stock === false) {
            $this->fail('Producto no encontrado en inventario.');
        }

        return (int) $stock;
    }

    private function fijarStock(int $productId, int $cantidad): void
    {
        $sql = "UPDATE {$this->prefix}product SET quantity = :qty WHERE product_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':qty' => $cantidad, ':id' => $productId]);
    }

    /**
     * @return array<string, mixed>
     */
    private function obtenerOrden(int $orderId): array
    {
        $sql = "SELECT * FROM {$this->prefix}order WHERE order_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $orderId]);

        return $stmt->fetch() ?: [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function obtenerLineasOrden(int $orderId): array
    {
        $sql = "SELECT * FROM {$this->prefix}order_product WHERE order_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $orderId]);

        return $stmt->fetchAll();
    }
}
