-- PASO 3: Crear índices para optimización
-- Para servidor compartido DonWeb
-- Ejecutar DESPUÉS del paso 2

USE a0020819_votocde;

-- Índices para usuarios (crear solo si no existen)
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_role_id ON users(role_id);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
CREATE INDEX IF NOT EXISTS idx_users_puesto ON users(puesto);

-- Índices para roles
CREATE INDEX IF NOT EXISTS idx_roles_name ON roles(name);
CREATE INDEX IF NOT EXISTS idx_roles_status ON roles(status);