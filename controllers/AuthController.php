<?php
class AuthController
{

    public function login()
    {
        global $pdo;

        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // 1. Buscar usuario
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verificar bloqueo
                if ($user['bloqueado_hasta'] && new DateTime() < new DateTime($user['bloqueado_hasta'])) {
                    $error = "Cuenta bloqueada temporalmente. Intenta más tarde.";
                    // REGISTRO AUDITORÍA: Intento bloqueado
                    $this->registrarAuditoria($user['id'], $email, 0, 'Bloqueado');
                    require 'views/auth/login.php';
                    return;
                }

                // Verificar password
                if (password_verify($password, $user['password_hash'])) {
                    // LOGIN ÉXITO
                    $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id");
                    $stmt->execute(['id' => $user['id']]);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nombre'] = $user['nombre_completo'];
                    $_SESSION['rol'] = $user['rol'];

                    // REGISTRO AUDITORÍA: Éxito
                    $this->registrarAuditoria($user['id'], $email, 1, 'Acceso Correcto');

                    // Redirección según rol
                    if ($user['rol'] === 'admin') {
                        header("Location: index.php?action=dashboard"); // O admin_dashboard si prefieres
                    } else {
                        header("Location: index.php?action=dashboard");
                    }
                    exit;

                } else {
                    // PASSWORD INCORRECTO
                    $this->manejarFalloLogin($user);
                    // REGISTRO AUDITORÍA: Fallo credenciales
                    $this->registrarAuditoria($user['id'], $email, 0, 'Contraseña Incorrecta');
                    $error = "Credenciales incorrectas.";
                }
            } else {
                // USUARIO NO EXISTE (Seguridad por oscuridad: mismo mensaje)
                // REGISTRO AUDITORÍA: Intento con email desconocido
                $this->registrarAuditoria(null, $email, 0, 'Usuario Inexistente');
                $error = "Credenciales incorrectas.";
            }
            require 'views/auth/login.php';
        } else {
            require 'views/auth/login.php';
        }
    }

    private function manejarFalloLogin($user)
    {
        global $pdo;

        // Aumentamos el contador
        $intentos = $user['intentos_fallidos'] + 1;
        $bloqueo = null;

        // Si llega a 5 intentos (o más), bloqueamos Y ENVIAMOS CORREO
        if ($intentos >= 5) {
            // Calculamos fecha de desbloqueo (15 minutos en el futuro)
            $bloqueo = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');

            // --- ENVIAR CORREO (SQAP 2.3) ---
            // Llamamos al helper que ya probaste que funciona
            EmailHelper::enviar($user['email'], $user['nombre_completo'], 'bloqueo');
        }

        // Guardamos en Base de Datos
        $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = :intentos, bloqueado_hasta = :bloqueo WHERE id = :id");
        $stmt->execute(['intentos' => $intentos, 'bloqueo' => $bloqueo, 'id' => $user['id']]);
    }

    // --- NUEVA FUNCIÓN PARA EL SQAP ---
    private function registrarAuditoria($user_id, $email, $exitoso, $detalle)
    {
        global $pdo;
        try {
            $ip = $_SERVER['REMOTE_ADDR']; // Obtiene la IP del usuario
            $stmt = $pdo->prepare("INSERT INTO auditoria_login (usuario_id, email_intentado, ip_address, exitoso, detalle) VALUES (:uid, :email, :ip, :exito, :det)");
            $stmt->execute([
                'uid' => $user_id,
                'email' => $email,
                'ip' => $ip,
                'exito' => $exitoso,
                'det' => $detalle
            ]);
        } catch (Exception $e) {
            // Silencioso: si falla el log, no queremos romper el login del usuario
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
?>