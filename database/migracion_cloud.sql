-- Script de migración para bases de datos existentes
-- Ejecutar solo si ya tienes datos y quieres agregar las nuevas tablas sin perder información

-- Verificar y crear tabla de roles si no existe
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Verificar y crear tabla de usuarios si no existe
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

-- Agregar columnas faltantes a tabla users si no existen
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS role_id INT,
ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active',
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Agregar clave foránea si no existe
ALTER TABLE users 
ADD CONSTRAINT IF NOT EXISTS fk_users_role 
FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL;

-- Verificar y crear tabla orden_dia si no existe
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

-- Verificar y crear tabla orden_dia_items si no existe
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

-- Verificar y crear tabla orden_dia_expedientes si no existe
CREATE TABLE IF NOT EXISTS orden_dia_expedientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_dia_item_id INT NOT NULL,
    numero_expediente VARCHAR(100),
    extracto TEXT,
    comision VARCHAR(100),
    tipo_instrumento ENUM('ordenanza', 'resolucion', 'comunicacion', 'declaracion') NULL,
    bloque_autor VARCHAR(150),
    concejal_autor VARCHAR(150),
    nombre_ciudadano VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (orden_dia_item_id) REFERENCES orden_dia_items(id) ON DELETE CASCADE
);

-- Verificar y crear tabla orden_dia_actas si no existe
CREATE TABLE IF NOT EXISTS orden_dia_actas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_dia_item_id INT NOT NULL,
    numero_acta VARCHAR(50),
    tipo_sesion VARCHAR(50),
    fecha_acta DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (orden_dia_item_id) REFERENCES orden_dia_items(id) ON DELETE CASCADE
);

-- Verificar y crear tabla sesiones_votacion si no existe
CREATE TABLE IF NOT EXISTS sesiones_votacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    estado ENUM('preparacion', 'activa', 'pausada', 'finalizada') DEFAULT 'preparacion',
    fecha_inicio TIMESTAMP NULL,
    fecha_fin TIMESTAMP NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Verificar y crear tabla votaciones si no existe
CREATE TABLE IF NOT EXISTS votaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo ENUM('nominal', 'secreta') DEFAULT 'nominal',
    estado ENUM('preparacion', 'activa', 'finalizada') DEFAULT 'preparacion',
    fecha_inicio TIMESTAMP NULL,
    fecha_fin TIMESTAMP NULL,
    resultado_final ENUM('aprobado', 'rechazado', 'sin_quorum') NULL,
    votos_favor INT DEFAULT 0,
    votos_contra INT DEFAULT 0,
    abstenciones INT DEFAULT 0,
    ausentes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones_votacion(id) ON DELETE CASCADE
);

-- Verificar y crear tabla votos si no existe
CREATE TABLE IF NOT EXISTS votos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    votacion_id INT NOT NULL,
    user_id INT NOT NULL,
    voto ENUM('favor', 'contra', 'abstencion', 'ausente') NOT NULL,
    fecha_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (votacion_id) REFERENCES votaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_vote (votacion_id, user_id)
);

-- Verificar y crear tablas adicionales si no existen
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS auditoria_votacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accion VARCHAR(100) NOT NULL,
    sesion_id INT,
    usuario_id INT,
    detalles JSON,
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_auditoria_accion (accion),
    INDEX idx_auditoria_sesion (sesion_id),
    INDEX idx_auditoria_usuario (usuario_id),
    INDEX idx_auditoria_fecha (fecha_accion)
);

-- Insertar datos iniciales solo si no existen
INSERT IGNORE INTO roles (name, description) VALUES
('admin', 'Administrador del sistema con acceso completo'),
('editor', 'Editor con permisos de gestión de contenido'),
('viewer', 'Usuario con permisos de solo lectura');

-- Insertar usuario admin solo si no existe
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id) VALUES
('admin', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 1);

-- ELIMINAR TRIGGERS SI EXISTEN (para evitar errores en servidor compartido)
DROP TRIGGER IF EXISTS crear_items_estandar;

-- Crear índices faltantes si no existen
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_role_id ON users(role_id);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
CREATE INDEX IF NOT EXISTS idx_roles_name ON roles(name);
CREATE INDEX IF NOT EXISTS idx_roles_status ON roles(status);
CREATE INDEX IF NOT EXISTS idx_orden_dia_fecha ON orden_dia(fecha_sesion);
CREATE INDEX IF NOT EXISTS idx_orden_dia_estado ON orden_dia(estado);
CREATE INDEX IF NOT EXISTS idx_items_tipo ON orden_dia_items(tipo_item);
CREATE INDEX IF NOT EXISTS idx_expedientes_numero ON orden_dia_expedientes(numero_expediente);
CREATE INDEX IF NOT EXISTS idx_votaciones_sesion ON votaciones(sesion_id);
CREATE INDEX IF NOT EXISTS idx_votaciones_estado ON votaciones(estado);
CREATE INDEX IF NOT EXISTS idx_votos_votacion ON votos(votacion_id);
CREATE INDEX IF NOT EXISTS idx_votos_user ON votos(user_id);

SELECT 'Migración completada exitosamente. Base de datos actualizada para servidor compartido.' as Resultado;