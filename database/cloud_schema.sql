-- Script SQL para servidor compartido/cloud (SIN TRIGGERS)
-- Configuración para base de datos: a0020819_votocde
-- Usuario: a0020819_votocde
-- Servidor: localhost

-- NO incluir CREATE DATABASE ya que la base de datos ya existe en el hosting
-- USE a0020819_votocde; -- Descomenta si es necesario

-- Tabla de roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de usuarios
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

-- Tabla de sesiones (opcional, para manejo avanzado de sesiones)
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de logs de actividad (opcional, para auditoría)
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

-- Tabla de auditoría para votaciones
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

-- Tablas para el sistema de votación
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

-- Insertar roles por defecto (solo si no existen)
INSERT IGNORE INTO roles (name, description) VALUES
('admin', 'Administrador del sistema con acceso completo'),
('editor', 'Editor con permisos de gestión de contenido'),
('viewer', 'Usuario con permisos de solo lectura');

-- Insertar usuario administrador por defecto (solo si no existe)
-- Usuario: admin, Contraseña: admin123
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id) VALUES
('admin', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 1);

-- Insertar algunos usuarios de ejemplo (solo si no existen)
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id) VALUES
('editor1', 'editor@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Editor', 'Principal', 2),
('viewer1', 'viewer@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuario', 'Viewer', 3);

-- Índices adicionales para optimización
-- Crear índices solo si no causan error
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_roles_name ON roles(name);
CREATE INDEX idx_roles_status ON roles(status);
CREATE INDEX idx_orden_dia_fecha ON orden_dia(fecha_sesion);
CREATE INDEX idx_orden_dia_estado ON orden_dia(estado);
CREATE INDEX idx_items_tipo ON orden_dia_items(tipo_item);
CREATE INDEX idx_expedientes_numero ON orden_dia_expedientes(numero_expediente);
CREATE INDEX idx_votaciones_sesion ON votaciones(sesion_id);
CREATE INDEX idx_votaciones_estado ON votaciones(estado);
CREATE INDEX idx_votos_votacion ON votos(votacion_id);
CREATE INDEX idx_votos_user ON votos(user_id);

-- Mostrar información de finalización
SELECT 'Base de datos configurada exitosamente para servidor compartido' as Resultado;