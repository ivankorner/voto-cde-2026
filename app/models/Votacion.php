<?php
require_once 'core/Model.php';

class Votacion extends Model {
    
    protected $table = 'sesiones_votacion';
    
    // ============================================
    // GESTIÓN DE SESIONES DE VOTACIÓN
    // ============================================
    
    public function crearSesion($data) {
        $query = "INSERT INTO {$this->table} 
                  (nombre, descripcion, created_by, created_at) 
                  VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($query);
        $success = $stmt->execute([
            $data['nombre'] ?? $data['nombre_sesion'] ?? '',
            $data['descripcion'] ?? '',
            $data['created_by']
        ]);
        
        return $success ? $this->db->lastInsertId() : false;
    }
    
    public function getSesionById($id) {
        $query = "SELECT sv.*, u.first_name, u.last_name
                  FROM {$this->table} sv
                  LEFT JOIN users u ON sv.created_by = u.id
                  WHERE sv.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getSesionesActivas() {
        $query = "SELECT sv.*, 
                  od.numero_acta, 
                  od.fecha_sesion, 
                  od.tipo_sesion,
                  u.username as created_by_username
                  FROM {$this->table} sv
                  LEFT JOIN orden_dia od ON sv.orden_dia_id = od.id
                  LEFT JOIN users u ON sv.created_by = u.id
                  WHERE sv.estado IN ('preparacion', 'activa', 'pausada')
                  ORDER BY sv.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTodasLasSesiones() {
        $query = "SELECT sv.*, 
                  od.numero_acta, 
                  od.fecha_sesion, 
                  od.tipo_sesion,
                  u.username as created_by_username
                  FROM {$this->table} sv
                  LEFT JOIN orden_dia od ON sv.orden_dia_id = od.id
                  LEFT JOIN users u ON sv.created_by = u.id
                  ORDER BY sv.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function actualizarEstadoSesion($id, $estado) {
        $fechaField = '';
        
        switch ($estado) {
            case 'activa':
                $fechaField = ', fecha_inicio = NOW()';
                break;
            case 'finalizada':
                $fechaField = ', fecha_fin = NOW()';
                break;
        }
        
        $query = "UPDATE {$this->table} 
                  SET estado = ?, updated_at = NOW() {$fechaField}
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$estado, $id]);
    }
    
    // ============================================
    // GESTIÓN DE PRESENCIA
    // ============================================
    
    public function registrarPresencia($sesionId, $userId, $presente = true) {
        // Asegurar que la tabla de presentes exista (fallback para entornos donde no se migró)
        $this->ensurePresentesTable();
        $query = "INSERT INTO presentes_sesion 
                  (sesion_id, user_id, presente, hora_ingreso) 
                  VALUES (?, ?, ?, NOW())
                  ON DUPLICATE KEY UPDATE 
                  presente = VALUES(presente),
                  hora_ingreso = IF(VALUES(presente) = 1 AND presente = 0, NOW(), hora_ingreso)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$sesionId, $userId, $presente]);
    }
    
    public function marcarSalida($sesionId, $userId) {
        $this->ensurePresentesTable();
        $query = "UPDATE presentes_sesion 
                  SET presente = 0, hora_salida = NOW()
                  WHERE sesion_id = ? AND user_id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$sesionId, $userId]);
    }
    
    public function getPresentesSesion($sesionId) {
        $this->ensurePresentesTable();
        $query = "SELECT ps.*, u.username, u.first_name, u.last_name, r.name as role_name
                  FROM presentes_sesion ps
                  JOIN users u ON ps.user_id = u.id
                  LEFT JOIN roles r ON u.role_id = r.id
                  WHERE ps.sesion_id = ? AND ps.presente = 1
                  ORDER BY u.first_name, u.last_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalPresentes($sesionId) {
        $this->ensurePresentesTable();
        $query = "SELECT COUNT(*) as total 
                  FROM presentes_sesion ps
                  JOIN users u ON ps.user_id = u.id
                  LEFT JOIN roles r ON u.role_id = r.id
                  WHERE ps.sesion_id = ? AND ps.presente = 1 
                  AND r.name = 'editor'"; // Solo editores pueden votar
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    private function ensurePresentesTable() {
        try {
            $this->db->query("SELECT 1 FROM presentes_sesion LIMIT 1");
        } catch (Exception $e) {
            // Crear tabla si no existe
            $sql = "CREATE TABLE IF NOT EXISTS presentes_sesion (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        sesion_id INT NOT NULL,
                        user_id INT NOT NULL,
                        presente TINYINT(1) NOT NULL DEFAULT 1,
                        hora_ingreso TIMESTAMP NULL,
                        hora_salida TIMESTAMP NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        UNIQUE KEY uniq_sesion_user (sesion_id, user_id),
                        INDEX idx_sesion (sesion_id),
                        INDEX idx_user (user_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->exec($sql);
        }
    }

    private function ensurePuntosHabilitadosTable() {
        try {
            $this->db->query("SELECT 1 FROM puntos_habilitados LIMIT 1");
        } catch (Exception $e) {
            // Crear tabla si no existe
            $sql = "CREATE TABLE IF NOT EXISTS puntos_habilitados (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        sesion_id INT NOT NULL,
                        item_tipo VARCHAR(50) NOT NULL,
                        item_id INT NULL,
                        numero_expediente VARCHAR(100) NULL,
                        extracto TEXT NULL,
                        orden_punto INT NOT NULL DEFAULT 0,
                        habilitado TINYINT(1) NOT NULL DEFAULT 0,
                        fecha_habilitacion TIMESTAMP NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_sesion (sesion_id),
                        INDEX idx_habilitado (habilitado),
                        INDEX idx_orden (orden_punto)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->exec($sql);
        }
    }
    
    // ============================================
    // GESTIÓN DE VOTOS
    // ============================================
    
    public function registrarVoto($data) {
        // Verificar que el usuario no haya votado ya
        if ($this->yaVoto($data['sesion_id'], $data['user_id'], $data['item_votacion_tipo'], $data['item_votacion_id'])) {
            return false; // Ya votó
        }
        
        $query = "INSERT INTO votos 
                  (sesion_id, user_id, item_votacion_tipo, item_votacion_id, 
                   numero_expediente, extracto_expediente, tipo_voto, observaciones, ip_address) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $success = $stmt->execute([
            $data['sesion_id'],
            $data['user_id'],
            $data['item_votacion_tipo'],
            $data['item_votacion_id'] ?? null,
            $data['numero_expediente'] ?? '',
            $data['extracto_expediente'] ?? '',
            $data['tipo_voto'],
            $data['observaciones'] ?? '',
            $data['ip_address'] ?? $_SERVER['REMOTE_ADDR']
        ]);
        
        if ($success) {
            // Actualizar historial de votación
            $this->actualizarHistorialVotacion($data['sesion_id'], $data['item_votacion_tipo'], $data['item_votacion_id']);
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    public function yaVoto($sesionId, $userId, $tipoItem, $itemId) {
        $query = "SELECT COUNT(*) as total 
                  FROM votos 
                  WHERE sesion_id = ? AND user_id = ? 
                  AND item_votacion_tipo = ? AND item_votacion_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId, $userId, $tipoItem, $itemId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
    
    public function getVotoUsuario($sesionId, $userId, $tipoItem, $itemId) {
        $query = "SELECT tipo_voto, fecha_voto 
                  FROM votos 
                  WHERE sesion_id = ? AND user_id = ? 
                  AND item_votacion_tipo = ? AND item_votacion_id = ?
                  ORDER BY fecha_voto DESC 
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId, $userId, $tipoItem, $itemId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function getResultadosItem($sesionId, $tipoItem, $itemId) {
        $query = "SELECT 
                    tipo_voto,
                    COUNT(*) as cantidad,
                    GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name) SEPARATOR ', ') as votantes
                  FROM votos v
                  JOIN users u ON v.user_id = u.id
                  WHERE v.sesion_id = ? AND v.item_votacion_tipo = ? AND v.item_votacion_id = ?
                  GROUP BY tipo_voto";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId, $tipoItem, $itemId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getVotosDetallados($sesionId, $tipoItem, $itemId) {
        $query = "SELECT v.*, u.first_name, u.last_name, u.username
                  FROM votos v
                  JOIN users u ON v.user_id = u.id
                  WHERE v.sesion_id = ? AND v.item_votacion_tipo = ? AND v.item_votacion_id = ?
                  ORDER BY v.fecha_voto ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId, $tipoItem, $itemId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ============================================
    // GESTIÓN DEL HISTORIAL
    // ============================================
    
    public function actualizarHistorialVotacion($sesionId, $tipoItem, $itemId) {
        // Contar votos
        $query = "SELECT 
                    SUM(CASE WHEN tipo_voto = 'positivo' THEN 1 ELSE 0 END) as positivos,
                    SUM(CASE WHEN tipo_voto = 'negativo' THEN 1 ELSE 0 END) as negativos,
                    SUM(CASE WHEN tipo_voto = 'abstencion' THEN 1 ELSE 0 END) as abstenciones,
                    COUNT(*) as total_votos
                  FROM votos 
                  WHERE sesion_id = ? AND item_votacion_tipo = ? AND item_votacion_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId, $tipoItem, $itemId]);
        $resultados = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener total de presentes
        $totalPresentes = $this->getTotalPresentes($sesionId);
        
        // Determinar resultado
        $resultado = 'pendiente';
        if ($resultados['total_votos'] == $totalPresentes) {
            if ($resultados['positivos'] > $resultados['negativos']) {
                $resultado = 'aprobado';
            } elseif ($resultados['negativos'] > $resultados['positivos']) {
                $resultado = 'rechazado';
            } else {
                $resultado = 'empate';
            }
        }
        
        // Insertar o actualizar historial
        $query = "INSERT INTO historial_votacion 
                  (sesion_id, item_votacion_tipo, item_votacion_id, total_presentes,
                   votos_positivos, votos_negativos, votos_abstenciones, resultado)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE
                  total_presentes = VALUES(total_presentes),
                  votos_positivos = VALUES(votos_positivos),
                  votos_negativos = VALUES(votos_negativos),
                  votos_abstenciones = VALUES(votos_abstenciones),
                  resultado = VALUES(resultado)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $sesionId, $tipoItem, $itemId, $totalPresentes,
            $resultados['positivos'], $resultados['negativos'], 
            $resultados['abstenciones'], $resultado
        ]);
    }
    
    public function getHistorialCompleto($filtros = []) {
        $where = ['1=1'];
        $params = [];
        
        if (!empty($filtros['sesion_id'])) {
            $where[] = 'hv.sesion_id = ?';
            $params[] = $filtros['sesion_id'];
        }
        
        if (!empty($filtros['resultado'])) {
            $where[] = 'hv.resultado = ?';
            $params[] = $filtros['resultado'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $where[] = 'hv.fecha_apertura >= ?';
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $where[] = 'hv.fecha_apertura <= ?';
            $params[] = $filtros['fecha_hasta'] . ' 23:59:59';
        }
        
        $query = "SELECT hv.*, sv.nombre as nombre_sesion
                  FROM historial_votacion hv
                  JOIN sesiones_votacion sv ON hv.sesion_id = sv.id
                  WHERE " . implode(' AND ', $where) . "
                  ORDER BY hv.fecha_apertura DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ============================================
    // UTILIDADES
    // ============================================
    
    public function puedeVotar($userId) {
        // Verificar que el usuario sea editor
        $query = "SELECT r.name 
                  FROM users u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE u.id = ? AND u.status = 'active'";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['name'] === 'editor';
    }
    
    public function getEstadisticasSesion($sesionId) {
        // Obtener presentes
        $queryPresentes = "SELECT COUNT(DISTINCT user_id) as total_presentes 
                          FROM presentes_sesion 
                          WHERE sesion_id = ? AND presente = 1";
        $stmt = $this->db->prepare($queryPresentes);
        $stmt->execute([$sesionId]);
        $presentes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener votantes únicos
        $queryVotantes = "SELECT COUNT(DISTINCT user_id) as total_votantes 
                         FROM votos 
                         WHERE sesion_id = ?";
        $stmt = $this->db->prepare($queryVotantes);
        $stmt->execute([$sesionId]);
        $votantes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener total de votos
        $queryVotos = "SELECT COUNT(*) as total_votos 
                      FROM votos 
                      WHERE sesion_id = ?";
        $stmt = $this->db->prepare($queryVotos);
        $stmt->execute([$sesionId]);
        $votos = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total_presentes' => $presentes['total_presentes'] ?? 0,
            'total_votantes' => $votantes['total_votantes'] ?? 0,
            'total_votos' => $votos['total_votos'] ?? 0,
            'items_votados' => 0,
            'items_aprobados' => 0,
            'items_rechazados' => 0
        ];
    }
    
    // ============================================
    // ELIMINACIÓN DE SESIONES
    // ============================================
    
    public function eliminarSesion($sesionId, $userId) {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();
            
            // Verificar que la sesión existe y obtener información
            $sesion = $this->getSesionById($sesionId);
            if (!$sesion) {
                $this->db->rollback();
                return ['success' => false, 'message' => 'Sesión no encontrada'];
            }
            
            // Solo permitir eliminar sesiones planificadas o finalizadas
            if (!in_array($sesion['estado'], ['planificada', 'finalizada'])) {
                $this->db->rollback();
                return ['success' => false, 'message' => 'No se puede eliminar una sesión activa o pausada. Finalice la sesión primero.'];
            }
            
            // Verificar si hay votos registrados
            $votoCount = $this->contarVotosSesion($sesionId);
            if ($votoCount > 0) {
                $this->db->rollback();
                return ['success' => false, 'message' => "No se puede eliminar la sesión. Hay {$votoCount} votos registrados."];
            }
            
            // Eliminar registros relacionados en orden
            
            // 1. Eliminar presentes de la sesión
            $query = "DELETE FROM presentes_sesion WHERE sesion_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$sesionId]);
            
            // 2. Eliminar historial de votación
            $query = "DELETE FROM historial_votacion WHERE sesion_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$sesionId]);
            
            // 3. Eliminar la sesión principal
            $query = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$sesionId]);
            
            // Registrar la eliminación en un log de auditoría
            $this->registrarAuditoriaEliminacion($sesionId, $sesion, $userId);
            
            // Confirmar transacción
            $this->db->commit();
            
            return ['success' => true, 'message' => 'Sesión eliminada exitosamente'];
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error eliminando sesión {$sesionId}: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    private function contarVotosSesion($sesionId) {
        $query = "SELECT COUNT(*) as total FROM votos WHERE sesion_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    private function registrarAuditoriaEliminacion($sesionId, $sesionData, $userId) {
        $query = "INSERT INTO auditoria_votacion 
                  (accion, sesion_id, usuario_id, detalles, fecha_accion) 
                  VALUES (?, ?, ?, ?, NOW())";
        
        $detalles = json_encode([
            'accion' => 'eliminacion_sesion',
            'sesion_eliminada' => [
                'id' => $sesionId,
                'nombre' => $sesionData['nombre'],
                'estado' => $sesionData['estado']
            ]
        ]);
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['eliminar_sesion', $sesionId, $userId, $detalles]);
    }

    // ============================================
    // CONTROL PROGRESIVO DE PUNTOS DEL ORDEN DEL DÍA
    // ============================================
    
    public function inicializarPuntosSesion($sesionId) {
        // Asegurar que la tabla existe
        $this->ensurePuntosHabilitadosTable();
        
        // Obtener todos los expedientes del orden del día de la sesión
        $query = "SELECT sv.id as sesion_id
                  FROM sesiones_votacion sv
                  WHERE sv.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId]);
        $ordenDia = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$ordenDia) {
            return false;
        }
        
        // Obtener expedientes asociados
        $queryExpedientes = "SELECT ode.id, ode.numero_expediente, ode.extracto
                            FROM orden_dia_expedientes ode
                            JOIN orden_dia_items odi ON ode.orden_dia_item_id = odi.id
                            WHERE odi.orden_dia_id = ?
                            ORDER BY ode.numero_expediente";
        
        $stmt = $this->db->prepare($queryExpedientes);
        $stmt->execute([$ordenDia['orden_dia_id']]);
        $expedientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Insertar puntos en la tabla de control
        $insertQuery = "INSERT INTO puntos_habilitados 
                       (sesion_id, item_tipo, item_id, numero_expediente, extracto, orden_punto, habilitado) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)
                       ON DUPLICATE KEY UPDATE 
                       numero_expediente = VALUES(numero_expediente),
                       extracto = VALUES(extracto)";
        
        $stmt = $this->db->prepare($insertQuery);
        
        // Insertar punto global (apertura de sesión)
        $stmt->execute([$sesionId, 'global', null, 'Apertura de Sesión', 'Llamado a lista y verificación de quórum', 1, 0]);
        
        $orden = 2;
        foreach ($expedientes as $expediente) {
            $stmt->execute([
                $sesionId, 
                'expediente', 
                $expediente['id'], 
                $expediente['numero_expediente'], 
                $expediente['extracto'], 
                $orden++, 
                0
            ]);
        }
        
        // Punto de cierre
        $stmt->execute([$sesionId, 'global', null, 'Cierre de Sesión', 'Cierre de la sesión y próximos pasos', $orden, 0]);
        
        return true;
    }
    
    public function habilitarPunto($sesionId, $puntoId, $userId) {
        $this->ensurePuntosHabilitadosTable();
        
        $query = "UPDATE puntos_habilitados 
                  SET habilitado = TRUE, 
                      fecha_habilitacion = NOW(), 
                      habilitado_por = ?
                  WHERE sesion_id = ? AND id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$userId, $sesionId, $puntoId]);
    }
    
    public function deshabilitarPunto($sesionId, $puntoId) {
        $this->ensurePuntosHabilitadosTable();
        
        $query = "UPDATE puntos_habilitados 
                  SET habilitado = FALSE, 
                      fecha_habilitacion = NULL, 
                      habilitado_por = NULL
                  WHERE sesion_id = ? AND id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$sesionId, $puntoId]);
    }
    
    public function getPuntosOrdenDia($sesionId, $soloHabilitados = false) {
        $whereClause = $soloHabilitados ? "AND ph.habilitado = TRUE" : "";
        
        $query = "SELECT ph.*, 
                         u.first_name, u.last_name,
                         sv.nombre as nombre_sesion, sv.estado as estado_sesion
                  FROM puntos_habilitados ph
                  LEFT JOIN users u ON ph.habilitado_por = u.id
                  JOIN sesiones_votacion sv ON ph.sesion_id = sv.id
                  WHERE ph.sesion_id = ? {$whereClause}
                  ORDER BY ph.orden_punto";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSiguientePuntoAHabilitar($sesionId) {
        $query = "SELECT * FROM puntos_habilitados 
                  WHERE sesion_id = ? AND habilitado = FALSE 
                  ORDER BY orden_punto LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function contarPuntosHabilitados($sesionId) {
        $query = "SELECT 
                    COUNT(*) as total_puntos,
                    SUM(CASE WHEN habilitado = TRUE THEN 1 ELSE 0 END) as puntos_habilitados
                  FROM puntos_habilitados 
                  WHERE sesion_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ============================================
    // MÉTODOS PARA VISTA PÚBLICA
    // ============================================
    
    public function getSesionActivaActual() {
        $query = "SELECT sv.*, od.numero_acta, od.fecha_sesion, od.tipo_sesion
                  FROM {$this->table} sv
                  JOIN orden_dia od ON sv.orden_dia_id = od.id
                  WHERE sv.estado = 'activa'
                  ORDER BY sv.created_at DESC
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getVotacionActivaPorPunto($sesionId, $puntoId) {
        // En esta implementación no existe una tabla de 'votaciones' en curso.
        // Devolvemos null y utilizamos la fecha_habilitacion del punto como referencia temporal.
        return null;
    }
    
    public function getMiembrosPresentes($sesionId) {
        // Por ahora, devolver todos los usuarios con rol 'editor' como presentes
        // TODO: Implementar sistema de asistencia real
        $query = "SELECT u.id as user_id, u.first_name, u.last_name, r.name as role_name
                  FROM users u
                  JOIN roles r ON u.role_id = r.id
                  WHERE r.name = 'editor'
                  ORDER BY u.last_name, u.first_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPuntosHabilitados($sesionId) {
        // Asegurar que la tabla existe
        $this->ensurePuntosHabilitadosTable();
        
        // Retorna los puntos habilitados mapeados para la vista pública
        $query = "SELECT id, item_tipo, item_id, numero_expediente, extracto, orden_punto, habilitado, fecha_habilitacion
                  FROM puntos_habilitados
                  WHERE sesion_id = ? AND habilitado = 1
                  ORDER BY orden_punto ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mapear a estructura esperada por la vista pública
        $mapped = [];
        foreach ($rows as $r) {
            $descripcion = '';
            if ($r['item_tipo'] === 'expediente') {
                $descripcion = trim(($r['numero_expediente'] ? ('EXPTE N° ' . $r['numero_expediente']) : '') .
                                   ($r['extracto'] ? (' - ' . $r['extracto']) : ''));
            } else { // global u otros
                $descripcion = $r['numero_expediente'] ?: $r['extracto'] ?: 'Punto del Orden del Día';
            }

            $mapped[] = [
                'punto_id' => (int)$r['id'],
                'numero' => (int)$r['orden_punto'],
                'descripcion' => $descripcion,
                'fecha_habilitacion' => $r['fecha_habilitacion'],
                'habilitado' => (bool)$r['habilitado'],
                'ponente' => null,
            ];
        }

        return $mapped;
    }

    public function getHistorialResultadoExpediente($sesionId, $expedienteId) {
        $query = "SELECT total_presentes, votos_positivos, votos_negativos, votos_abstenciones, resultado
                  FROM historial_votacion
                  WHERE sesion_id = ? AND item_votacion_tipo = 'expediente' AND item_votacion_id = ?
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sesionId, $expedienteId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getResultadosExpedienteCounts($sesionId, $expedienteId) {
        $rows = $this->getResultadosItem($sesionId, 'expediente', $expedienteId);
        $result = ['positivo' => 0, 'negativo' => 0, 'abstencion' => 0];
        foreach ($rows as $r) {
            $tipo = $r['tipo_voto'];
            $result[$tipo] = (int)$r['cantidad'];
        }
        return $result;
    }

    // ============================================
    // SISTEMA DE MOCIONES
    // ============================================
    
    public function ensureMocionesTable() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS mociones (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sesion_id INT NOT NULL,
                usuario_id INT NOT NULL,
                tipo VARCHAR(50) NOT NULL,
                texto TEXT NOT NULL,
                autor_nombre VARCHAR(255) NOT NULL,
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                activa BOOLEAN DEFAULT TRUE,
                INDEX idx_sesion (sesion_id),
                INDEX idx_fecha (fecha_creacion),
                FOREIGN KEY (sesion_id) REFERENCES sesiones_votacion(id) ON DELETE CASCADE,
                FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $this->db->exec($query);
            return true;
        } catch (Exception $e) {
            error_log("Error creando tabla mociones: " . $e->getMessage());
            return false;
        }
    }
    
    public function crearMocion($data) {
        $this->ensureMocionesTable();
        
        try {
            // Mapeo de tipos para mostrar texto amigable
            $tiposTexto = [
                'orden' => 'Moción de orden',
                'aclaracion' => 'Solicitud de aclaración',
                'reconsideracion' => 'Moción de reconsideración', 
                'cuestion_previa' => 'Cuestión previa',
                'otro' => 'Otra moción'
            ];
            
            $query = "INSERT INTO mociones 
                      (sesion_id, usuario_id, tipo, texto, autor_nombre, fecha_creacion) 
                      VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([
                $data['sesion_id'],
                $data['usuario_id'],
                $data['tipo'],
                $data['texto'],
                $data['autor_nombre']
            ]);
            
            return $success ? $this->db->lastInsertId() : false;
            
        } catch (Exception $e) {
            error_log("Error creando moción: " . $e->getMessage());
            return false;
        }
    }
    
    public function getMocionById($id) {
        $this->ensureMocionesTable();
        
        try {
            // Mapeo de tipos para mostrar texto amigable
            $tiposTexto = [
                'orden' => 'Moción de orden',
                'aclaracion' => 'Solicitud de aclaración',
                'reconsideracion' => 'Moción de reconsideración', 
                'cuestion_previa' => 'Cuestión previa',
                'otro' => 'Otra moción'
            ];
            
            $query = "SELECT m.*, 
                      CASE 
                          WHEN m.tipo = 'orden' THEN 'Moción de orden'
                          WHEN m.tipo = 'aclaracion' THEN 'Solicitud de aclaración'
                          WHEN m.tipo = 'reconsideracion' THEN 'Moción de reconsideración'
                          WHEN m.tipo = 'cuestion_previa' THEN 'Cuestión previa'
                          ELSE 'Otra moción'
                      END as tipo_texto
                      FROM mociones m 
                      WHERE m.id = ? AND m.activa = 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error obteniendo moción: " . $e->getMessage());
            return null;
        }
    }
    
    public function getMocionesRecientes($sesionId, $desde = 0) {
        $this->ensureMocionesTable();
        
        try {
            // Solo obtener mociones activas para notificaciones automáticas
            $query = "SELECT m.*, 
                      CASE 
                          WHEN m.tipo = 'orden' THEN 'Moción de orden'
                          WHEN m.tipo = 'aclaracion' THEN 'Solicitud de aclaración'
                          WHEN m.tipo = 'reconsideracion' THEN 'Moción de reconsideración'
                          WHEN m.tipo = 'cuestion_previa' THEN 'Cuestión previa'
                          ELSE 'Otra moción'
                      END as tipo_texto
                      FROM mociones m 
                      WHERE m.sesion_id = ? AND m.id > ? AND m.activa = 1
                      ORDER BY m.fecha_creacion DESC 
                      LIMIT 10";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$sesionId, $desde]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error obteniendo mociones recientes: " . $e->getMessage());
            return [];
        }
    }
    
    public function getTodasLasMociones($sesionId) {
        $this->ensureMocionesTable();
        
        try {
            // Para el historial, mostrar todas las mociones (activas e inactivas)
            $query = "SELECT m.*, 
                      CASE 
                          WHEN m.tipo = 'orden' THEN 'Moción de orden'
                          WHEN m.tipo = 'aclaracion' THEN 'Solicitud de aclaración'
                          WHEN m.tipo = 'reconsideracion' THEN 'Moción de reconsideración'
                          WHEN m.tipo = 'cuestion_previa' THEN 'Cuestión previa'
                          ELSE 'Otra moción'
                      END as tipo_texto
                      FROM mociones m 
                      WHERE m.sesion_id = ?
                      ORDER BY m.fecha_creacion DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$sesionId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error obteniendo todas las mociones: " . $e->getMessage());
            return [];
        }
    }
    
    public function desactivarMocion($id) {
        $this->ensureMocionesTable();
        
        try {
            $query = "UPDATE mociones SET activa = 0 WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute([$id]);
            
        } catch (Exception $e) {
            error_log("Error desactivando moción: " . $e->getMessage());
            return false;
        }
    }
}
?>