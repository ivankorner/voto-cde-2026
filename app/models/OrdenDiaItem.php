<?php
require_once 'core/Model.php';

class OrdenDiaItem extends Model {
    
    protected $table = 'orden_dia_items';
    
    public function getByOrdenDiaId($ordenDiaId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE orden_dia_id = ? 
                  ORDER BY numero_orden ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$ordenDiaId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET descripcion = ?, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['descripcion'],
            $id
        ]);
    }
    
    // Obtener expedientes de un ítem
    public function getExpedientes($itemId) {
        $query = "SELECT * FROM orden_dia_expedientes 
                  WHERE orden_dia_item_id = ? 
                  ORDER BY id ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$itemId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Agregar expediente a un ítem
    public function addExpediente($itemId, $data) {
        $query = "INSERT INTO orden_dia_expedientes 
                  (orden_dia_item_id, numero_expediente, extracto, comision, 
                   tipo_instrumento, bloque_autor, concejal_autor, nombre_ciudadano) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $itemId,
            $data['numero_expediente'] ?? null,
            $data['extracto'] ?? null,
            $data['comision'] ?? null,
            $data['tipo_instrumento'] ?? null,
            $data['bloque_autor'] ?? null,
            $data['concejal_autor'] ?? null,
            $data['nombre_ciudadano'] ?? null
        ]);
    }
    
    // Eliminar expediente
    public function deleteExpediente($expedienteId) {
        $query = "DELETE FROM orden_dia_expedientes WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$expedienteId]);
    }
    
    // Actualizar expediente
    public function updateExpediente($expedienteId, $data) {
        $query = "UPDATE orden_dia_expedientes 
                  SET numero_expediente = ?, extracto = ?, comision = ?, 
                      tipo_instrumento = ?, bloque_autor = ?, concejal_autor = ?, 
                      nombre_ciudadano = ?, updated_at = CURRENT_TIMESTAMP
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['numero_expediente'] ?? null,
            $data['extracto'] ?? null,
            $data['comision'] ?? null,
            $data['tipo_instrumento'] ?? null,
            $data['bloque_autor'] ?? null,
            $data['concejal_autor'] ?? null,
            $data['nombre_ciudadano'] ?? null,
            $expedienteId
        ]);
    }
    
    // Obtener actas de un ítem (para el ítem 3)
    public function getActas($itemId) {
        $query = "SELECT * FROM orden_dia_actas 
                  WHERE orden_dia_item_id = ? 
                  ORDER BY fecha_acta DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$itemId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Agregar acta a un ítem
    public function addActa($itemId, $data) {
        $query = "INSERT INTO orden_dia_actas 
                  (orden_dia_item_id, numero_acta, tipo_sesion, fecha_acta) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $itemId,
            $data['numero_acta'],
            $data['tipo_sesion'],
            $data['fecha_acta']
        ]);
    }
    
    // Eliminar acta
    public function deleteActa($actaId) {
        $query = "DELETE FROM orden_dia_actas WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$actaId]);
    }
    
    // Actualizar acta
    public function updateActa($actaId, $data) {
        $query = "UPDATE orden_dia_actas 
                  SET numero_acta = ?, tipo_sesion = ?, fecha_acta = ?, 
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['numero_acta'],
            $data['tipo_sesion'],
            $data['fecha_acta'],
            $actaId
        ]);
    }
    
    // Obtener ítem con todos sus datos relacionados
    public function getItemCompleto($itemId) {
        $item = $this->getById($itemId);
        if (!$item) {
            return null;
        }
        
        $item['expedientes'] = $this->getExpedientes($itemId);
        $item['actas'] = $this->getActas($itemId);
        
        return $item;
    }
    
    // Obtener todos los ítems de una orden del día con sus datos
    public function getItemsCompletos($ordenDiaId) {
        $items = $this->getByOrdenDiaId($ordenDiaId);
        
        foreach ($items as &$item) {
            $item['expedientes'] = $this->getExpedientes($item['id']);
            $item['actas'] = $this->getActas($item['id']);
        }
        
        return $items;
    }
    
    // Verificar si un ítem permite expedientes
    public function permiteExpedientes($tipoItem) {
        $tiposConExpedientes = [
            'expedientes_fuera_termino',
            'tratamientos',
            'proyectos_concejales',
            'proyectos_ejecutivo',
            'notas_ejecutivo',
            'notas_oficiales',
            'notas_particulares',
            'dictamenes_comisiones',
            'temas_internos'
        ];
        
        return in_array($tipoItem, $tiposConExpedientes);
    }
    
    // Verificar si un ítem permite actas (solo el ítem 3)
    public function permiteActas($tipoItem) {
        return $tipoItem === 'lectura_actas';
    }
    
    // Verificar si un ítem es espacio ciudadano
    public function esEspacioCiudadano($tipoItem) {
        return $tipoItem === 'espacio_ciudadano';
    }
    
    // Obtener expediente por ID
    public function getExpedienteById($expedienteId) {
        $query = "SELECT * FROM orden_dia_expedientes WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$expedienteId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Obtener acta por ID
    public function getActaById($actaId) {
        $query = "SELECT * FROM orden_dia_actas WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$actaId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>