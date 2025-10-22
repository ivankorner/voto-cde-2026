-- ============================================
-- MÓDULO DE VOTACIÓN LEGISLATIVA
-- ============================================
-- Base de datos: voto_db
-- Fecha: 16 de septiembre de 2025
-- ============================================

-- Tabla para gestionar sesiones de votación
CREATE TABLE sesiones_voto (
    id INT(11) NOT NULL AUTO_INCREMENT,
    orden_dia_id INT(11) NOT NULL,
    nombre_sesion VARCHAR(255) NOT NULL,
    descripcion TEXT,
    estado ENUM('planificada', 'activa', 'pausada', 'finalizada') NOT NULL DEFAULT 'planificada',
    fecha_inicio TIMESTAMP NULL,
    fecha_fin TIMESTAMP NULL,
    created_by INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    FOREIGN KEY (orden_dia_id) REFERENCES orden_dia(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_sesion_estado (estado),
    INDEX idx_sesion_fecha (fecha_inicio),
    INDEX idx_orden_dia (orden_dia_id)
);

-- Tabla para registrar presencia de usuarios en sesiones
CREATE TABLE presentes_sesion (
    id INT(11) NOT NULL AUTO_INCREMENT,
    sesion_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    presente BOOLEAN DEFAULT TRUE,
    hora_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    hora_salida TIMESTAMP NULL,
    observaciones VARCHAR(255),
    
    PRIMARY KEY (id),
    FOREIGN KEY (sesion_id) REFERENCES sesiones_voto(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_sesion (sesion_id, user_id),
    INDEX idx_sesion_presentes (sesion_id),
    INDEX idx_user_presentes (user_id)
);

-- Tabla para registrar los votos individuales
CREATE TABLE votos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    sesion_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    item_votacion_tipo ENUM('expediente', 'actas', 'item_general') NOT NULL,
    item_votacion_id INT(11), -- ID del expediente o item del orden del día
    numero_expediente VARCHAR(50), -- Para referencia rápida
    extracto_expediente TEXT, -- Descripción de lo que se vota
    tipo_voto ENUM('positivo', 'negativo', 'abstencion') NOT NULL,
    observaciones TEXT,
    fecha_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    
    PRIMARY KEY (id),
    FOREIGN KEY (sesion_id) REFERENCES sesiones_voto(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Un usuario solo puede votar una vez por item en una sesión
    UNIQUE KEY unique_user_item_sesion (sesion_id, user_id, item_votacion_tipo, item_votacion_id),
    
    INDEX idx_sesion_votos (sesion_id),
    INDEX idx_user_votos (user_id),
    INDEX idx_fecha_voto (fecha_voto),
    INDEX idx_expediente (numero_expediente),
    INDEX idx_item_tipo (item_votacion_tipo)
);

-- Tabla para el historial de estados de votación por ítem
CREATE TABLE historial_votacion (
    id INT(11) NOT NULL AUTO_INCREMENT,
    sesion_id INT(11) NOT NULL,
    item_votacion_tipo ENUM('expediente', 'actas', 'item_general') NOT NULL,
    item_votacion_id INT(11),
    numero_expediente VARCHAR(50),
    extracto_expediente TEXT,
    total_presentes INT(11) NOT NULL DEFAULT 0,
    votos_positivos INT(11) NOT NULL DEFAULT 0,
    votos_negativos INT(11) NOT NULL DEFAULT 0,
    votos_abstenciones INT(11) NOT NULL DEFAULT 0,
    estado_votacion ENUM('abierta', 'cerrada', 'suspendida') NOT NULL DEFAULT 'abierta',
    resultado ENUM('aprobado', 'rechazado', 'empate', 'pendiente') NOT NULL DEFAULT 'pendiente',
    fecha_apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP NULL,
    observaciones TEXT,
    
    PRIMARY KEY (id),
    FOREIGN KEY (sesion_id) REFERENCES sesiones_voto(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_item_sesion (sesion_id, item_votacion_tipo, item_votacion_id),
    INDEX idx_sesion_historial (sesion_id),
    INDEX idx_resultado (resultado),
    INDEX idx_estado_votacion (estado_votacion),
    INDEX idx_expediente_hist (numero_expediente)
);

-- ============================================
-- INSERTAR DATOS INICIALES
-- ============================================

-- Comentarios explicativos del sistema
-- Se puede insertar una sesión de prueba manualmente después de la creación