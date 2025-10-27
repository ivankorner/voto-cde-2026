-- Versión alternativa para servidores compartidos con restricciones
-- Si los scripts anteriores fallan, usar este script básico

USE a0020819_votocde;

-- Eliminar tablas si existen (cuidado con datos existentes)
DROP TABLE IF EXISTS auditoria_votacion;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS user_sessions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

-- Crear tabla de roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crear tabla de usuarios  
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    puesto VARCHAR(50) NULL,
    role_id INT,
    status VARCHAR(20) DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar roles
INSERT INTO roles (name, description) VALUES
('admin', 'Administrador del sistema con acceso completo'),
('editor', 'Editor con permisos de gestión de contenido'),
('viewer', 'Usuario con permisos de solo lectura');

-- Insertar usuario administrador
INSERT INTO users (username, email, password, first_name, last_name, role_id) VALUES
('admin', 'admin@donweb.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 1);

-- Insertar usuarios de ejemplo
INSERT INTO users (username, email, password, first_name, last_name, role_id) VALUES
('editor1', 'editor@donweb.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Editor', 'Principal', 2),
('viewer1', 'viewer@donweb.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuario', 'Viewer', 3);

-- Crear índices básicos
ALTER TABLE users ADD INDEX idx_users_email (email);
ALTER TABLE users ADD INDEX idx_users_username (username);
ALTER TABLE users ADD INDEX idx_users_role_id (role_id);

-- Mostrar resultado
SELECT 'Instalación básica completada' as resultado;