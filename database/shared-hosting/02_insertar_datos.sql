-- PASO 2: Insertar datos iniciales
-- Para servidor compartido DonWeb
-- Ejecutar DESPUÉS del paso 1

USE a0020819_votocde;

-- Insertar roles por defecto
INSERT IGNORE INTO roles (name, description) VALUES
('admin', 'Administrador del sistema con acceso completo'),
('editor', 'Editor con permisos de gestión de contenido'),
('viewer', 'Usuario con permisos de solo lectura');

-- Insertar usuario administrador por defecto
-- Usuario: admin, Contraseña: admin123
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id) VALUES
('admin', 'admin@donweb.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 1);

-- Insertar algunos usuarios de ejemplo
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id) VALUES
('editor1', 'editor@donweb.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Editor', 'Principal', 2),
('viewer1', 'viewer@donweb.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuario', 'Viewer', 3);