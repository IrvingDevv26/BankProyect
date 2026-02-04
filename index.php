<?php
session_start();
require 'config/db.php';
require 'controllers/AuthController.php';
require 'controllers/ClientController.php';
require 'controllers/AdminController.php';
require_once 'helpers/EmailHelper.php';

// --- NUEVO: GUARDIÁN DE INACTIVIDAD (SQAP 2.3) ---
$tiempo_limite = 600; // 10 minutos en segundos

// Si hay sesión activa...
if (isset($_SESSION['user_id'])) {
    // Verificamos si existe un tiempo registrado de "última actividad"
    if (isset($_SESSION['ultimo_acceso'])) {
        $tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];

        // Si pasó más del límite...
        if ($tiempo_transcurrido > $tiempo_limite) {
            // Destruimos sesión y redirigimos con alerta
            session_unset();
            session_destroy();
            header("Location: index.php?action=login&msg=timeout");
            exit;
        }
    }
    // Si no ha expirado (o es login nuevo), actualizamos el reloj
    $_SESSION['ultimo_acceso'] = time();
}
// ------------------------------------------------

// Enrutador (Switch) - RESTO DEL ARCHIVO IGUAL...
$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        // Verificamos sesión antes de instanciar nada
        if (!isset($_SESSION['rol'])) {
            header("Location: index.php?action=login");
            exit;
        }

        // Redirección según rol
        if ($_SESSION['rol'] === 'admin') {
            $controller = new AdminController();
            $controller->index();
        } else {
            $controller = new ClientController();
            $controller->index();
        }
        break;

    // AQUI ES DONDE IMPLEMENTAREMOS JSON DESPUÉS
    case 'api_transferir':
        $controller = new ClientController();
        $controller->transferir(); // Esta función devolverá JSON
        break;

    case 'api_bloquear_usuario':
        $controller = new AdminController();
        $controller->toggleBloqueo();
        break;
    case 'tarjetas':
        $controller = new ClientController();
        $controller->tarjetas();
        break;

    // Acción API (AJAX)
    case 'api_congelar_tarjeta':
        $controller = new ClientController();
        $controller->toggleTarjeta();
        break;

    case 'reportes':
        $controller = new ClientController();
        $controller->reportes();
        break;
    case 'auditoria':
        $controller = new AdminController();
        $controller->auditoria();
        break;
    case 'admin_emails': // Nueva ruta
        $controller = new AdminController();
        $controller->emails();
        break;
    case 'perfil':
        $controller = new ClientController();
        $controller->perfil();
        break;

    case 'api_cambiar_password':
        $controller = new ClientController();
        $controller->cambiarPassword();
        break;
    case 'api_admin_depositar':
        $controller = new AdminController();
        $controller->depositar();
        break;
    case 'admin_config':
        $controller = new AdminController();
        $controller->configuracion();
        break;

    case 'api_crear_usuario':
        $controller = new AdminController();
        $controller->crearUsuario();
        break;

    case 'api_editar_usuario':
        $controller = new AdminController();
        $controller->editarUsuario();
        break;
    default:
        echo "<h1>Error 404</h1><p>Página no encontrada</p>";
        break;
}
?>