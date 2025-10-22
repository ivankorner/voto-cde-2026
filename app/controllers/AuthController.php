<?php
require_once 'core/Controller.php';

class AuthController extends Controller {
    
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error = 'Por favor, complete todos los campos';
            } else {
                $userModel = $this->loadModel('User');
                $user = $userModel->validateLogin($username, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_username'] = $user['username'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    
                    // Obtener el rol del usuario
                    $roleModel = $this->loadModel('Role');
                    $role = $roleModel->findById($user['role_id']);
                    $_SESSION['user_role'] = $role ? $role['name'] : 'user';
                    
                    $this->redirect('dashboard');
                } else {
                    $error = 'Credenciales incorrectas';
                }
            }
        }
        
        $this->loadView('auth/login', ['error' => $error]);
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('login');
    }
    
    // Método para desarrollo: cambio rápido de usuario
    public function switchUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboard');
        }
        
        $userId = $_POST['user_id'] ?? '';
        
        if (empty($userId)) {
            $_SESSION['error_message'] = 'Usuario no válido';
            $this->redirect('dashboard');
        }
        
        $userModel = $this->loadModel('User');
        $user = $userModel->findById($userId);
        
        if (!$user) {
            $_SESSION['error_message'] = 'Usuario no encontrado';
            $this->redirect('dashboard');
        }
        
        // Cambiar sesión al nuevo usuario
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        
        // Obtener el rol del usuario
        $roleModel = $this->loadModel('Role');
        $role = $roleModel->findById($user['role_id']);
        $_SESSION['user_role'] = $role ? $role['name'] : 'user';
        
        $_SESSION['success_message'] = 'Cambiado a usuario: ' . $user['username'] . ' (' . $role['name'] . ')';
        
        // Redirigir a la página anterior o dashboard
        $redirect = $_POST['redirect'] ?? 'dashboard';
        $this->redirect($redirect);
    }
}
?>
