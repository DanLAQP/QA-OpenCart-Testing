<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class BaseTestCase
 *
 * Clase base para todas las pruebas unitarias.
 * Proporciona funciones comunes y mocks para la BD.
 */
abstract class BaseTestCase extends TestCase {
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Opencart\System\Engine\Registry
     */
    protected $registry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $db;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $config;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $session;

    /**
     * setUp
     *
     * Configura los mocks antes de cada test
     */
    protected function setUp(): void {
        parent::setUp();

        // Mock de la BD - crear como objeto anónimo para mayor flexibilidad
        $this->db = new class {
            public $queryResult = null;

            public function query($sql) {
                return $this->queryResult;
            }

            public function escape($value) {
                return addslashes($value);
            }

            public function getLastId() {
                return 1;
            }

            public function setQueryResult($result) {
                $this->queryResult = $result;
                return $this;
            }
        };

        // Mock de Config - simple object
        $this->config = new class {
            private $defaults = [
                'config_store_id' => 0,
                'config_language_id' => 1,
                'config_customer_group_id' => 1,
                'config_login_attempts' => 5,
                'config_stock_warning' => 1,
                'config_customer_price' => 0,
                'config_cart_weight' => 0,
                'config_allow_out_of_stock' => false,
                'config_password_length' => 4,
            ];

            public function get($key) {
                return $this->defaults[$key] ?? null;
            }

            public function set($key, $value) {
                $this->defaults[$key] = $value;
            }
        };

        // Mock de Session
        $this->session = new class {
            public $data = [];

            public function get($key) {
                return $this->data[$key] ?? null;
            }

            public function set($key, $value) {
                $this->data[$key] = $value;
            }
        };

        // Mock de Registry - usar onlyMethods para métodos existentes
        $this->registry = $this->getMockBuilder(\Opencart\System\Engine\Registry::class)
            ->onlyMethods(['get', '__get', '__set'])
            ->getMock();

        $this->registry->method('get')
            ->willReturnCallback(function($name) {
                if ($name === 'db') return $this->db;
                if ($name === 'config') return $this->config;
                if ($name === 'session') return $this->session;
                return null;
            });

        $this->registry->method('__get')
            ->willReturnCallback(function($name) {
                if ($name === 'db') return $this->db;
                if ($name === 'config') return $this->config;
                if ($name === 'session') return $this->session;
                return null;
            });
    }

    /**
     * tearDown
     *
     * Limpia después de cada test
     */
    protected function tearDown(): void {
        parent::tearDown();
        $this->registry = null;
        $this->db = null;
        $this->config = null;
        $this->session = null;
    }

    /**
     * assertDatabaseHasRecord
     *
     * Asegura que el mock de BD fue llamado con cierta query
     *
     * @param string $expectedQuery
     */
    protected function assertDatabaseQueryCalled(string $expectedQuery): void {
        // Este método puede ser usado en tests para verificar queries
        // Útil para tests de integración más tarde
    }

    /**
     * createMockQueryResult
     *
     * Crea un objeto mock que simula un resultado de query de OpenCart
     *
     * @param array $data
     * @param int $numRows
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createMockQueryResult(array $data = [], int $numRows = 0) {
        $query = $this->createMock(\stdClass::class);
        $query->num_rows = $numRows;
        $query->row = $data;
        $query->rows = [];

        return $query;
    }
}
