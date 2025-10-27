<?php
require_once 'core/Controller.php';

class UserController extends Controller {
    
    public function index() {
        $this->requireAdmin(); // Solo admin puede ver usuarios
        
        $userModel = $this->loadModel('User');
        $users = $userModel->getUsersWithRoles();
        
        $data = [
            'users' => $users,
            'page_title' => 'Gestión de Usuarios'
        ];
        
        $this->loadView('users/index', $data);
    }
    
    public function create() {
        $this->requireAdmin(); // Solo admin puede crear usuarios
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'puesto' => $_POST['puesto'] ?? null,
                'role_id' => $_POST['role_id'] ?? '',
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $error = $this->validateUserData($data);
            
            if (empty($error)) {
                $userModel = $this->loadModel('User');
                
                // Verificar si el email ya existe
                if ($userModel->emailExists($data['email'])) {
                    $error = 'El email ya está registrado';
                } elseif ($userModel->usernameExists($data['username'])) {
                    $error = 'El nombre de usuario ya existe';
                } else {
                    if ($userModel->createUser($data)) {
                        $success = 'Usuario creado exitosamente';
                        // Limpiar datos del formulario
                        $data = [];
                    } else {
                        $error = 'Error al crear el usuario';
                    }
                }
            }
        }
        
        $roleModel = $this->loadModel('Role');
        $roles = $roleModel->findAll();
        
        $viewData = [
            'roles' => $roles,
            'error' => $error,
            'success' => $success,
            'data' => $data ?? [],
            'page_title' => 'Crear Usuario'
        ];
        
        $this->loadView('users/create', $viewData);
    }
    
    public function edit() {
        $this->requireAdmin(); // Solo admin puede editar usuarios
        
        $id = $_GET['id'] ?? 0;
        $userModel = $this->loadModel('User');
        $user = $userModel->findById($id);
        
        if (!$user) {
            $this->redirect('users');
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'puesto' => $_POST['puesto'] ?? null,
                'role_id' => $_POST['role_id'] ?? '',
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $error = $this->validateUserData($data, $id);
            
            if (empty($error)) {
                // Verificar si el email ya existe (excluyendo el usuario actual)
                if ($userModel->emailExists($data['email'], $id)) {
                    $error = 'El email ya está registrado';
                } elseif ($userModel->usernameExists($data['username'], $id)) {
                    $error = 'El nombre de usuario ya existe';
                } else {
                    if ($userModel->updateUser($id, $data)) {
                        $success = 'Usuario actualizado exitosamente';
                        // Actualizar datos del usuario mostrado
                        $user = $userModel->findById($id);
                    } else {
                        $error = 'Error al actualizar el usuario';
                    }
                }
            }
        }
        
        $roleModel = $this->loadModel('Role');
        $roles = $roleModel->findAll();
        
        $viewData = [
            'user' => $user,
            'roles' => $roles,
            'error' => $error,
            'success' => $success,
            'page_title' => 'Editar Usuario'
        ];
        
        $this->loadView('users/edit', $viewData);
    }
    
    public function delete() {
        $this->requireAdmin(); // Solo admin puede eliminar usuarios
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            
            // No permitir que el admin se elimine a sí mismo
            if ($id == $_SESSION['user_id']) {
                $_SESSION['error'] = 'No puedes eliminar tu propio usuario';
            } else {
                $userModel = $this->loadModel('User');
                if ($userModel->delete($id)) {
                    $_SESSION['success'] = 'Usuario eliminado exitosamente';
                } else {
                    $_SESSION['error'] = 'Error al eliminar el usuario';
                }
            }
        }
        
        $this->redirect('users');
    }
    
    private function validateUserData($data, $excludeId = null) {
        $errors = [];
        
        if (empty($data['username'])) {
            $errors[] = 'El nombre de usuario es requerido';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'El email es requerido';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido';
        }
        
        if (empty($data['first_name'])) {
            $errors[] = 'El nombre es requerido';
        }
        
        if (empty($data['last_name'])) {
            $errors[] = 'El apellido es requerido';
        }
        
        if (empty($data['role_id'])) {
            $errors[] = 'El rol es requerido';
        }
        
        // Validar puesto si se proporciona
        if (!empty($data['puesto'])) {
            $puestosValidos = ['Presidente', 'Vice Presidente', 'Concejal', 'Secretario', 'Pro Secretario'];
            if (!in_array($data['puesto'], $puestosValidos)) {
                $errors[] = 'El puesto seleccionado no es válido';
            }
        }
        
        // Validar contraseña solo si es un nuevo usuario o se está proporcionando una nueva contraseña
        if ($excludeId === null && empty($data['password'])) {
            $errors[] = 'La contraseña es requerida';
        } elseif (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        return implode(', ', $errors);
    }
}
?>
