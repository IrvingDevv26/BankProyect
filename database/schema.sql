-- Creación de la base de datos si no existe
CREATE DATABASE IF NOT EXISTS banco_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE banco_db;

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente',
    intentos_fallidos INT DEFAULT 0,
    bloqueado_hasta DATETIME NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    estatus ENUM('activo', 'inactivo', 'susp') DEFAULT 'activo'
) ENGINE=InnoDB;

-- Tabla de Cuentas Bancarias
CREATE TABLE IF NOT EXISTS cuentas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    numero_cuenta VARCHAR(20) NOT NULL UNIQUE,
    tipo_cuenta ENUM('ahorro', 'corriente') NOT NULL,
    saldo DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    fecha_apertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    estatus ENUM('activa', 'bloqueada', 'cerrada') DEFAULT 'activa',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de Transacciones
CREATE TABLE IF NOT EXISTS transacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuenta_origen_id INT NOT NULL,
    cuenta_destino_id INT NULL, -- Puede ser NULL si es depósito/retiro externo
    tipo_transaccion ENUM('transferencia', 'deposito', 'retiro', 'pago_servicio') NOT NULL,
    monto DECIMAL(15, 2) NOT NULL,
    descripcion VARCHAR(255) NULL,
    fecha_transaccion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completada', 'fallida', 'reversada') DEFAULT 'completada',
    FOREIGN KEY (cuenta_origen_id) REFERENCES cuentas(id),
    FOREIGN KEY (cuenta_destino_id) REFERENCES cuentas(id)
) ENGINE=InnoDB;

-- Índices para mejorar el rendimiento
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_cuentas_numero ON cuentas(numero_cuenta);
CREATE INDEX idx_transacciones_fecha ON transacciones(fecha_transaccion);

-- Tabla de Auditoría
CREATE TABLE IF NOT EXISTS auditoria_login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    email_intentado VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    exitoso TINYINT(1) NOT NULL,
    detalle VARCHAR(255) NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabla de Emails
CREATE TABLE IF NOT EXISTS emails_simulados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinatario VARCHAR(100) NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    cuerpo TEXT NOT NULL,
    estado VARCHAR(50) NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
