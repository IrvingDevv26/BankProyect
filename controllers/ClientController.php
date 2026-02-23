<?php
class ClientController
{

    public function index()
    {
        global $pdo;
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
            header("Location: index.php?action=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // 1. Obtener datos del USUARIO
        $stmt = $pdo->prepare("SELECT *, nombre_completo AS nombre FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        // 1. Obtener Cuenta y Saldo
        $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE usuario_id = :uid");
        $stmt->execute(['uid' => $user_id]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Lógica del Buscador (CORREGIDA PARA EVITAR ERROR HY093)
        $busqueda = $_GET['q'] ?? '';

        // Usamos nombres únicos (:cid1 y :cid2) aunque sea el mismo valor
        $sql = "SELECT * FROM transacciones 
                WHERE (cuenta_origen_id = :cid1 OR cuenta_destino_id = :cid2)";

        $params = [
            'cid1' => $cuenta['id'],
            'cid2' => $cuenta['id']
        ];

        $sql = "SELECT t.*, 
                c_origen.numero_cuenta as cuenta_origen, 
                c_destino.numero_cuenta as cuenta_destino
                FROM transacciones t
                LEFT JOIN cuentas c_origen ON t.cuenta_origen_id = c_origen.id
                LEFT JOIN cuentas c_destino ON t.cuenta_destino_id = c_destino.id
                WHERE t.cuenta_origen_id = :id_origen OR t.cuenta_destino_id = :id_destino 
                ORDER BY t.fecha_transaccion DESC LIMIT 10";
        if (!empty($busqueda)) {
            // También usamos nombres únicos para la búsqueda
            $sql .= " AND (descripcion LIKE :busqueda1 OR tipo LIKE :busqueda2)";
            $params['busqueda1'] = "%$busqueda%";
            $params['busqueda2'] = "%$busqueda%";
        }

        $sql .= " ORDER BY fecha DESC LIMIT 10";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'views/client/dashboard.php';
    }

    public function transferir()
    {
        global $pdo;
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
            echo json_encode(['ok' => false, 'msg' => 'Acceso no autorizado']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $email_destino = $data['email'] ?? '';
        $monto = floatval($data['monto'] ?? 0);
        $user_id = $_SESSION['user_id'];

        if ($monto <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Monto inválido']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // 1. Obtener MI cuenta origen (y bloquear fila para evitar doble gasto)
            // Asumimos cuenta tipo 'ahorro' o la primera que encuentre
            $stmt = $pdo->prepare("SELECT id, saldo FROM cuentas WHERE usuario_id = :id LIMIT 1 FOR UPDATE");
            $stmt->execute(['id' => $user_id]);
            $cuenta_origen = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cuenta_origen)
                throw new Exception("No tienes una cuenta activa.");
            if ($cuenta_origen['saldo'] < $monto)
                throw new Exception("Saldo insuficiente.");

            // 2. Buscar cuenta DESTINO a partir del email
            // Primero buscamos al usuario dueño del email
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email_destino]);
            $usuario_destino = $stmt->fetchColumn();

            if (!$usuario_destino)
                throw new Exception("El usuario destino no existe.");
            if ($usuario_destino == $user_id)
                throw new Exception("No puedes transferirte a ti mismo.");

            // Luego buscamos su cuenta principal
            $stmt = $pdo->prepare("SELECT id FROM cuentas WHERE usuario_id = :id LIMIT 1");
            $stmt->execute(['id' => $usuario_destino]);
            $cuenta_destino_id = $stmt->fetchColumn();

            if (!$cuenta_destino_id)
                throw new Exception("El destinatario no tiene cuenta bancaria activa.");

            // 3. Ejecutar Movimientos (ACID)
            // Restar al origen
            $stmt = $pdo->prepare("UPDATE cuentas SET saldo = saldo - :monto WHERE id = :id");
            $stmt->execute(['monto' => $monto, 'id' => $cuenta_origen['id']]);

            // Sumar al destino
            $stmt = $pdo->prepare("UPDATE cuentas SET saldo = saldo + :monto WHERE id = :id");
            $stmt->execute(['monto' => $monto, 'id' => $cuenta_destino_id]);

            // Registrar transacción
            $stmt = $pdo->prepare("INSERT INTO transacciones 
                (cuenta_origen_id, cuenta_destino_id, monto, tipo_transaccion, descripcion) 
                VALUES (:origen, :destino, :monto, 'transferencia', :desc)");

            $stmt->execute([
                'origen' => $cuenta_origen['id'],
                'destino' => $cuenta_destino_id,
                'monto' => $monto,
                'desc' => "Transferencia a $email_destino"
            ]);

            $pdo->commit();

            echo json_encode([
                'ok' => true,
                'msg' => 'Transferencia exitosa',
                'nuevo_saldo' => $cuenta_origen['saldo'] - $monto
            ]);

        } catch (Exception $e) {
            if ($pdo->inTransaction())
                $pdo->rollBack();
            echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        }
    }

    // Muestra la vista de "Mis Tarjetas"
    public function tarjetas()
    {
        global $pdo;

        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
            header("Location: index.php?action=login");
            exit;
        }

        // Obtener datos de la cuenta para pintarlos en la tarjeta
        $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE usuario_id = :id LIMIT 1");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no tiene cuenta, datos dummy
        if (!$cuenta) {
            $cuenta = ['numero_cuenta' => '0000000000', 'estado' => 1, 'saldo' => 0];
        } else {
            $cuenta['estado'] = ($cuenta['estatus'] === 'activa') ? 1 : 0;
        }

        require 'views/client/tarjetas.php';
    }

    // API JSON para congelar/descongelar
    public function toggleTarjeta()
    {
        global $pdo;
        header('Content-Type: application/json');

        // 1. Validar sesión
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
            echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
            exit;
        }

        try {
            // 2. Obtener estado actual
            $stmt = $pdo->prepare("SELECT id, estatus FROM cuentas WHERE usuario_id = :id LIMIT 1");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cuenta) {
                echo json_encode(['ok' => false, 'msg' => 'No se encontró cuenta']);
                exit;
            }

            // 3. Cambiar estado (De activa a bloqueada y viceversa)
            $nuevo_estatus = ($cuenta['estatus'] === 'activa') ? 'bloqueada' : 'activa';

            $update = $pdo->prepare("UPDATE cuentas SET estatus = :estatus WHERE id = :id");
            $update->execute(['estatus' => $nuevo_estatus, 'id' => $cuenta['id']]);

            echo json_encode([
                'ok' => true,
                'msg' => ($nuevo_estatus === 'activa') ? 'Tarjeta activada' : 'Tarjeta congelada',
                'nuevo_estado' => ($nuevo_estatus === 'activa') ? 1 : 0
            ]);

        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => 'Error BD']);
        }
    }

    public function reportes()
    {
        global $pdo;

        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
            header("Location: index.php?action=login");
            exit;
        }

        // 1. Obtener cuenta (necesaria para el encabezado del PDF)
        $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE usuario_id = :id LIMIT 1");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Filtro de Fechas (Por defecto: Mes actual)
        $mes = $_GET['mes'] ?? date('m');
        $anio = $_GET['anio'] ?? date('Y');

        // 3. Consulta Filtrada
        // Buscamos transacciones donde el mes y año coincidan con la fecha de la transacción
        $sql = "SELECT t.*, 
                c_origen.numero_cuenta as cuenta_origen, 
                c_destino.numero_cuenta as cuenta_destino
                FROM transacciones t
                LEFT JOIN cuentas c_origen ON t.cuenta_origen_id = c_origen.id
                LEFT JOIN cuentas c_destino ON t.cuenta_destino_id = c_destino.id
                WHERE (t.cuenta_origen_id = :id1 OR t.cuenta_destino_id = :id2)
                AND MONTH(t.fecha_transaccion) = :mes AND YEAR(t.fecha_transaccion) = :anio
                ORDER BY t.fecha_transaccion DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id1' => $cuenta['id'],
            'id2' => $cuenta['id'],
            'mes' => $mes,
            'anio' => $anio
        ]);
        $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'views/client/reportes.php';
    }
    // Mostrar vista perfil
    public function perfil()
    {
        global $pdo;
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        // Obtener datos frescos del usuario
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        require 'views/client/perfil.php';
    }

    // API para cambiar password
    public function cambiarPassword()
    {
        global $pdo;
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['ok' => false, 'msg' => 'Sesión expirada']);
            exit;
        }

        $current = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';

        try {
            // 1. Verificar contraseña actual
            $stmt = $pdo->prepare("SELECT password_hash FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $hash_actual = $stmt->fetchColumn();

            if (!password_verify($current, $hash_actual)) {
                echo json_encode(['ok' => false, 'msg' => 'La contraseña actual es incorrecta.']);
                exit;
            }

            // 2. Actualizar a la nueva (Hasheada)
            $nuevo_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE usuarios SET password_hash = :p WHERE id = :id");
            $update->execute(['p' => $nuevo_hash, 'id' => $_SESSION['user_id']]);

            echo json_encode(['ok' => true, 'msg' => 'Éxito']);

        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => 'Error de servidor']);
        }
    }
}
?>