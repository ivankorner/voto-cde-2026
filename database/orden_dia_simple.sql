-- Script simplificado para crear las tablas del sistema de Orden del Día

-- Tabla principal para las órdenes del día
CREATE TABLE IF NOT EXISTS orden_dia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_acta VARCHAR(50) NOT NULL,
    fecha_sesion DATE NOT NULL,
    hora_sesion TIME NOT NULL,
    tipo_sesion ENUM('ordinaria', 'extraordinaria') DEFAULT 'ordinaria',
    estado ENUM('borrador', 'publicado', 'archivado') DEFAULT 'borrador',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_acta (numero_acta)
);

-- Tabla para los ítems de cada orden del día
CREATE TABLE IF NOT EXISTS orden_dia_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_dia_id INT NOT NULL,
    numero_orden TINYINT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo_item VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (orden_dia_id) REFERENCES orden_dia(id) ON DELETE CASCADE
);

-- Tabla para los expedientes/proyectos asociados a cada ítem
CREATE TABLE IF NOT EXISTS orden_dia_expedientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_dia_item_id INT NOT NULL,
    numero_expediente VARCHAR(100),
    extracto TEXT,
    comision VARCHAR(100),
    tipo_instrumento VARCHAR(50) NULL,
    bloque_autor VARCHAR(150),
    concejal_autor VARCHAR(150),
    nombre_ciudadano VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (orden_dia_item_id) REFERENCES orden_dia_items(id) ON DELETE CASCADE
);

-- Tabla para actas referenciadas (para el ítem 3)
CREATE TABLE IF NOT EXISTS orden_dia_actas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_dia_item_id INT NOT NULL,
    numero_acta VARCHAR(50),
    tipo_sesion VARCHAR(50),
    fecha_acta DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (orden_dia_item_id) REFERENCES orden_dia_items(id) ON DELETE CASCADE
);