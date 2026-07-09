<?php

use PHPUnit\Framework\TestCase;

class SistemaResenasIntegrationTest extends TestCase
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
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        $this->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    /** @test CI-REV-01 */
    public function testRegistroDeResenaValidaSePersiste(): void
    {
        $productId = $this->obtenerProductoActivoId();

        $reviewId = $this->registrarResena($productId, 'Carlos', str_repeat('Muy buen producto. ', 2), 5, 1);

        $resena = $this->obtenerResena($reviewId);

        $this->assertGreaterThan(0, $reviewId);
        $this->assertSame($productId, (int) $resena['product_id']);
        $this->assertSame(5, (int) $resena['rating']);
    }

    /** @test CI-REV-02 */
    public function testSoloResenasAprobadasSeListanEnFrontend(): void
    {
        $productId = $this->obtenerProductoActivoId();

        $this->registrarResena($productId, 'Aprobada', str_repeat('Texto válido ', 3), 4, 1);
        $this->registrarResena($productId, 'Pendiente', str_repeat('Texto válido ', 3), 3, 0);

        $publicadas = $this->listarResenasPublicadas($productId);

        $this->assertCount(1, $publicadas);
        $this->assertSame('Aprobada', $publicadas[0]['author']);
    }

    /** @test CI-REV-03 */
    public function testConteoDeResenasConsideraSoloAprobadas(): void
    {
        $productId = $this->obtenerProductoActivoId();

        $this->registrarResena($productId, 'Uno', str_repeat('Texto válido ', 3), 5, 1);
        $this->registrarResena($productId, 'Dos', str_repeat('Texto válido ', 3), 4, 1);
        $this->registrarResena($productId, 'Tres', str_repeat('Texto válido ', 3), 2, 0);

        $this->assertSame(2, $this->contarResenasPublicadas($productId));
    }

    /** @test CI-REV-04 */
    public function testAutorDemasiadoCortoEsRechazado(): void
    {
        $productId = $this->obtenerProductoActivoId();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El autor debe tener entre 3 y 25 caracteres.');

        $this->registrarResena($productId, 'Al', str_repeat('Texto válido ', 3), 5, 1);
    }

    /** @test CI-REV-05 */
    public function testRatingFueraDeRangoEsRechazado(): void
    {
        $productId = $this->obtenerProductoActivoId();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El rating debe estar entre 1 y 5.');

        $this->registrarResena($productId, 'María', str_repeat('Texto válido ', 3), 6, 1);
    }

    private function obtenerProductoActivoId(): int
    {
        $sql = "SELECT product_id FROM {$this->prefix}product WHERE status = 1 LIMIT 1";
        $productId = $this->db->query($sql)->fetchColumn();

        if (!$productId) {
            $this->fail('No existe un producto activo para registrar reseñas.');
        }

        return (int) $productId;
    }

    private function registrarResena(int $productId, string $author, string $text, int $rating, int $status): int
    {
        if (mb_strlen($author) < 3 || mb_strlen($author) > 25) {
            throw new InvalidArgumentException('El autor debe tener entre 3 y 25 caracteres.');
        }

        if (mb_strlen($text) < 25 || mb_strlen($text) > 1000) {
            throw new InvalidArgumentException('La reseña debe tener entre 25 y 1000 caracteres.');
        }

        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('El rating debe estar entre 1 y 5.');
        }

        $sql = "INSERT INTO {$this->prefix}review (product_id, author, text, rating, status, date_added, date_modified)
                VALUES (:product_id, :author, :text, :rating, :status, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':product_id' => $productId,
            ':author' => $author,
            ':text' => $text,
            ':rating' => $rating,
            ':status' => $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    private function obtenerResena(int $reviewId): array
    {
        $sql = "SELECT review_id, product_id, author, rating, status
                FROM {$this->prefix}review
                WHERE review_id = :review_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':review_id' => $reviewId]);
        $review = $stmt->fetch();

        if (!$review) {
            $this->fail('No se encontró la reseña registrada.');
        }

        return $review;
    }

    private function listarResenasPublicadas(int $productId): array
    {
        $sql = "SELECT author, rating, text
                FROM {$this->prefix}review
                WHERE product_id = :product_id AND status = 1
                ORDER BY date_added DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':product_id' => $productId]);

        return $stmt->fetchAll();
    }

    private function contarResenasPublicadas(int $productId): int
    {
        $sql = "SELECT COUNT(*)
                FROM {$this->prefix}review
                WHERE product_id = :product_id AND status = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':product_id' => $productId]);

        return (int) $stmt->fetchColumn();
    }
}
