<?php
require_once 'core/Controller.php';

class DashboardController extends Controller {
    
    public function index() {
        $this->requireLogin();
        
        $data = [
            'page_title' => 'Dashboard'
        ];
        
        // Solo cargar estadísticas y usuarios para administradores
        if ($_SESSION['user_role'] === 'admin') {
            // Estadísticas del dashboard
            $userModel = $this->loadModel('User');
            $roleModel = $this->loadModel('Role');
            
            $stats = [
                'total_users' => $userModel->count(),
                'total_roles' => $roleModel->count(),
            ];
            
            // Usuarios recientes
            $recentUsers = $userModel->getUsersWithRoles();
            $recentUsers = array_slice($recentUsers, 0, 5); // Solo los 5 más recientes
            
            $data['stats'] = $stats;
            $data['recent_users'] = $recentUsers;
        } else {
            // Para editores y viewers, solo datos básicos
            $data['stats'] = [];
            $data['recent_users'] = [];
        }
        
        $this->loadView('dashboard/index', $data);
    }
}
?>
