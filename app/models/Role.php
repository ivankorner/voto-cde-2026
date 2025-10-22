<?php
require_once 'core/Model.php';

class Role extends Model {
    protected $table = 'roles';
    
    public function findByName($name) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
    
    public function createRole($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data);
    }
    
    public function updateRole($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->update($id, $data);
    }
    
    public function nameExists($name, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = ?";
        $params = [$name];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    public function getUserCount($roleId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE role_id = ?");
        $stmt->execute([$roleId]);
        $result = $stmt->fetch();
        
        return $result['count'];
    }
    
    public function canDelete($roleId) {
        // No se puede eliminar un rol si tiene usuarios asignados
        return $this->getUserCount($roleId) == 0;
    }
}
?>
