<?php

namespace Config;

use PDO;
use PDOException;

class ConexionBD
{
    private string $host = 'localhost';
    private string $db = 'banco_db';
    private string $user = 'root';
    private string $pass = '292006';
    private string $charset = 'utf8mb4';
    private ?PDO $pdo = null;

    public function obtenerConexion(): ?PDO
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
            } catch (PDOException $e) {
                // En producción, registrar el error en un log en lugar de mostrarlo
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                // Lanzar una excepción genérica o manejar el error según la política de la aplicación
                // Para este caso, detenemos la ejecución con un mensaje controlado como se solicitó
                die("Error crítico: No se pudo establecer conexión con la base de datos.");
            }
        }

        return $this->pdo;
    }
}
