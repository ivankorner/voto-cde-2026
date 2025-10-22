-- Script para agregar claves foráneas e índices DESPUÉS de crear las tablas
-- Ejecutar SOLO después de haber ejecutado cloud_schema_compatible.sql exitosamente

-- Agregar claves foráneas
ALTER TABLE users ADD FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL;
ALTER TABLE orden_dia ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE orden_dia_items ADD FOREIGN KEY (orden_dia_id) REFERENCES orden_dia(id) ON DELETE CASCADE;
ALTER TABLE orden_dia_expedientes ADD FOREIGN KEY (orden_dia_item_id) REFERENCES orden_dia_items(id) ON DELETE CASCADE;
ALTER TABLE orden_dia_actas ADD FOREIGN KEY (orden_dia_item_id) REFERENCES orden_dia_items(id) ON DELETE CASCADE;
ALTER TABLE sesiones_votacion ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE votaciones ADD FOREIGN KEY (sesion_id) REFERENCES sesiones_votacion(id) ON DELETE CASCADE;
ALTER TABLE votos ADD FOREIGN KEY (votacion_id) REFERENCES votaciones(id) ON DELETE CASCADE;
ALTER TABLE votos ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE user_sessions ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE activity_logs ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE auditoria_votacion ADD FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL;

-- Agregar índices para optimización
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
CREATE INDEX idx_auditoria_accion ON auditoria_votacion(accion);
CREATE INDEX idx_auditoria_sesion ON auditoria_votacion(sesion_id);
CREATE INDEX idx_auditoria_usuario ON auditoria_votacion(usuario_id);
CREATE INDEX idx_auditoria_fecha ON auditoria_votacion(fecha_accion);

SELECT 'Claves foráneas e índices agregados exitosamente' as Resultado;