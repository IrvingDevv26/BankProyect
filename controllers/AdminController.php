<?php
class AdminController
{

    public function index()
    {
        global $pdo;

        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $nombre_admin = $_SESSION['nombre'];

        // 1. Estadísticas Globales
        $stmt_count = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente'");
        $total_usuarios = $stmt_count->fetchColumn();

        // CORRECCIÓN: Ahora sumamos el saldo de la tabla 'cuentas', no 'usuarios'
        $stmt_money = $pdo->query("SELECT SUM(saldo) FROM cuentas");
        $total_dinero = $stmt_money->fetchColumn();

        // 2. Lista de Usuarios (Unimos con cuentas para ver saldo y numero de cuenta)
        $sql_users = "SELECT u.id, u.nombre_completo AS nombre, u.email, u.bloqueado_hasta, c.saldo, c.numero_cuenta 
                      FROM usuarios u 
                      LEFT JOIN cuentas c ON u.id = c.usuario_id 
                      WHERE u.rol != 'admin' 
                      ORDER BY u.id DESC";

        $stmt_users = $pdo->prepare($sql_users);
        $stmt_users->execute();
        $lista_usuarios = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

        require 'views/admin/dashboard.php';
    }

    public function toggleBloqueo()
    {
        global $pdo;
        header('Content-Type: application/json');

        // 1. Seguridad: Solo Admin
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['ok' => false, 'msg' => 'Acceso denegado']);
            exit;
        }

        // 2. Recibir ID
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $data['id'] ?? 0;

        if ($user_id <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
            exit;
        }

        try {
            // 3. Verificar estado actual
            $stmt = $pdo->prepare("SELECT bloqueado_hasta FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $user_id]);
            $actual = $stmt->fetchColumn();

            if ($actual) {
                // ESTÁ BLOQUEADO -> DESBLOQUEAR (NULL)
                $nuevo_estado = NULL;
                $msg = "Usuario desbloqueado correctamente.";
                $accion = "activo";
            } else {
                // ESTÁ ACTIVO -> BLOQUEAR (Fecha futura lejana, ej: año 2099)
                // El PDF habla de bloqueos temporales automáticos, pero el admin tiene poder absoluto.
                $nuevo_estado = '2099-12-31 23:59:59';
                $msg = "Usuario bloqueado permanentemente.";
                $accion = "bloqueado";
            }

            // 4. Aplicar cambio
            $update = $pdo->prepare("UPDATE usuarios SET bloqueado_hasta = :fecha, intentos_fallidos = 0 WHERE id = :id");
            $update->execute(['fecha' => $nuevo_estado, 'id' => $user_id]);

            echo json_encode(['ok' => true, 'msg' => $msg, 'estado' => $accion]);

        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => 'Error de BD']);
        }
    }

    public function auditoria()
    {
        global $pdo;

        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        // Obtener los últimos 50 accesos (Join con usuarios para ver nombres si existen)
        $sql = "SELECT a.*, u.nombre_completo AS nombre 
                FROM auditoria_login a 
                LEFT JOIN usuarios u ON a.usuario_id = u.id 
                ORDER BY a.fecha DESC LIMIT 50";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'views/admin/auditoria.php';
    }

    public function emails()
    {
        global $pdo;
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        // Obtener correos ordenados por fecha
        $stmt = $pdo->query("SELECT * FROM emails_simulados ORDER BY fecha_envio DESC");
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'views/admin/emails.php';
    }
    public function depositar()
    {
        global $pdo;
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['ok' => false, 'msg' => 'Acceso denegado']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $data['id'] ?? 0;
        $monto = $data['monto'] ?? 0;

        if ($monto <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Monto inválido']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // 1. Obtener ID de la cuenta del usuario
            $stmt = $pdo->prepare("SELECT id FROM cuentas WHERE usuario_id = :uid");
            $stmt->execute(['uid' => $user_id]);
            $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cuenta) {
                throw new Exception("El usuario no tiene cuenta asignada");
            }

            // 2. Sumar saldo
            $upd = $pdo->prepare("UPDATE cuentas SET saldo = saldo + :monto WHERE id = :cid");
            $upd->execute(['monto' => $monto, 'cid' => $cuenta['id']]);

            // 3. Registrar transacción (Tipo Depósito)
            // Origen NULL porque es depósito de ventanilla
            $ins = $pdo->prepare("INSERT INTO transacciones (cuenta_origen_id, cuenta_destino_id, monto, tipo_transaccion, descripcion) 
                                  VALUES (NULL, :cid, :monto, 'deposito', 'Depósito en Ventanilla (Admin)')");
            $ins->execute(['cid' => $cuenta['id'], 'monto' => $monto]);

            $pdo->commit();
            echo json_encode(['ok' => true, 'msg' => 'Depósito realizado']);

        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        }
    }

    // --- VISTA CONFIGURACIÓN (Reutilizamos la vista de perfil del cliente pero adaptada) ---
    public function configuracion()
    {
        global $pdo;
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }
        // Usamos la misma vista de perfil porque la lógica es idéntica (cambio de pass)
        // Solo inyectamos los datos del admin
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Podemos crear una vista específica o reutilizar la de perfil.
        // Para rápido y limpio, duplicaremos perfil como admin_config.php en el paso 5.
        require 'views/admin/config.php';
    }

    // --- API CREAR USUARIO (Transacción Compleja) ---
    public function crearUsuario()
    {
        global $pdo;
        header('Content-Type: application/json');

        if ($_SESSION['rol'] !== 'admin')
            exit;

        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Validación básica
        if (empty($nombre) || empty($email) || empty($password)) {
            echo json_encode(['ok' => false, 'msg' => 'Todos los campos son obligatorios']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // 1. Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                throw new Exception("El correo ya está registrado");
            }

            // 2. Insertar Usuario
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, email, password_hash, rol) VALUES (:n, :e, :p, 'cliente')");
            $stmt->execute(['n' => $nombre, 'e' => $email, 'p' => $hash]);
            $user_id = $pdo->lastInsertId();

            // 3. Crear Cuenta Bancaria (Con $0.00 y número aleatorio)
            $numero_cuenta = '4000' . rand(1000000000, 9999999999); // Simula 14 dígitos
            $stmt = $pdo->prepare("INSERT INTO cuentas (usuario_id, numero_cuenta, saldo) VALUES (:uid, :num, 0.00)");
            $stmt->execute(['uid' => $user_id, 'num' => $numero_cuenta]);

            $pdo->commit();
            echo json_encode(['ok' => true, 'msg' => 'Usuario creado']);

        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        }
    }

    // --- API EDITAR USUARIO ---
    public function editarUsuario()
    {
        global $pdo;
        header('Content-Type: application/json');

        if ($_SESSION['rol'] !== 'admin')
            exit;

        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];

        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo = :n, email = :e WHERE id = :id");
            $stmt->execute(['n' => $nombre, 'e' => $email, 'id' => $id]);
            echo json_encode(['ok' => true]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => 'Error al actualizar']);
        }
    }
}
?>