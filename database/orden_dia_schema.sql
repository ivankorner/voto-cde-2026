-- Script para crear las tablas del sistema de Orden del Día
-- Ejecutar este script en la base de datos voto_db

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
    tipo_item ENUM(
        'protocolo', 
        'lectura_actas', 
        'expedientes_fuera_termino', 
        'tratamientos', 
        'proyectos_concejales', 
        'proyectos_ejecutivo', 
        'notas_ejecutivo', 
        'notas_oficiales', 
        'notas_particulares', 
        'espacio_ciudadano', 
        'dictamenes_comisiones', 
        'homenajes', 
        'temas_internos'
    ) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (orden_dia_id) REFERENCES orden_dia(id) ON DELETE CASCADE,
    KEY idx_orden_dia_numero (orden_dia_id, numero_orden)
);

-- Tabla para los expedientes/proyectos asociados a cada ítem
CREATE TABLE IF NOT EXISTS orden_dia_expedientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_dia_item_id INT NOT NULL,
    numero_expediente VARCHAR(100),
    extracto TEXT,
    comision VARCHAR(100),
    tipo_instrumento ENUM('ordenanza', 'resolucion', 'comunicacion', 'declaracion') NULL,
    bloque_autor VARCHAR(150),
    concejal_autor VARCHAR(150),
    nombre_ciudadano VARCHAR(150), -- Para espacio ciudadano
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

-- Insertar los ítems predeterminados para una orden del día estándar
-- Esta será una función que se ejecutará al crear una nueva orden del día

-- Ejemplo de trigger para crear automáticamente los ítems estándar
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS crear_items_estandar 
AFTER INSERT ON orden_dia
FOR EACH ROW
BEGIN
    INSERT INTO orden_dia_items (orden_dia_id, numero_orden, titulo, tipo_item) VALUES
    (NEW.id, 1, 'IZAMIENTO DEL PABELLÓN NACIONAL', 'protocolo'),
    (NEW.id, 2, 'LECTURA ORDEN DEL DIA', 'protocolo'),
    (NEW.id, 3, 'LECTURA Y CONSIDERACIÓN DE ACTAS', 'lectura_actas'),
    (NEW.id, 4, 'EXPEDIENTES INGRESADO FUERA DE TÉRMINO', 'expedientes_fuera_termino'),
    (NEW.id, 5, 'TRATAMIENTOS: SOBRE TABLAS, DE PREFERENCIA Y/O DE RECONSIDERACIÓN', 'tratamientos'),
    (NEW.id, 6, 'PROYECTOS PRESENTADOS POR LAS Y LOS CONCEJALES', 'proyectos_concejales'),
    (NEW.id, 7, 'PROYECTOS PRESENTADOS POR EL PODER EJECUTIVO MUNICIPAL', 'proyectos_ejecutivo'),
    (NEW.id, 8, 'NOTAS DEL PODER EJECUTIVO', 'notas_ejecutivo'),
    (NEW.id, 9, 'NOTAS DE ASUNTOS OFICIALES', 'notas_oficiales'),
    (NEW.id, 10, 'NOTAS DE ASUNTOS PARTICULARES', 'notas_particulares'),
    (NEW.id, 11, 'ESPACIO CIUDADANO', 'espacio_ciudadano'),
    (NEW.id, 12, 'DICTAMENES DE COMISIONES', 'dictamenes_comisiones'),
    (NEW.id, 13, 'HOMENAJES', 'homenajes'),
    (NEW.id, 14, 'TEMAS INTERNOS', 'temas_internos');
END$$
DELIMITER ;

-- Índices para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_orden_dia_fecha ON orden_dia(fecha_sesion);
CREATE INDEX IF NOT EXISTS idx_orden_dia_estado ON orden_dia(estado);
CREATE INDEX IF NOT EXISTS idx_items_tipo ON orden_dia_items(tipo_item);
CREATE INDEX IF NOT EXISTS idx_expedientes_numero ON orden_dia_expedientes(numero_expediente);