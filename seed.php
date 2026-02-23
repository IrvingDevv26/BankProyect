<?php
// seed.php - Script para poblar la base de datos con datos de prueba
require_once 'config/database.php';

use Config\ConexionBD;

echo "<h1>🌱 Iniciando reinicio de datos de prueba...</h1>";

try {
    $db = new ConexionBD();
    $pdo = $db->obtenerConexion();

    // 1. Limpiar datos antiguos
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE transacciones");
    $pdo->exec("TRUNCATE TABLE cuentas");
    $pdo->exec("TRUNCATE TABLE usuarios");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "<p>✅ Base de datos limpiada.</p>";

    // 2. Generar Hash seguro
    // La contraseña será '12345678'
    $password_hash = password_hash('12345678', PASSWORD_DEFAULT);

    // 3. Insertar USUARIOS
    $sql_usuarios = "INSERT INTO usuarios (nombre_completo, email, password_hash, rol, estatus) VALUES (:nombre, :email, :pass, :rol, :estatus)";
    $stmt = $pdo->prepare($sql_usuarios);

    // Usuario 1
    $stmt->execute([
        'nombre' => 'Irving Dávila',
        'email' => 'irving@banco.com',
        'pass' => $password_hash,
        'rol' => 'admin',
        'estatus' => 'activo'
    ]);
    $id_usuario1 = $pdo->lastInsertId();

    // Usuario 2
    $stmt->execute([
        'nombre' => 'Usuario Prueba',
        'email' => 'test@banco.com',
        'pass' => $password_hash,
        'rol' => 'cliente',
        'estatus' => 'activo'
    ]);
    $id_usuario2 = $pdo->lastInsertId();

    echo "<p>✅ Usuarios creados (Password: 12345678).</p>";

    // 4. Insertar CUENTAS BANCARIAS
    $sql_cuentas = "INSERT INTO cuentas (usuario_id, numero_cuenta, tipo_cuenta, saldo, estatus) VALUES (:uid, :num, :tipo, :saldo, :estatus)";
    $stmt_cuenta = $pdo->prepare($sql_cuentas);

    // Cuenta Usuario 1
    $stmt_cuenta->execute([
        'uid' => $id_usuario1,
        'num' => '2026100150',
        'tipo' => 'ahorro',
        'saldo' => 5000.00,
        'estatus' => 'activa'
    ]);
    $id_cuenta1 = $pdo->lastInsertId();

    // Cuenta Usuario 2
    $stmt_cuenta->execute([
        'uid' => $id_usuario2,
        'num' => '2026100260',
        'tipo' => 'corriente',
        'saldo' => 10000.00,
        'estatus' => 'activa'
    ]);
    $id_cuenta2 = $pdo->lastInsertId();

    echo "<p>✅ Cuentas bancarias asignadas.</p>";

    // 5. Insertar TRANSACCIONES DE PRUEBA
    $sql_trans = "INSERT INTO transacciones (cuenta_origen_id, cuenta_destino_id, tipo_transaccion, monto, descripcion, estado) 
                  VALUES (:origen, :destino, :tipo, :monto, :desc, :estado)";
    $stmt_trans = $pdo->prepare($sql_trans);

    $stmt_trans->execute([
        'origen' => $id_cuenta2,
        'destino' => $id_cuenta1,
        'tipo' => 'transferencia',
        'monto' => 500.00,
        'desc' => 'Pago de prueba',
        'estado' => 'completada'
    ]);

    echo "<p>✅ Transacciones de prueba creadas.</p>";

    echo "<hr>";
    echo "<h3>🚀 ¡Todo listo!</h3>";
    echo "<p>Usuario 1: <b>irving@banco.com</b> / 12345678</p>";
    echo "<p>Usuario 2: <b>test@banco.com</b> / 12345678</p>";

    // Detectar protocolo para el enlace
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Asumimos que seed.php está en la raíz, igual que el login
    $url_login = $protocol . $host . dirname($_SERVER['PHP_SELF']) . '/index.php'; // Ajustar si es necesario

    // Simplificación: enlace relativo
    echo "<br><a href='index.php'>Ir al Login</a>";

} catch (PDOException $e) {
    die("❌ Error Fatal: " . $e->getMessage());
} catch (Exception $e) {
    die("❌ Error General: " . $e->getMessage());
}
