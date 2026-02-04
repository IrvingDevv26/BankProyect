<?php
// seed.php - VERSIÓN CORREGIDA
require 'config/db.php';

echo "<h1>🌱 Iniciando reinicio de datos de prueba...</h1>";

try {
    // 1. Limpiar datos antiguos
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE transacciones");
    $pdo->exec("TRUNCATE TABLE cuentas");
    $pdo->exec("TRUNCATE TABLE usuarios");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "<p>✅ Base de datos limpiada.</p>";

    // 2. Generar Hash seguro
    $password_segura = password_hash('12345', PASSWORD_BCRYPT);

    // 3. Insertar USUARIOS (Uno por uno para evitar error de parámetros)

    // Usuario Admin
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :pass, :rol)");
    $stmt->execute([
        'nombre' => 'Administrador',
        'email' => 'admin@banco.com',
        'pass' => $password_segura,
        'rol' => 'admin'
    ]);

    // Usuario Cliente
    $stmt->execute([
        'nombre' => 'Irving Cliente',
        'email' => 'cliente@banco.com',
        'pass' => $password_segura,
        'rol' => 'cliente'
    ]);

    // Obtenemos los IDs (Asumimos 1 y 2 porque acabamos de limpiar la tabla)
    $id_cliente = 2;

    echo "<p>✅ Usuarios creados con contraseña encriptada.</p>";

    // 4. Insertar CUENTAS BANCARIAS
    $stmt_cuenta = $pdo->prepare("INSERT INTO cuentas (usuario_id, numero_cuenta, saldo, tipo_cuenta) VALUES (:uid, '20260001', 5000.00, 'ahorro')");
    $stmt_cuenta->execute(['uid' => $id_cliente]);

    echo "<p>✅ Cuentas bancarias asignadas.</p>";

    echo "<hr>";
    echo "<h3>🚀 ¡Todo listo!</h3>";
    echo "<p>Usuario Admin: <b>admin@banco.com</b> / 12345</p>";
    echo "<p>Usuario Cliente: <b>cliente@banco.com</b> / 12345</p>";
    echo "<br><a href='index.php'>Ir al Login</a>";

} catch (PDOException $e) {
    die("❌ Error Fatal: " . $e->getMessage());
}
?>