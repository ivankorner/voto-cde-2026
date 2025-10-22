<?php
class Controller {
    protected function loadModel($model) {
        require_once MODELS_PATH . $model . '.php';
        return new $model();
    }
    
    protected function loadView($view, $data = []) {
        extract($data);
        require_once VIEWS_PATH . $view . '.php';
    }
    
    protected function redirect($url) {
        // Si la URL contiene http/https, redirigir directamente
        if (strpos($url, 'http') === 0) {
            header('Location: ' . $url);
            exit();
        }
        
        // Para URLs internas, usar el formato correcto según la configuración
        if (empty($url)) {
            $redirectUrl = BASE_URL;
        } else {
            // Construir URL compatible con servidores sin mod_rewrite
            $redirectUrl = BASE_URL . 'index.php?url=' . ltrim($url, '/');
        }
        
        header('Location: ' . $redirectUrl);
        exit();
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
        }
    }
    
    protected function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    protected function requireRole($role) {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            $this->redirect('dashboard');
        }
    }
    
    protected function isAdmin() {
        return $this->hasRole('admin');
    }
    
    protected function requireAdmin() {
        $this->requireRole('admin');
    }
    
    protected function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    protected function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
