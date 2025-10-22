<?php
require_once 'core/Model.php';

class OrdenDia extends Model {
    
    protected $table = 'orden_dia';
    
    public function getAll() {
        $query = "SELECT od.*, u.username as created_by_name 
                  FROM {$this->table} od 
                  LEFT JOIN users u ON od.created_by = u.id 
                  ORDER BY od.fecha_sesion DESC, od.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT od.*, u.username as created_by_name 
                  FROM {$this->table} od 
                  LEFT JOIN users u ON od.created_by = u.id 
                  WHERE od.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (numero_acta, fecha_sesion, hora_sesion, tipo_sesion, created_by) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([
            $data['numero_acta'],
            $data['fecha_sesion'],
            $data['hora_sesion'],
            $data['tipo_sesion'],
            $data['created_by']
        ]);
        
        if ($result) {
            $ordenId = $this->db->lastInsertId();
            
            // Crear ítems estándar automáticamente
            $this->crearItemsEstandar($ordenId);
            
            return $ordenId;
        }
        return false;
    }
    
    private function crearItemsEstandar($ordenDiaId) {
        // Verificar si ya existen ítems para esta orden del día
        $queryCheck = "SELECT COUNT(*) as count FROM orden_dia_items WHERE orden_dia_id = ?";
        $stmtCheck = $this->db->prepare($queryCheck);
        $stmtCheck->execute([$ordenDiaId]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        // Solo crear ítems si no existen
        if ($result['count'] == 0) {
            $items = [
                [1, 'IZAMIENTO DEL PABELLÓN NACIONAL', 'protocolo'],
                [2, 'LECTURA ORDEN DEL DIA', 'protocolo'],
                [3, 'LECTURA Y CONSIDERACIÓN DE ACTAS', 'lectura_actas'],
                [4, 'EXPEDIENTES INGRESADO FUERA DE TÉRMINO', 'expedientes_fuera_termino'],
                [5, 'TRATAMIENTOS: SOBRE TABLAS, DE PREFERENCIA Y/O DE RECONSIDERACIÓN', 'tratamientos'],
                [6, 'PROYECTOS PRESENTADOS POR LAS Y LOS CONCEJALES', 'proyectos_concejales'],
                [7, 'PROYECTOS PRESENTADOS POR EL PODER EJECUTIVO MUNICIPAL', 'proyectos_ejecutivo'],
                [8, 'NOTAS DEL PODER EJECUTIVO', 'notas_ejecutivo'],
                [9, 'NOTAS DE ASUNTOS OFICIALES', 'notas_oficiales'],
                [10, 'NOTAS DE ASUNTOS PARTICULARES', 'notas_particulares'],
                [11, 'ESPACIO CIUDADANO', 'espacio_ciudadano'],
                [12, 'DICTAMENES DE COMISIONES', 'dictamenes_comisiones'],
                [13, 'HOMENAJES', 'homenajes'],
                [14, 'TEMAS INTERNOS', 'temas_internos']
            ];
            
            $sql = "INSERT INTO orden_dia_items (orden_dia_id, numero_orden, titulo, tipo_item) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            
            foreach ($items as $item) {
                $stmt->execute([$ordenDiaId, $item[0], $item[1], $item[2]]);
            }
        }
    }
    
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET numero_acta = ?, fecha_sesion = ?, hora_sesion = ?, 
                      tipo_sesion = ?, estado = ?, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['numero_acta'],
            $data['fecha_sesion'],
            $data['hora_sesion'],
            $data['tipo_sesion'],
            $data['estado'],
            $id
        ]);
    }
    
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
    
    public function getByNumeroActa($numeroActa) {
        $query = "SELECT * FROM {$this->table} WHERE numero_acta = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$numeroActa]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getRecientes($limit = 5) {
        $query = "SELECT od.*, u.username as created_by_name 
                  FROM {$this->table} od 
                  LEFT JOIN users u ON od.created_by = u.id 
                  ORDER BY od.created_at DESC 
                  LIMIT ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByEstado($estado) {
        $query = "SELECT od.*, u.username as created_by_name 
                  FROM {$this->table} od 
                  LEFT JOIN users u ON od.created_by = u.id 
                  WHERE od.estado = ? 
                  ORDER BY od.fecha_sesion DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$estado]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getItemsWithDetails($ordenDiaId) {
        $query = "SELECT odi.*, 
                  COUNT(ode.id) as total_expedientes,
                  COUNT(oda.id) as total_actas
                  FROM orden_dia_items odi 
                  LEFT JOIN orden_dia_expedientes ode ON odi.id = ode.orden_dia_item_id
                  LEFT JOIN orden_dia_actas oda ON odi.id = oda.orden_dia_item_id
                  WHERE odi.orden_dia_id = ? 
                  GROUP BY odi.id
                  ORDER BY odi.numero_orden ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$ordenDiaId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateEstado($id, $estado) {
        $query = "UPDATE {$this->table} 
                  SET estado = ?, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$estado, $id]);
    }
    
    public function getEstadisticas() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'borrador' THEN 1 ELSE 0 END) as borradores,
                    SUM(CASE WHEN estado = 'publicado' THEN 1 ELSE 0 END) as publicados,
                    SUM(CASE WHEN estado = 'archivado' THEN 1 ELSE 0 END) as archivados,
                    SUM(CASE WHEN fecha_sesion >= CURDATE() THEN 1 ELSE 0 END) as proximas_sesiones
                  FROM {$this->table}";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function validarNumeroActa($numeroActa, $excludeId = null) {
        $query = "SELECT id FROM {$this->table} WHERE numero_acta = ?";
        $params = [$numeroActa];
        
        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) === false; // true si no existe
    }
    
    public function inicializarItemsEstandar($ordenDiaId) {
        // Función pública para inicializar ítems si no existen
        $this->crearItemsEstandar($ordenDiaId);
    }
}
?>