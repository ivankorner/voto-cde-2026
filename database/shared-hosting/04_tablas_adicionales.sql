-- PASO 4: Crear tablas adicionales (opcional)
-- Para servidor compartido DonWeb
-- Ejecutar DESPUÉS del paso 3

USE a0020819_votocde;

-- Tabla de sesiones (para manejo avanzado de sesiones)
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de logs de actividad (para auditoría)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT, -- Cambiado de JSON a TEXT para compatibilidad
    new_values TEXT, -- Cambiado de JSON a TEXT para compatibilidad
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabla de auditoría para votaciones
CREATE TABLE IF NOT EXISTS auditoria_votacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accion VARCHAR(100) NOT NULL,
    sesion_id INT,
    usuario_id INT,
    detalles TEXT, -- Cambiado de JSON a TEXT para compatibilidad
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Crear índices para las tablas adicionales
CREATE INDEX IF NOT EXISTS idx_auditoria_accion ON auditoria_votacion(accion);
CREATE INDEX IF NOT EXISTS idx_auditoria_sesion ON auditoria_votacion(sesion_id);
CREATE INDEX IF NOT EXISTS idx_auditoria_usuario ON auditoria_votacion(usuario_id);
CREATE INDEX IF NOT EXISTS idx_auditoria_fecha ON auditoria_votacion(fecha_accion);