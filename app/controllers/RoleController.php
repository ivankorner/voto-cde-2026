<?php
require_once 'core/Controller.php';

class RoleController extends Controller {
    
    public function index() {
        $this->requireAdmin(); // Solo admin puede gestionar roles
        
        $roleModel = $this->loadModel('Role');
        $roles = $roleModel->findAll();
        
        // Agregar conteo de usuarios por rol
        foreach ($roles as &$role) {
            $role['user_count'] = $roleModel->getUserCount($role['id']);
        }
        
        $data = [
            'roles' => $roles,
            'page_title' => 'Gestión de Roles'
        ];
        
        $this->loadView('roles/index', $data);
    }
    
    public function create() {
        $this->requireAdmin(); // Solo admin puede crear roles
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $error = $this->validateRoleData($data);
            
            if (empty($error)) {
                $roleModel = $this->loadModel('Role');
                
                // Verificar si el nombre ya existe
                if ($roleModel->nameExists($data['name'])) {
                    $error = 'El nombre del rol ya existe';
                } else {
                    if ($roleModel->createRole($data)) {
                        $success = 'Rol creado exitosamente';
                        // Limpiar datos del formulario
                        $data = [];
                    } else {
                        $error = 'Error al crear el rol';
                    }
                }
            }
        }
        
        $viewData = [
            'error' => $error,
            'success' => $success,
            'data' => $data ?? [],
            'page_title' => 'Crear Rol'
        ];
        
        $this->loadView('roles/create', $viewData);
    }
    
    public function edit() {
        $this->requireAdmin(); // Solo admin puede editar roles
        
        $id = $_GET['id'] ?? 0;
        $roleModel = $this->loadModel('Role');
        $role = $roleModel->findById($id);
        
        if (!$role) {
            $this->redirect('roles');
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $error = $this->validateRoleData($data);
            
            if (empty($error)) {
                // Verificar si el nombre ya existe (excluyendo el rol actual)
                if ($roleModel->nameExists($data['name'], $id)) {
                    $error = 'El nombre del rol ya existe';
                } else {
                    if ($roleModel->updateRole($id, $data)) {
                        $success = 'Rol actualizado exitosamente';
                        // Actualizar datos del rol mostrado
                        $role = $roleModel->findById($id);
                    } else {
                        $error = 'Error al actualizar el rol';
                    }
                }
            }
        }
        
        $viewData = [
            'role' => $role,
            'error' => $error,
            'success' => $success,
            'page_title' => 'Editar Rol'
        ];
        
        $this->loadView('roles/edit', $viewData);
    }
    
    public function delete() {
        $this->requireAdmin(); // Solo admin puede eliminar roles
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $roleModel = $this->loadModel('Role');
            
            // Verificar si el rol se puede eliminar
            if (!$roleModel->canDelete($id)) {
                $_SESSION['error'] = 'No se puede eliminar el rol porque tiene usuarios asignados';
            } else {
                if ($roleModel->delete($id)) {
                    $_SESSION['success'] = 'Rol eliminado exitosamente';
                } else {
                    $_SESSION['error'] = 'Error al eliminar el rol';
                }
            }
        }
        
        $this->redirect('roles');
    }
    
    private function validateRoleData($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'El nombre del rol es requerido';
        } elseif (strlen($data['name']) < 3) {
            $errors[] = 'El nombre del rol debe tener al menos 3 caracteres';
        }
        
        if (empty($data['description'])) {
            $errors[] = 'La descripción es requerida';
        }
        
        return implode(', ', $errors);
    }
}
?>
