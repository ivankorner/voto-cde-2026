<?php
class Router {
    private $routes = [];
    
    public function __construct() {
        $this->addRoutes();
    }
    
    private function addRoutes() {
        // Rutas del sistema
        $this->routes = [
            '' => ['controller' => 'AuthController', 'method' => 'login'],
            'login' => ['controller' => 'AuthController', 'method' => 'login'],
            'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
            'auth/login' => ['controller' => 'AuthController', 'method' => 'login'],
            'auth/logout' => ['controller' => 'AuthController', 'method' => 'logout'],
            'auth/switchUser' => ['controller' => 'AuthController', 'method' => 'switchUser'],
            'dashboard' => ['controller' => 'DashboardController', 'method' => 'index'],
            'users' => ['controller' => 'UserController', 'method' => 'index'],
            'users/create' => ['controller' => 'UserController', 'method' => 'create'],
            'users/edit' => ['controller' => 'UserController', 'method' => 'edit'],
            'users/delete' => ['controller' => 'UserController', 'method' => 'delete'],
            'roles' => ['controller' => 'RoleController', 'method' => 'index'],
            'roles/create' => ['controller' => 'RoleController', 'method' => 'create'],
            'roles/edit' => ['controller' => 'RoleController', 'method' => 'edit'],
            'roles/delete' => ['controller' => 'RoleController', 'method' => 'delete'],
            // Rutas de Orden del Día
            'orden_dia' => ['controller' => 'OrdenDiaController', 'method' => 'index'],
            'orden_dia/create' => ['controller' => 'OrdenDiaController', 'method' => 'create'],
            'orden_dia/edit' => ['controller' => 'OrdenDiaController', 'method' => 'edit'],
            'orden_dia/update' => ['controller' => 'OrdenDiaController', 'method' => 'update'],
            'orden_dia/delete' => ['controller' => 'OrdenDiaController', 'method' => 'delete'],
            'orden_dia/view' => ['controller' => 'OrdenDiaController', 'method' => 'view'],
            'orden_dia/lista' => ['controller' => 'OrdenDiaController', 'method' => 'lista'],
            'orden_dia/updateItem' => ['controller' => 'OrdenDiaController', 'method' => 'updateItem'],
            'orden_dia/addExpediente' => ['controller' => 'OrdenDiaController', 'method' => 'addExpediente'],
            'orden_dia/deleteExpediente' => ['controller' => 'OrdenDiaController', 'method' => 'deleteExpediente'],
            'orden_dia/addActa' => ['controller' => 'OrdenDiaController', 'method' => 'addActa'],
            'orden_dia/deleteActa' => ['controller' => 'OrdenDiaController', 'method' => 'deleteActa'],
            'orden_dia/cambiarEstado' => ['controller' => 'OrdenDiaController', 'method' => 'cambiarEstado'],
            // Rutas de Votación
            'votacion' => ['controller' => 'VotacionController', 'method' => 'index'],
            'votacion/crear' => ['controller' => 'VotacionController', 'method' => 'crear'],
            'votacion/votar' => ['controller' => 'VotacionController', 'method' => 'votar'],
            'votacion/historial' => ['controller' => 'VotacionController', 'method' => 'historial'],
            'pantalla-grande' => ['controller' => 'VotacionController', 'method' => 'pantallaGrande'],
        ];
    }
    
    public function run() {
        $url = $this->getUrl();
        $urlParts = explode('/', $url);
        
        // Verificar rutas exactas primero
        if (array_key_exists($url, $this->routes)) {
            $controller = $this->routes[$url]['controller'];
            $method = $this->routes[$url]['method'];
            
            $this->callController($controller, $method);
            return;
        }
        
        // Verificar rutas dinámicas
        if (count($urlParts) >= 2) {
            $baseRoute = $urlParts[0];
            $action = $urlParts[1];
            $id = $urlParts[2] ?? null;
            $param2 = $urlParts[3] ?? null;
            $param3 = $urlParts[4] ?? null;
            
            // Rutas para usuarios
            if ($baseRoute === 'users') {
                $this->handleDynamicRoute('UserController', $action, $id);
                return;
            }
            
            // Rutas para roles
            if ($baseRoute === 'roles') {
                $this->handleDynamicRoute('RoleController', $action, $id);
                return;
            }
            
            // Rutas para orden del día
            if ($baseRoute === 'orden_dia') {
                $this->handleDynamicRoute('OrdenDiaController', $action, $id);
                return;
            }
            
            // Rutas para votación
            if ($baseRoute === 'votacion') {
                $this->handleVotacionRoute($action, $id, $param2, $param3);
                return;
            }
        }
        
        // Si no se encuentra ninguna ruta, mostrar error 404
        $this->callController('ErrorController', 'notFound');
    }
    
    private function handleDynamicRoute($controllerName, $action, $id = null) {
        require_once CONTROLLERS_PATH . $controllerName . '.php';
        $controller = new $controllerName();
        
        if ($id && method_exists($controller, $action)) {
            $controller->$action($id);
        } elseif (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            $this->callController('ErrorController', 'notFound');
        }
    }
    
    private function handleVotacionRoute($action, $id = null, $param2 = null, $param3 = null) {
        require_once CONTROLLERS_PATH . 'VotacionController.php';
        $controller = new VotacionController();
        
        switch ($action) {
            case 'votar':
                if (method_exists($controller, 'votar')) {
                    $controller->votar();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'sesion':
                if ($id && method_exists($controller, 'sesion')) {
                    $controller->sesion($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'iniciarSesion':
                if ($id && method_exists($controller, 'iniciarSesion')) {
                    $controller->iniciarSesion($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'pausarSesion':
                if ($id && method_exists($controller, 'pausarSesion')) {
                    $controller->pausarSesion($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'finalizarSesion':
                if ($id && method_exists($controller, 'finalizarSesion')) {
                    $controller->finalizarSesion($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'eliminarSesion':
                if ($id && method_exists($controller, 'eliminarSesion')) {
                    $controller->eliminarSesion($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'marcarPresencia':
                if ($id && method_exists($controller, 'marcarPresencia')) {
                    $controller->marcarPresencia($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'abandonarSesion':
                if ($id && method_exists($controller, 'abandonarSesion')) {
                    $controller->abandonarSesion($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'resultados':
                if ($id && $param2 && $param3 && method_exists($controller, 'resultados')) {
                    $controller->resultados($id, $param2, $param3);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'panel-control':
                if ($id && method_exists($controller, 'panelControl')) {
                    $controller->panelControl($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'habilitar-punto':
                if (method_exists($controller, 'habilitarPunto')) {
                    $controller->habilitarPunto();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'deshabilitar-punto':
                if (method_exists($controller, 'deshabilitarPunto')) {
                    $controller->deshabilitarPunto();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'estado-puntos':
                if ($id && method_exists($controller, 'obtenerEstadoPuntos')) {
                    $controller->obtenerEstadoPuntos($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'enviar-mocion':
                if (method_exists($controller, 'enviarMocion')) {
                    $controller->enviarMocion();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'verificar-mociones':
                if ($id && method_exists($controller, 'verificarMociones')) {
                    // El segundo parámetro sería el "desde" (opcional)
                    $desde = isset($_GET['desde']) ? (int)$_GET['desde'] : 0;
                    $controller->verificarMociones($id, $desde);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'obtener-mociones':
                if ($id && method_exists($controller, 'obtenerMociones')) {
                    $controller->obtenerMociones($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'parar-mocion':
                if (method_exists($controller, 'pararMocion')) {
                    $controller->pararMocion();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'admin-mociones':
                if (method_exists($controller, 'adminMociones')) {
                    $controller->adminMociones();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'admin-parar-todas-mociones':
                if (method_exists($controller, 'adminPararTodasMociones')) {
                    $controller->adminPararTodasMociones();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'reactivar-mocion':
                if (method_exists($controller, 'reactivarMocion')) {
                    $controller->reactivarMocion();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'limpiar-historial-mociones':
                if (method_exists($controller, 'limpiarHistorialMociones')) {
                    $controller->limpiarHistorialMociones();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'debug-mociones':
                if (method_exists($controller, 'debugMociones')) {
                    $controller->debugMociones();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'reactivar-todas-mociones':
                if (method_exists($controller, 'reactivarTodasMociones')) {
                    $controller->reactivarTodasMociones();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                

                
            case 'inicializar-puntos':
                if ($id && method_exists($controller, 'inicializarPuntos')) {
                    $controller->inicializarPuntos($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'api':
                if ($id && method_exists($controller, 'api')) {
                    $controller->api($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'vista-publica':
                if (method_exists($controller, 'vistaPublica')) {
                    $controller->vistaPublica($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'pantalla-grande':
                if (method_exists($controller, 'pantallaGrande')) {
                    $controller->pantallaGrande($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            case 'estado-publico-json':
                // API pública - NO requiere autenticación
                if (method_exists($controller, 'estadoPublicoJson')) {
                    $controller->estadoPublicoJson($id);
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
                
            default:
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    $this->callController('ErrorController', 'notFound');
                }
                break;
        }
    }
    
    private function getUrl() {
        // Primero intentar obtener la URL del parámetro GET
        if (isset($_GET['url'])) {
            $url = $_GET['url'];
        } else {
            // Si no hay parámetro GET, intentar obtener de REQUEST_URI (mod_rewrite)
            $request_uri = $_SERVER['REQUEST_URI'] ?? '';
            
            // Remover parámetros GET de la URI
            $uri = strtok($request_uri, '?');
            
            // Obtener el directorio base
            $script_name = dirname($_SERVER['SCRIPT_NAME']);
            if ($script_name !== '/') {
                $uri = str_replace($script_name, '', $uri);
            }
            
            $url = ltrim($uri, '/');
        }
        
        return rtrim($url, '/');
    }
    
    private function callController($controllerName, $methodName) {
        require_once CONTROLLERS_PATH . $controllerName . '.php';
        
        $controller = new $controllerName();
        
        if (method_exists($controller, $methodName)) {
            $controller->$methodName();
        } else {
            $this->callController('ErrorController', 'notFound');
        }
    }
}
?>
