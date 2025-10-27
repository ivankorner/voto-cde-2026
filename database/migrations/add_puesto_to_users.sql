-- Migración para agregar el campo 'puesto' a la tabla users
-- Para servidor compartido DonWeb

USE a0020819_votocde;

-- Agregar la columna puesto a la tabla users
ALTER TABLE users ADD COLUMN puesto ENUM('Presidente', 'Vice Presidente', 'Concejal', 'Secretario', 'Pro Secretario') NULL AFTER last_name;

-- Agregar índice para el campo puesto
CREATE INDEX idx_users_puesto ON users(puesto);