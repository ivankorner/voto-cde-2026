-- Migración para crear tablas faltantes en VotoCDE
-- Fecha: 2025-10-22

-- Crear tabla puntos_habilitados si no existe
CREATE TABLE IF NOT EXISTS puntos_habilitados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    item_tipo VARCHAR(50) NOT NULL,
    item_id INT NULL,
    numero_expediente VARCHAR(100) NULL,
    extracto TEXT NULL,
    orden_punto INT NOT NULL DEFAULT 0,
    habilitado TINYINT(1) NOT NULL DEFAULT 0,
    habilitado_por INT NULL,
    fecha_habilitacion TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sesion (sesion_id),
    INDEX idx_habilitado (habilitado),
    INDEX idx_orden (orden_punto),
    INDEX idx_item (item_tipo, item_id),
    FOREIGN KEY (sesion_id) REFERENCES sesiones_votacion(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear tabla presentes_sesion si no existe (por si acaso)
CREATE TABLE IF NOT EXISTS presentes_sesion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    user_id INT NOT NULL,
    presente TINYINT(1) NOT NULL DEFAULT 1,
    hora_ingreso TIMESTAMP NULL,
    hora_salida TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_sesion_user (sesion_id, user_id),
    INDEX idx_sesion (sesion_id),
    INDEX idx_user (user_id),
    FOREIGN KEY (sesion_id) REFERENCES sesiones_votacion(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Verificar que la tabla historial_votacion exista
CREATE TABLE IF NOT EXISTS historial_votacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    item_votacion_tipo VARCHAR(50) NOT NULL,
    item_votacion_id INT NULL,
    total_presentes INT NOT NULL DEFAULT 0,
    votos_positivos INT NOT NULL DEFAULT 0,
    votos_negativos INT NOT NULL DEFAULT 0,
    votos_abstenciones INT NOT NULL DEFAULT 0,
    resultado ENUM('aprobado', 'rechazado', 'empate', 'pendiente') DEFAULT 'pendiente',
    fecha_votacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sesion (sesion_id),
    INDEX idx_item (item_votacion_tipo, item_votacion_id),
    FOREIGN KEY (sesion_id) REFERENCES sesiones_votacion(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Agregar columnas faltantes a orden_dia_expedientes si no existen
ALTER TABLE orden_dia_expedientes 
ADD COLUMN IF NOT EXISTS numero_expediente VARCHAR(100) NULL AFTER orden_dia_item_id;

ALTER TABLE orden_dia_expedientes 
ADD COLUMN IF NOT EXISTS extracto TEXT NULL AFTER numero_expediente;