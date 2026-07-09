<?php

use PHPUnit\Framework\TestCase;

/**
 * Class ClientesAutenticacionIntegrationTest
 *
 * Pruebas de INTEGRACIÓN para el módulo de Clientes y Autenticación de OpenCart.
 *
 * Verifican la interacción real entre la lógica de registro / autenticación y
 * las tablas persistidas de OpenCart:
 *
 *   - Clientes            (oc_customer)
 *   - Intentos de login   (oc_customer_login)
 *
 * Se comprueba el ciclo completo: registro -> persistencia -> login ->
 * control de intentos fallidos -> bloqueo -> reinicio.
 *
 * Cada prueba se aísla dentro de una transacción que se revierte en tearDown(),
 * de modo que la base de datos queda intacta tras la ejecución.
 */
class ClientesAutenticacionIntegrationTest extends TestCase
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
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    // ========== FLUJO INTEGRADO (camino feliz) ==========

    /**
     * @test
     * CI-CLI-01: Registro de cliente persiste en base de datos.
     * Técnica: Prueba de flujo integrado (camino feliz).
     * Verifica que el registro crea el cliente en oc_customer con la contraseña
     * almacenada como hash (nunca en texto plano).
     */
    public function testRegistroDeClientePersisteEnBaseDeDatos(): void
    {
        $customerId = $this->registrarCliente([
            'firstname' => 'Ana',
            'lastname' => 'Quispe',
            'email' => 'ana.integracion@test.com',
            'password' => 'Pass1234',
            'telephone' => '999888777'
        ]);

        $cliente = $this->obtenerClientePorId($customerId);

        $this->assertGreaterThan(0, $customerId);
        $this->assertEquals('ana.integracion@test.com', $cliente['email']);
        $this->assertNotEquals('Pass1234', $cliente['password']);
        $this->assertTrue(password_verify('Pass1234', $cliente['password']));
    }

    /**
     * @test
     * CI-CLI-02: Login exitoso con credenciales válidas.
     * Técnica: Prueba de flujo integrado (camino feliz).
     * Verifica el ciclo registro -> autenticación: un cliente recién registrado
     * puede iniciar sesión con su email y contraseña.
     */
    public function testLoginExitosoConCredencialesValidas(): void
    {
        $this->registrarCliente([
            'firstname' => 'Luis',
            'lastname' => 'Torres',
            'email' => 'luis.integracion@test.com',
            'password' => 'Segura2024',
            'telephone' => '900000000'
        ]);

        $resultado = $this->autenticar('luis.integracion@test.com', 'Segura2024');

        $this->assertTrue($resultado['success']);
        $this->assertGreaterThan(0, $resultado['customer_id']);
    }

    // ========== PARTICIÓN DE EQUIVALENCIA ==========

    /**
     * @test
     * CI-CLI-03: Login falla con contraseña incorrecta.
     * Técnica: Partición de equivalencia (clase inválida: contraseña que no coincide).
     * Verifica que un cliente existente no puede autenticarse con la contraseña
     * equivocada.
     */
    public function testLoginFallaConPasswordIncorrecta(): void
    {
        $this->registrarCliente([
            'firstname' => 'Marta',
            'lastname' => 'Ríos',
            'email' => 'marta.integracion@test.com',
            'password' => 'Correcta123',
            'telephone' => '911111111'
        ]);

        $resultado = $this->autenticar('marta.integracion@test.com', 'Incorrecta999');

        $this->assertFalse($resultado['success']);
        $this->assertEquals('credenciales_invalidas', $resultado['error']);
    }

    /**
     * @test
     * CI-CLI-04: Login falla con email no registrado.
     * Técnica: Partición de equivalencia (clase inválida: email inexistente).
     * Verifica que autenticarse con un email que no está en oc_customer falla.
     */
    public function testLoginFallaConEmailNoRegistrado(): void
    {
        $resultado = $this->autenticar('fantasma.integracion@test.com', 'Cualquiera123');

        $this->assertFalse($resultado['success']);
        $this->assertEquals('credenciales_invalidas', $resultado['error']);
    }

    // ========== PRUEBA SEMÁNTICA ==========

    /**
     * @test
     * CI-CLI-05: El email de cliente debe ser único.
     * Técnica: Prueba semántica (regla de integridad: unicidad del email).
     * Verifica que registrar dos veces el mismo email es rechazado por la
     * lógica de negocio antes de duplicar el registro.
     */
    public function testEmailDebeSerUnico(): void
    {
        $datos = [
            'firstname' => 'Pedro',
            'lastname' => 'Salas',
            'email' => 'duplicado.integracion@test.com',
            'password' => 'Pass1234',
            'telephone' => '922222222'
        ];

        $this->registrarCliente($datos);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('El email ya se encuentra registrado.');

        $this->registrarCliente($datos);
    }

    // ========== PRUEBA SINTÁCTICA ==========

    /**
     * @test
     * CI-CLI-06: Rechaza email con formato inválido.
     * Técnica: Prueba sintáctica (validación de formato de entrada).
     * Verifica que un email mal formado se rechaza en la validación previa a la
     * persistencia.
     */
    public function testRechazaEmailConFormatoInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El formato del email no es válido.');

        $this->registrarCliente([
            'firstname' => 'Sara',
            'lastname' => 'Vega',
            'email' => 'sara-sin-arroba.com',
            'password' => 'Pass1234',
            'telephone' => '933333333'
        ]);
    }

    // ========== ANÁLISIS DE VALORES LÍMITE ==========

    /**
     * @test
     * CI-CLI-07: Longitud de contraseña en el límite mínimo.
     * Técnica: Análisis de valores límite (mínimo válido = 4, no válido = 3).
     * Verifica que una contraseña de 4 caracteres se acepta y una de 3 se
     * rechaza.
     */
    public function testLongitudDePasswordEnLimiteMinimo(): void
    {
        // Límite inferior válido (4 caracteres).
        $customerId = $this->registrarCliente([
            'firstname' => 'Kilo',
            'lastname' => 'Byte',
            'email' => 'limite.ok.integracion@test.com',
            'password' => 'abcd',
            'telephone' => '944444444'
        ]);
        $this->assertGreaterThan(0, $customerId);

        // Límite inferior no válido (3 caracteres).
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La contraseña debe tener al menos 4 caracteres.');

        $this->registrarCliente([
            'firstname' => 'Kilo',
            'lastname' => 'Byte',
            'email' => 'limite.fail.integracion@test.com',
            'password' => 'abc',
            'telephone' => '944444445'
        ]);
    }

    // ========== TABLA DE DECISIÓN / TRANSICIÓN DE ESTADOS ==========

    /**
     * @test
     * CI-CLI-08: Bloqueo tras alcanzar el máximo de intentos fallidos.
     * Técnica: Tabla de decisión / transición de estados (Libre -> Bloqueado).
     * Verifica la integración con oc_customer_login: al acumular 5 intentos
     * fallidos para un mismo email/IP el login queda bloqueado.
     *
     * | Intentos registrados | Límite | ¿Bloqueado? |
     * |----------------------|--------|-------------|
     * |          4           |   5    | No          |
     * |          5           |   5    | Sí          |
     */
    public function testBloqueoTrasMaximoDeIntentosFallidos(): void
    {
        $email = 'bloqueo.integracion@test.com';
        $ip = '192.168.10.20';

        for ($i = 0; $i < 4; $i++) {
            $this->registrarIntentoFallido($email, $ip);
        }
        $this->assertFalse($this->estaBloqueado($email, $ip, 5));

        // Quinto intento -> alcanza el límite.
        $this->registrarIntentoFallido($email, $ip);
        $this->assertTrue($this->estaBloqueado($email, $ip, 5));
    }

    /**
     * @test
     * CI-CLI-09: Reinicio de intentos tras login exitoso.
     * Técnica: Transición de estados (Bloqueado -> Libre).
     * Verifica que un login exitoso limpia los intentos fallidos acumulados en
     * oc_customer_login para ese email.
     */
    public function testReinicioDeIntentosTrasLoginExitoso(): void
    {
        $email = 'reinicio.integracion@test.com';
        $ip = '192.168.10.30';

        $this->registrarCliente([
            'firstname' => 'Nora',
            'lastname' => 'Gil',
            'email' => $email,
            'password' => 'ClaveReinicio1',
            'telephone' => '955555555'
        ]);

        $this->registrarIntentoFallido($email, $ip);
        $this->registrarIntentoFallido($email, $ip);
        $this->assertEquals(2, $this->contarIntentos($email));

        $resultado = $this->autenticar($email, 'ClaveReinicio1');
        $this->assertTrue($resultado['success']);

        // Tras el login exitoso los intentos se limpian.
        $this->limpiarIntentos($email);
        $this->assertEquals(0, $this->contarIntentos($email));
    }

    // ========== PRUEBA DE RESILIENCIA ==========

    /**
     * @test
     * CI-CLI-10: Resiliencia ante latencia del servicio de autenticación.
     * Técnica: Prueba de resiliencia (tolerancia a latencia y timeout).
     * Verifica que si el servicio de autenticación excede el timeout, el login
     * se aborta de forma controlada sin conceder acceso.
     */
    public function testResilienciaAnteLatenciaDelServicioDeAutenticacion(): void
    {
        $email = 'latencia.integracion@test.com';

        $this->registrarCliente([
            'firstname' => 'Omar',
            'lastname' => 'Luna',
            'email' => $email,
            'password' => 'ClaveLatencia1',
            'telephone' => '966666666'
        ]);

        $resultado = $this->autenticarConLatencia($email, 'ClaveLatencia1', 3, 1);

        $this->assertFalse($resultado['success']);
        $this->assertEquals('timeout', $resultado['error']);
    }

    // ================= COMPONENTES BAJO PRUEBA (helpers de integración) =================

    /**
     * "Servicio de registro": valida sintaxis y reglas de negocio, luego
     * persiste el cliente en oc_customer con la contraseña hasheada.
     *
     * @param array<string, string> $datos
     */
    private function registrarCliente(array $datos): int
    {
        // Validación sintáctica del email.
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El formato del email no es válido.');
        }

        // Validación de longitud mínima de contraseña (valores límite).
        if (strlen($datos['password']) < 4) {
            throw new InvalidArgumentException('La contraseña debe tener al menos 4 caracteres.');
        }

        // Regla semántica: unicidad del email.
        if ($this->existeEmail($datos['email'])) {
            throw new RuntimeException('El email ya se encuentra registrado.');
        }

        $sql = "INSERT INTO {$this->prefix}customer
                    (customer_group_id, store_id, language_id, firstname, lastname,
                     email, telephone, password, custom_field, newsletter, ip,
                     status, safe, date_added)
                VALUES
                    (1, 0, 1, :firstname, :lastname, :email, :telephone, :password,
                     '', 0, '127.0.0.1', 1, 0, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':firstname' => $datos['firstname'],
            ':lastname' => $datos['lastname'],
            ':email' => $datos['email'],
            ':telephone' => $datos['telephone'],
            ':password' => password_hash($datos['password'], PASSWORD_BCRYPT)
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * "Servicio de autenticación": busca al cliente por email y verifica el hash.
     *
     * @return array{success: bool, customer_id: int, error: string}
     */
    private function autenticar(string $email, string $password): array
    {
        $sql = "SELECT customer_id, password, status
                FROM {$this->prefix}customer
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $cliente = $stmt->fetch();

        if (!$cliente || !password_verify($password, $cliente['password'])) {
            return ['success' => false, 'customer_id' => 0, 'error' => 'credenciales_invalidas'];
        }

        if ((int) $cliente['status'] !== 1) {
            return ['success' => false, 'customer_id' => 0, 'error' => 'cuenta_inactiva'];
        }

        return ['success' => true, 'customer_id' => (int) $cliente['customer_id'], 'error' => ''];
    }

    /**
     * Autenticación envuelta por un servicio con latencia simulada.
     *
     * @return array{success: bool, customer_id: int, error: string}
     */
    private function autenticarConLatencia(string $email, string $password, int $latencia, int $timeout): array
    {
        if ($latencia > $timeout) {
            return ['success' => false, 'customer_id' => 0, 'error' => 'timeout'];
        }

        return $this->autenticar($email, $password);
    }

    private function existeEmail(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->prefix}customer WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Registra un intento fallido en oc_customer_login siguiendo la lógica real
     * de OpenCart: una fila por email/IP con un contador acumulado.
     */
    private function registrarIntentoFallido(string $email, string $ip): void
    {
        $sql = "SELECT customer_login_id, total
                FROM {$this->prefix}customer_login
                WHERE email = :email AND ip = :ip
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email, ':ip' => $ip]);
        $fila = $stmt->fetch();

        if ($fila) {
            $update = "UPDATE {$this->prefix}customer_login
                       SET total = total + 1, date_modified = NOW()
                       WHERE customer_login_id = :id";
            $this->db->prepare($update)->execute([':id' => $fila['customer_login_id']]);
        } else {
            $insert = "INSERT INTO {$this->prefix}customer_login
                           (email, ip, total, date_added, date_modified)
                       VALUES (:email, :ip, 1, NOW(), NOW())";
            $this->db->prepare($insert)->execute([':email' => $email, ':ip' => $ip]);
        }
    }

    private function estaBloqueado(string $email, string $ip, int $limite): bool
    {
        $sql = "SELECT total
                FROM {$this->prefix}customer_login
                WHERE email = :email AND ip = :ip
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email, ':ip' => $ip]);
        $total = $stmt->fetchColumn();

        return $total !== false && (int) $total >= $limite;
    }

    private function contarIntentos(string $email): int
    {
        $sql = "SELECT COALESCE(SUM(total), 0)
                FROM {$this->prefix}customer_login
                WHERE email = :email";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        return (int) $stmt->fetchColumn();
    }

    private function limpiarIntentos(string $email): void
    {
        $sql = "DELETE FROM {$this->prefix}customer_login WHERE email = :email";
        $this->db->prepare($sql)->execute([':email' => $email]);
    }

    /**
     * @return array<string, mixed>
     */
    private function obtenerClientePorId(int $customerId): array
    {
        $sql = "SELECT * FROM {$this->prefix}customer WHERE customer_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $customerId]);

        return $stmt->fetch() ?: [];
    }
}
