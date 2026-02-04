<?php
// helpers/EmailHelper.php

require_once 'libs/Exception.php';
require_once 'libs/PHPMailer.php';
require_once 'libs/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {

    public static function enviar($destinatario, $nombre, $tipo) {
        global $pdo;

        // 1. CARGAR CONFIGURACIÓN DE SEGURIDAD
        // Usamos __DIR__ para salir de la carpeta helpers y entrar a config
        if (!file_exists(__DIR__ . '/../config/env.php')) {
            die("Error Crítico: No se encuentra el archivo de configuración de correo (config/env.php)");
        }
        $config = require __DIR__ . '/../config/env.php';

        $mail = new PHPMailer(true);

        try {
            // 2. Usar las credenciales del archivo externo
            $mail->isSMTP();
            $mail->Host       = $config['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['SMTP_USER'];
            $mail->Password   = $config['SMTP_PASS']; // ¡Ya no está visible aquí!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $config['SMTP_PORT'];

            // Remitente
            $mail->setFrom($config['SMTP_USER'], $config['SMTP_FROM_NAME']);
            $mail->addAddress($destinatario, $nombre);
            $mail->CharSet = 'UTF-8';

            // Contenido
            $mail->isHTML(true);

            switch ($tipo) {
                case 'bloqueo':
                    $mail->Subject = "⚠️ ALERTA DE SEGURIDAD: Cuenta Bloqueada";
                    $cuerpo = "<h3>Hola $nombre,</h3>
                               <p>Hemos detectado <strong>5 intentos fallidos</strong> de acceso a tu cuenta.</p>
                               <p style='color:red;'>Por seguridad, tu acceso ha sido bloqueado por 15 minutos.</p>
                               <p>Si no fuiste tú, contacta a soporte inmediatamente.</p>
                               <hr><small>NeoBank Security Team</small>";
                    break;

                case 'transferencia':
                    $mail->Subject = "✅ Transferencia Exitosa";
                    $cuerpo = "<h3>Hola $nombre,</h3>
                               <p>Tu transferencia se ha realizado correctamente.</p>
                               <p>Puedes descargar tu comprobante desde tu banca en línea.</p>
                               <hr><small>Gracias por usar NeoBank</small>";
                    break;

                default:
                    $mail->Subject = "Notificación NeoBank";
                    $cuerpo = "Tienes un nuevo mensaje del sistema.";
            }

            $mail->Body = $cuerpo;
            $mail->AltBody = strip_tags($cuerpo);

            // Enviar
            $mail->send();

            // Log en BD
            $stmt = $pdo->prepare("INSERT INTO emails_simulados (destinatario, asunto, cuerpo, estado) VALUES (:dest, :asunto, :cuerpo, 'enviado')");
            $stmt->execute([
                'dest' => $destinatario,
                'asunto' => $mail->Subject,
                'cuerpo' => $cuerpo
            ]);

            return true;

        } catch (Exception $e) {
            $error_msg = $mail->ErrorInfo;
            // Log de error en BD
            try {
                $stmt = $pdo->prepare("INSERT INTO emails_simulados (destinatario, asunto, cuerpo, estado) VALUES (:dest, 'ERROR ENVIO', :error, 'fallido')");
                $stmt->execute([
                    'dest' => $destinatario,
                    'error' => "Error PHPMailer: $error_msg"
                ]);
            } catch(Exception $dbError) {
                // Si falla la BD, no hacemos nada para no romper el flujo
            }
            return false;
        }
    }
}
?>