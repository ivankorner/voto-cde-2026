-- Script SQL ULTRA COMPATIBLE para servidor compartido/cloud
-- Configuración para base de datos: a0020819_votocde
-- Compatible con MySQL 5.5+ y MariaDB

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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
    nombre_ciudadano VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla para actas referenciadas
CREATE TABLE IF NOT EXISTS orden_dia_actas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_dia_item_id INT NOT NULL,
    numero_acta VARCHAR(50),
    tipo_sesion VARCHAR(50),
    fecha_acta DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de sesiones de votación
CREATE TABLE IF NOT EXISTS sesiones_votacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    estado ENUM('preparacion', 'activa', 'pausada', 'finalizada') DEFAULT 'preparacion',
    fecha_inicio TIMESTAMP NULL,
    fecha_fin TIMESTAMP NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de votaciones
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de votos
CREATE TABLE IF NOT EXISTS votos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    votacion_id INT NOT NULL,
    user_id INT NOT NULL,
    voto ENUM('favor', 'contra', 'abstencion', 'ausente') NOT NULL,
    fecha_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    UNIQUE KEY unique_user_vote (votacion_id, user_id)
);

-- Tabla de sesiones de usuario
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de logs de actividad
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de auditoría para votaciones
CREATE TABLE IF NOT EXISTS auditoria_votacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accion VARCHAR(100) NOT NULL,
    sesion_id INT,
    usuario_id INT,
    detalles TEXT,
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar roles por defecto
INSERT IGNORE INTO roles (name, description) VALUES
('admin', 'Administrador del sistema con acceso completo'),
('editor', 'Editor con permisos de gestión de contenido'),
('viewer', 'Usuario con permisos de solo lectura');

-- Insertar usuario administrador por defecto
-- Usuario: admin, Contraseña: admin123
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id) VALUES
('admin', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 1);

-- Insertar usuarios de ejemplo
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id) VALUES
('editor1', 'editor@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Editor', 'Principal', 2),
('viewer1', 'viewer@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuario', 'Viewer', 3);

-- Mensaje de confirmación
SELECT 'Base de datos configurada exitosamente para servidor compartido' as Resultado;