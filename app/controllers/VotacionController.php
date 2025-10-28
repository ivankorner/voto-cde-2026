<?php
require_once 'core/Controller.php';

class VotacionController extends Controller {
    
    // ============================================
    // GESTIÓN DE SESIONES
    // ============================================
    
    public function index() {
        $this->requireLogin(); // Requiere estar logueado (admin o editor)
        
        $votacionModel = $this->loadModel('Votacion');
        
        // Determinar qué vista mostrar según el rol
        $isAdmin = ($_SESSION['user_role'] === 'admin');
        
        // Los admins ven todas las sesiones, otros usuarios solo las activas
        $sesiones = $isAdmin ? $votacionModel->getTodasLasSesiones() : $votacionModel->getSesionesActivas();
        
        $data = [
            'sesiones' => $sesiones,
            'page_title' => $isAdmin ? 'Gestión de Votaciones' : 'Sala de Votaciones',
            'is_admin' => $isAdmin
        ];
        
        // Mostrar vista diferente según el rol
        $view = $isAdmin ? 'votacion/index' : 'votacion/sala';
        $this->loadView($view, $data);
    }
    
    public function crear() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }
        
        // Obtener órdenes del día disponibles
        $ordenDiaModel = $this->loadModel('OrdenDia');
        $ordenesDelDia = $ordenDiaModel->getAll();
        
        $data = [
            'ordenes_del_dia' => $ordenesDelDia,
            'page_title' => 'Crear Sesión de Votación'
        ];
        
        $this->loadView('votacion/crear', $data);
    }
    
    public function store() {
        $this->requireAdmin();
        
        $this->validateCSRF();
        
        // Validaciones
        $errors = [];
        
        if (empty($_POST['orden_dia_id'])) {
            $errors[] = 'Debe seleccionar un orden del día';
        }
        
        if (empty($_POST['nombre_sesion'])) {
            $errors[] = 'El nombre de la sesión es obligatorio';
        }
        
        if (!empty($errors)) {
            $_SESSION['error_message'] = implode('<br>', $errors);
            header('Location: ' . BASE_URL . 'votacion/crear');
            exit;
        }
        
        $votacionModel = $this->loadModel('Votacion');
        
        $data = [
            'orden_dia_id' => $_POST['orden_dia_id'],
            'nombre_sesion' => $_POST['nombre_sesion'],
            'descripcion' => $_POST['descripcion'] ?? '',
            'created_by' => $_SESSION['user_id']
        ];
        
        $sesionId = $votacionModel->crearSesion($data);
        
        if ($sesionId) {
            // Inicializar automáticamente los puntos del orden del día
            $puntosInicializados = $votacionModel->inicializarPuntosSesion($sesionId);
            
            if ($puntosInicializados) {
                $_SESSION['flash_success'] = 'Sesión de votación creada exitosamente con puntos del orden del día inicializados';
            } else {
                $_SESSION['flash_error'] = 'Sesión creada, pero hubo un problema al inicializar los puntos del orden del día';
            }
            
            header('Location: ' . BASE_URL . 'votacion/sesion/' . $sesionId);
        } else {
            $_SESSION['flash_error'] = 'Error al crear la sesión de votación';
            header('Location: ' . BASE_URL . 'votacion/crear');
        }
        exit;
    }
    
    // ============================================
    // SALA DE VOTACIÓN
    // ============================================
    
    public function sesion($id) {
        $this->requireLogin(); // Cualquier usuario logueado puede ver
        
        $votacionModel = $this->loadModel('Votacion');
        $sesion = $votacionModel->getSesionById($id);
        
        if (!$sesion) {
            $_SESSION['error_message'] = 'Sesión de votación no encontrada';
            header('Location: ' . BASE_URL . 'votacion');
            exit;
        }
        
        // Verificar si es editor para poder votar
        $puedeVotar = $votacionModel->puedeVotar($_SESSION['user_id']);
        
        // Verificar si el usuario ya está presente en la sesión
        $usuarioYaPresente = $votacionModel->usuarioEstaPresente($id, $_SESSION['user_id']);
        
        // Obtener datos de la sesión
        $presentes = $votacionModel->getPresentesSesion($id);
        $estadisticas = $votacionModel->getEstadisticasSesion($id);
        
        // Obtener ítems del orden del día para votar
        $ordenDiaItemModel = $this->loadModel('OrdenDiaItem');
        $items = $ordenDiaItemModel->getItemsCompletos($sesion['orden_dia_id']);
        
        // Obtener puntos habilitados si el usuario es editor
        $puntosHabilitados = [];
        if ($_SESSION['user_role'] === 'editor') {
            $puntosHabilitados = $votacionModel->getPuntosOrdenDia($id, true); // Solo habilitados para editores
        } else {
            $puntosHabilitados = $votacionModel->getPuntosOrdenDia($id, false); // Todos para admins
        }
        
        // Determinar si el punto de 'Actas' está habilitado por el administrador
        $actasHabilitada = $votacionModel->isPuntoHabilitado($id, 'actas', 0);

        // Crear un mapa de expedientes habilitados
        $expedientesHabilitados = [];
        foreach ($puntosHabilitados as $punto) {
            if ($punto['item_tipo'] === 'expediente' && $punto['habilitado']) {
                $expedientesHabilitados[$punto['item_id']] = true;
            }
        }
        
        // Agregar información de votación a cada ítem
        foreach ($items as &$item) {
            // Para expedientes, verificar voto específico de cada expediente
            if (!empty($item['expedientes'])) {
                // Filtrar expedientes según puntos habilitados (solo para editores)
                if ($_SESSION['user_role'] === 'editor') {
                    $item['expedientes'] = array_filter($item['expedientes'], function($expediente) use ($expedientesHabilitados) {
                        return isset($expedientesHabilitados[$expediente['id']]);
                    });
                }
                
                foreach ($item['expedientes'] as &$expediente) {
                    $expediente['ya_voto'] = $puedeVotar ? $votacionModel->yaVoto($id, $_SESSION['user_id'], 'expediente', $expediente['id']) : false;
                    $expediente['voto_usuario'] = $puedeVotar ? $votacionModel->getVotoUsuario($id, $_SESSION['user_id'], 'expediente', $expediente['id']) : null;
                    $expediente['resultados'] = $votacionModel->getResultadosItem($id, 'expediente', $expediente['id']);
                }
            }
            
            // Mantener la lógica original para ítems que no sean expedientes
            $item['resultados'] = $votacionModel->getResultadosItem($id, 'expediente', $item['id']);
            $item['ya_voto'] = $puedeVotar ? $votacionModel->yaVoto($id, $_SESSION['user_id'], 'expediente', $item['id']) : false;
        }
        
        // Votar "Lectura de Actas" de forma global
        $actasItem = [
            'id' => 0,
            'titulo' => 'Lectura y Consideración de Actas',
            'tipo_item' => 'lectura_actas',
            'resultados' => $votacionModel->getResultadosItem($id, 'actas', 0),
            'ya_voto' => $puedeVotar ? $votacionModel->yaVoto($id, $_SESSION['user_id'], 'actas', 0) : false,
            'voto_usuario' => $puedeVotar ? $votacionModel->getVotoUsuario($id, $_SESSION['user_id'], 'actas', 0) : null
        ];
        
        // Asegurar token CSRF para acciones de la vista (votar, etc.)
        $this->generateCSRFToken();

        $data = [
            'sesion' => $sesion,
            'presentes' => $presentes,
            'estadisticas' => $estadisticas,
            'items' => $items,
            'actas_item' => $actasItem,
            'puede_votar' => $puedeVotar,
            'usuario_ya_presente' => $usuarioYaPresente,
            'es_admin' => $_SESSION['user_role'] === 'admin',
            'puntos_habilitados' => $puntosHabilitados,
            'actas_habilitada' => $actasHabilitada,
            'total_puntos' => count($votacionModel->getPuntosOrdenDia($id, false)),
            'puntos_habilitados_count' => count(array_filter($puntosHabilitados, function($p) { return $p['habilitado']; })),
            'page_title' => 'Sala de Votación - ' . ($sesion['nombre'] ?? 'Sesión')
        ];
        
        $this->loadView('votacion/sesion', $data);
    }
    
    // ============================================
    // GESTIÓN DE PRESENCIA
    // ============================================
    
    public function marcarPresencia($sesionId) {
        try {
            $this->requireLogin();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendJSON(['success' => false, 'message' => 'Método no permitido']);
                return;
            }
            
            $votacionModel = $this->loadModel('Votacion');
            
            // Solo editores pueden marcar presencia para votar
            if (!$votacionModel->puedeVotar($_SESSION['user_id'])) {
                $this->sendJSON(['success' => false, 'message' => 'Solo los editores pueden marcar presencia para votar']);
                return;
            }
            
            // Verificar si el usuario ya está presente
            if ($votacionModel->usuarioEstaPresente($sesionId, $_SESSION['user_id'])) {
                $this->sendJSON(['success' => false, 'message' => 'Ya has registrado tu presencia en esta sesión']);
                return;
            }
            
            $presente = isset($_POST['presente']) ? (bool)$_POST['presente'] : true;
            
            if ($presente) {
                $success = $votacionModel->registrarPresencia($sesionId, $_SESSION['user_id'], true);
            } else {
                $success = $votacionModel->marcarSalida($sesionId, $_SESSION['user_id']);
            }
            
            if ($success) {
                $presentes = $votacionModel->getPresentesSesion($sesionId);
                $this->sendJSON([
                    'success' => true, 
                    'message' => $presente ? 'Presencia registrada exitosamente' : 'Salida registrada',
                    'presentes' => $presentes,
                    'total_presentes' => count($presentes),
                    'ya_presente' => true // Indicar que ahora está presente
                ]);
            } else {
                $this->sendJSON(['success' => false, 'message' => 'Error al registrar presencia']);
            }
        } catch (Exception $e) {
            error_log("Error en marcarPresencia(): " . $e->getMessage());
            $this->sendJSON(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }
    
    // ============================================
    // VOTACIÓN
    // ============================================
    
    public function votar() {
        try {
            $this->requireLogin();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendJSON(['success' => false, 'message' => 'Método no permitido']);
                return;
            }
            
            $this->validateCSRF();
            
            $votacionModel = $this->loadModel('Votacion');
            
            // Verificar permisos
            if (!$votacionModel->puedeVotar($_SESSION['user_id'])) {
                $this->sendJSON(['success' => false, 'message' => 'Solo los editores pueden votar']);
                return;
            }
        } catch (Exception $e) {
            error_log("Error en votar() - Verificaciones iniciales: " . $e->getMessage());
            $this->sendJSON(['success' => false, 'message' => 'Error interno del servidor']);
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (empty($_POST['sesion_id'])) {
            $errors[] = 'ID de sesión requerido';
        }
        
        if (empty($_POST['tipo_voto']) || !in_array($_POST['tipo_voto'], ['positivo', 'negativo', 'abstencion'])) {
            $errors[] = 'Tipo de voto inválido';
        }
        
        if (empty($_POST['item_tipo']) || !in_array($_POST['item_tipo'], ['expediente', 'actas', 'item_general'])) {
            $errors[] = 'Tipo de ítem inválido';
        }
        
        if (!empty($errors)) {
            $this->sendJSON(['success' => false, 'message' => implode('. ', $errors)]);
        }
        
        // Verificar que la sesión esté activa
        $sesion = $votacionModel->getSesionById($_POST['sesion_id']);
        if (!$sesion || $sesion['estado'] !== 'activa') {
            $this->sendJSON(['success' => false, 'message' => 'La sesión no está activa']);
        }
        
        // Verificar presencia
        $presentes = $votacionModel->getPresentesSesion($_POST['sesion_id']);
        $estaPresente = false;
        foreach ($presentes as $presente) {
            if ($presente['user_id'] == $_SESSION['user_id']) {
                $estaPresente = true;
                break;
            }
        }
        
        if (!$estaPresente) {
            $this->sendJSON(['success' => false, 'message' => 'Debe marcar presencia antes de votar']);
        }

        // Verificar que el punto esté habilitado por el administrador (control progresivo)
        $itemTipo = $_POST['item_tipo'];
        // Para actas usamos item_id=0; para expediente viene explícito; para otros tipos exigimos también habilitado si existiera
        $itemId = isset($_POST['item_id']) ? (($_POST['item_id'] === '' || $_POST['item_id'] === null) ? null : $_POST['item_id']) : null;
        if ($itemTipo === 'actas') {
            $itemId = 0;
        }
        
        // Solo permitir votar si el punto correspondiente está habilitado
        if (!$votacionModel->isPuntoHabilitado($_POST['sesion_id'], $itemTipo, $itemId)) {
            $this->sendJSON(['success' => false, 'message' => 'Este punto aún no está habilitado para votación por el administrador']);
        }
        
        try {
            $data = [
                'sesion_id' => $_POST['sesion_id'],
                'user_id' => $_SESSION['user_id'],
                'item_votacion_tipo' => $_POST['item_tipo'],
                'item_votacion_id' => $_POST['item_id'] ?? null,
                'numero_expediente' => $_POST['numero_expediente'] ?? '',
                'extracto_expediente' => $_POST['extracto_expediente'] ?? '',
                'tipo_voto' => $_POST['tipo_voto'],
                'observaciones' => $_POST['observaciones'] ?? '',
                'ip_address' => $_SERVER['REMOTE_ADDR']
            ];
            
            $votoId = $votacionModel->registrarVoto($data);
            
            if ($votoId) {
                // Obtener resultados actualizados
                $resultados = $votacionModel->getResultadosItem(
                    $_POST['sesion_id'], 
                    $_POST['item_tipo'], 
                    $_POST['item_id'] ?? null
                );
                
                $this->sendJSON([
                    'success' => true, 
                    'message' => 'Voto registrado exitosamente',
                    'resultados' => $resultados,
                    'voto_id' => $votoId
                ]);
            } else {
                $this->sendJSON(['success' => false, 'message' => 'Ya ha votado este ítem o error al registrar voto']);
            }
        } catch (Exception $e) {
            error_log("Error en votar() - Registro de voto: " . $e->getMessage());
            error_log("Datos del POST: " . print_r($_POST, true));
            $this->sendJSON(['success' => false, 'message' => 'Error al procesar el voto. Intente nuevamente.']);
        }
    }
    
    // ============================================
    // CONTROL DE SESIÓN (ADMIN)
    // ============================================
    
    public function iniciarSesion($id) {
        $this->requireAdmin();
        
        $votacionModel = $this->loadModel('Votacion');
        
        if ($votacionModel->actualizarEstadoSesion($id, 'activa')) {
            $_SESSION['success_message'] = 'Sesión de votación iniciada';
        } else {
            $_SESSION['error_message'] = 'Error al iniciar la sesión';
        }
        
        header('Location: ' . BASE_URL . 'votacion/sesion/' . $id);
        exit;
    }
    
    public function pausarSesion($id) {
        $this->requireAdmin();
        
        $votacionModel = $this->loadModel('Votacion');
        
        if ($votacionModel->actualizarEstadoSesion($id, 'pausada')) {
            $_SESSION['success_message'] = 'Sesión pausada';
        } else {
            $_SESSION['error_message'] = 'Error al pausar la sesión';
        }
        
        header('Location: ' . BASE_URL . 'votacion/sesion/' . $id);
        exit;
    }
    
    public function finalizarSesion($id) {
        $this->requireAdmin();
        
        $votacionModel = $this->loadModel('Votacion');
        
        if ($votacionModel->actualizarEstadoSesion($id, 'finalizada')) {
            $_SESSION['success_message'] = 'Sesión finalizada';
        } else {
            $_SESSION['error_message'] = 'Error al finalizar la sesión';
        }
        
        header('Location: ' . BASE_URL . 'votacion');
        exit;
    }
    
    // ============================================
    // RESULTADOS Y ESTADÍSTICAS
    // ============================================
    
    public function resultados($sesionId, $tipoItem, $itemId) {
        $this->requireLogin();
        
        $votacionModel = $this->loadModel('Votacion');
        $sesion = $votacionModel->getSesionById($sesionId);
        
        if (!$sesion) {
            $_SESSION['error_message'] = 'Sesión no encontrada';
            header('Location: ' . BASE_URL . 'votacion');
            exit;
        }
        
        $resultados = $votacionModel->getResultadosItem($sesionId, $tipoItem, $itemId);
        $votosDetallados = $votacionModel->getVotosDetallados($sesionId, $tipoItem, $itemId);
        $totalPresentes = $votacionModel->getTotalPresentes($sesionId);
        
        $data = [
            'sesion' => $sesion,
            'resultados' => $resultados,
            'votos_detallados' => $votosDetallados,
            'total_presentes' => $totalPresentes,
            'tipo_item' => $tipoItem,
            'item_id' => $itemId,
            'page_title' => 'Resultados de Votación'
        ];
        
        $this->loadView('votacion/resultados', $data);
    }
    
    public function historial() {
        $this->requireLogin();
        
        $votacionModel = $this->loadModel('Votacion');
        
        // Filtros
        $filtros = [];
        if (!empty($_GET['sesion_id'])) {
            $filtros['sesion_id'] = $_GET['sesion_id'];
        }
        if (!empty($_GET['resultado'])) {
            $filtros['resultado'] = $_GET['resultado'];
        }
        if (!empty($_GET['fecha_desde'])) {
            $filtros['fecha_desde'] = $_GET['fecha_desde'];
        }
        if (!empty($_GET['fecha_hasta'])) {
            $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
        }
        
        $historial = $votacionModel->getHistorialCompleto($filtros);
        $sesiones = $votacionModel->getSesionesActivas(); // Para filtro
        
        $data = [
            'historial' => $historial,
            'sesiones' => $sesiones,
            'filtros' => $filtros,
            'page_title' => 'Historial de Votaciones'
        ];
        
        $this->loadView('votacion/historial', $data);
    }
    
    // ============================================
    // API ENDPOINTS
    // ============================================
    
    public function api($action) {
        $this->requireLogin();
        
        switch ($action) {
            case 'resultados':
                if (isset($_GET['sesion']) && isset($_GET['tipo']) && isset($_GET['item'])) {
                    $votacionModel = $this->loadModel('Votacion');
                    $resultados = $votacionModel->getResultadosItem($_GET['sesion'], $_GET['tipo'], $_GET['item']);
                    $this->sendJSON(['success' => true, 'resultados' => $resultados]);
                } else {
                    $this->sendJSON(['success' => false, 'message' => 'Parámetros requeridos']);
                }
                break;
                
            case 'presentes':
                if (isset($_GET['sesion'])) {
                    $votacionModel = $this->loadModel('Votacion');
                    $presentes = $votacionModel->getPresentesSesion($_GET['sesion']);
                    $this->sendJSON(['success' => true, 'presentes' => $presentes]);
                } else {
                    $this->sendJSON(['success' => false, 'message' => 'ID de sesión requerido']);
                }
                break;
                
            case 'estadisticas':
                if (isset($_GET['sesion'])) {
                    $votacionModel = $this->loadModel('Votacion');
                    $estadisticas = $votacionModel->getEstadisticasSesion($_GET['sesion']);
                    $this->sendJSON(['success' => true, 'estadisticas' => $estadisticas]);
                } else {
                    $this->sendJSON(['success' => false, 'message' => 'ID de sesión requerido']);
                }
                break;
                
            default:
                $this->sendJSON(['success' => false, 'message' => 'Acción no válida']);
        }
    }
    
    // ============================================
    // ELIMINACIÓN DE SESIONES
    // ============================================
    
    public function eliminarSesion($id) {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/votacion');
            return;
        }
        
        $votacionModel = $this->loadModel('Votacion');
        $resultado = $votacionModel->eliminarSesion($id, $_SESSION['user_id']);
        
        if ($resultado['success']) {
            $_SESSION['flash_success'] = $resultado['message'];
        } else {
            $_SESSION['flash_error'] = $resultado['message'];
        }
        
        $this->redirect('/votacion');
    }

    // ============================================
    // CONTROL PROGRESIVO DE PUNTOS
    // ============================================
    
    public function panelControl($sesionId = null) {
        $this->requireAdmin();
        
        // Debug: Registrar acceso al panel
        error_log("Panel Control: Accediendo con sesionId = " . ($sesionId ?? 'null'));
        
        if (!$sesionId) {
            error_log("Panel Control: No se proporcionó sesionId, redirigiendo");
            $this->redirect('/votacion');
            return;
        }
        
        $votacionModel = $this->loadModel('Votacion');
        
        // Obtener información de la sesión
        $sesion = $votacionModel->getSesionById($sesionId);
        if (!$sesion) {
            error_log("Panel Control: Sesión $sesionId no encontrada");
            $_SESSION['flash_error'] = 'Sesión no encontrada';
            $this->redirect('/votacion');
            return;
        }
        
        error_log("Panel Control: Sesión encontrada - " . $sesion['nombre_sesion']);
        
        // Obtener puntos del orden del día
        try {
            $puntos = $votacionModel->getPuntosOrdenDia($sesionId);
            error_log("Panel Control: Puntos obtenidos - " . count($puntos) . " puntos");
        } catch (Exception $e) {
            error_log("Panel Control: Error al obtener puntos - " . $e->getMessage());
            $puntos = [];
        }
        
        try {
            $estadisticas = $votacionModel->contarPuntosHabilitados($sesionId);
            error_log("Panel Control: Estadísticas obtenidas");
        } catch (Exception $e) {
            error_log("Panel Control: Error al obtener estadísticas - " . $e->getMessage());
            $estadisticas = ['total_puntos' => 0, 'puntos_habilitados' => 0, 'puntos_pendientes' => 0];
        }
        
        // Generar CSRF token si no existe
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $data = [
            'sesion' => $sesion,
            'puntos' => $puntos,
            'estadisticas' => $estadisticas,
            'page_title' => 'Control Progresivo - ' . $sesion['nombre_sesion']
        ];
        
        error_log("Panel Control: Cargando vista con datos preparados");
        $this->loadView('votacion/panel_control', $data);
    }
    
    public function habilitarPunto() {
        // Debug: Registrar que se está ejecutando el método
        error_log("DEBUG: habilitarPunto method called");
        error_log("DEBUG: POST data: " . json_encode($_POST));
        
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("DEBUG: Not POST method");
            $this->sendJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $this->validateCSRF();
        
        $sesionId = $_POST['sesion_id'] ?? null;
        $puntoId = $_POST['punto_id'] ?? null;
        
        if (!$sesionId || !$puntoId) {
            $this->sendJSON(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        $votacionModel = $this->loadModel('Votacion');
        
        // Verificar que la sesión existe y está activa
        $sesion = $votacionModel->getSesionById($sesionId);
        if (!$sesion || !in_array($sesion['estado'], ['activa', 'pausada'])) {
            $this->sendJSON(['success' => false, 'message' => 'La sesión debe estar activa para habilitar puntos']);
            return;
        }
        
        $success = $votacionModel->habilitarPunto($sesionId, $puntoId, $_SESSION['user_id']);
        
        if ($success) {
            // Obtener información del punto habilitado
            $puntos = $votacionModel->getPuntosOrdenDia($sesionId);
            $puntoHabilitado = array_filter($puntos, function($p) use ($puntoId) {
                return $p['id'] == $puntoId;
            });
            $puntoHabilitado = reset($puntoHabilitado);
            
            $this->sendJSON([
                'success' => true, 
                'message' => 'Punto habilitado exitosamente',
                'punto' => $puntoHabilitado
            ]);
        } else {
            $this->sendJSON(['success' => false, 'message' => 'Error al habilitar el punto']);
        }
    }
    
    public function deshabilitarPunto() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $this->validateCSRF();
        
        $sesionId = $_POST['sesion_id'] ?? null;
        $puntoId = $_POST['punto_id'] ?? null;
        
        if (!$sesionId || !$puntoId) {
            $this->sendJSON(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        $votacionModel = $this->loadModel('Votacion');
        
        $success = $votacionModel->deshabilitarPunto($sesionId, $puntoId);
        
        if ($success) {
            $this->sendJSON(['success' => true, 'message' => 'Punto deshabilitado exitosamente']);
        } else {
            $this->sendJSON(['success' => false, 'message' => 'Error al deshabilitar el punto']);
        }
    }
    
    public function obtenerEstadoPuntos($sesionId = null) {
        $this->requireLogin();
        
        if (!$sesionId) {
            $this->sendJSON(['success' => false, 'message' => 'ID de sesión requerido']);
            return;
        }
        
        $votacionModel = $this->loadModel('Votacion');
        
        // Los editores solo ven puntos habilitados, los admins ven todos
        $soloHabilitados = ($_SESSION['user_role'] !== 'admin');
        $puntos = $votacionModel->getPuntosOrdenDia($sesionId, $soloHabilitados);
        $estadisticas = $votacionModel->contarPuntosHabilitados($sesionId);
        
        $this->sendJSON([
            'success' => true,
            'puntos' => $puntos,
            'estadisticas' => $estadisticas
        ]);
    }
    
    public function inicializarPuntos($sesionId = null) {
        $this->requireAdmin();
        
        if (!$sesionId) {
            $_SESSION['flash_error'] = 'ID de sesión requerido';
            $this->redirect('/votacion');
            return;
        }
        
        $votacionModel = $this->loadModel('Votacion');
        
        $success = $votacionModel->inicializarPuntosSesion($sesionId);
        
        if ($success) {
            $_SESSION['flash_success'] = 'Puntos del orden del día inicializados correctamente';
        } else {
            $_SESSION['flash_error'] = 'Error al inicializar los puntos del orden del día';
        }
        
        $this->redirect('/votacion/panel-control/' . $sesionId);
    }

    // ============================================
    // UTILIDADES
    // ============================================
    
    private function sendJSON($data) {
        // Versión optimizada y compatible para servidor compartido
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
        }
        
        $json = json_encode($data);
        if ($json === false) {
            $json = json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        
        echo $json;
        exit;
    }
    
    private function validateCSRF() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            $this->sendJSON(['success' => false, 'message' => 'Token CSRF requerido']);
        }
        
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->sendJSON(['success' => false, 'message' => 'Token CSRF inválido']);
        }
    }
    
    // ============================================
    // VENTANA PÚBLICA DE CONSULTA
    // ============================================
    
    public function vistaPublica($sesionId = null) {
        // Esta función NO requiere autenticación - es pública
        
        if (!$sesionId) {
            // Si no se especifica sesión, mostrar la sesión activa más reciente
            $votacionModel = $this->loadModel('Votacion');
            $sesionActiva = $votacionModel->getSesionActivaActual();
            if ($sesionActiva) {
                $sesionId = $sesionActiva['id'];
            } else {
                $this->loadView('votacion/publica_sin_sesion');
                return;
            }
        }
        
        $votacionModel = $this->loadModel('Votacion');
        
        // Obtener información de la sesión
        $sesion = $votacionModel->getSesionById($sesionId);
        if (!$sesion) {
            $this->loadView('votacion/publica_sin_sesion');
            return;
        }
        
        // Obtener miembros presentes
        $miembrosPresentes = $votacionModel->getMiembrosPresentes($sesionId);
        
        // Obtener estado de puntos (solo los habilitados)
        $puntosHabilitados = $votacionModel->getPuntosHabilitados($sesionId);
        // Enriquecer con resultado final si aplica
        foreach ($puntosHabilitados as &$ph) {
            if (!empty($ph['item_tipo']) && $ph['item_tipo'] === 'expediente' && !empty($ph['item_id'])) {
                $hist = $votacionModel->getHistorialResultadoExpediente($sesionId, $ph['item_id']);
                if ($hist && !empty($hist['resultado']) && $hist['resultado'] !== 'pendiente') {
                    $ph['resultado_final'] = $hist['resultado'];
                } else {
                    $ph['resultado_final'] = null;
                }
            } else {
                $ph['resultado_final'] = null;
            }
        }
        
        // Obtener "votación actual": usamos el último punto habilitado como referencia
        $votacionActual = null;
        $estadoVotacion = null;
        if (!empty($puntosHabilitados)) {
            // Tomamos el último como el actual en votación
            $votacionActual = end($puntosHabilitados);
            $estadoVotacion = [ 'created_at' => $votacionActual['fecha_habilitacion'] ?? null ];
            // Enriquecer con resultados si es expediente
            if (!empty($votacionActual['item_tipo']) && $votacionActual['item_tipo'] === 'expediente' && !empty($votacionActual['item_id'])) {
                $votacionActual['resultados'] = $votacionModel->getResultadosExpedienteCounts($sesionId, $votacionActual['item_id']);
                $votacionActual['historial'] = $votacionModel->getHistorialResultadoExpediente($sesionId, $votacionActual['item_id']);
            }
        }
        
        // Obtener estadísticas generales
        $estadisticas = [
            'total_miembros' => count($miembrosPresentes),
            'puntos_habilitados' => count($puntosHabilitados),
            'sesion_activa' => ($sesion['estado'] === 'activa')
        ];
        
        $data = [
            'sesion' => $sesion,
            'miembros_presentes' => $miembrosPresentes,
            'puntos_habilitados' => $puntosHabilitados,
            'votacion_actual' => $votacionActual,
            'estado_votacion' => $estadoVotacion,
            'estadisticas' => $estadisticas
        ];
        
        $this->loadView('votacion/vista_publica', $data);
    }
    
    public function estadoPublicoJson($sesionId = null) {
        // API pública para obtener estado actual en JSON (para actualizaciones AJAX)
        
        if (!$sesionId) {
            $votacionModel = $this->loadModel('Votacion');
            $sesionActiva = $votacionModel->getSesionActivaActual();
            if ($sesionActiva) {
                $sesionId = $sesionActiva['id'];
            } else {
                $this->sendJSON(['error' => 'No hay sesión activa']);
                return;
            }
        }
        
        $votacionModel = $this->loadModel('Votacion');
        
        // Obtener información básica
        $sesion = $votacionModel->getSesionById($sesionId);
        $miembrosPresentes = $votacionModel->getMiembrosPresentes($sesionId);
        $puntosHabilitados = $votacionModel->getPuntosHabilitados($sesionId);
        foreach ($puntosHabilitados as &$ph) {
            if (!empty($ph['item_tipo']) && $ph['item_tipo'] === 'expediente' && !empty($ph['item_id'])) {
                $hist = $votacionModel->getHistorialResultadoExpediente($sesionId, $ph['item_id']);
                if ($hist && !empty($hist['resultado']) && $hist['resultado'] !== 'pendiente') {
                    $ph['resultado_final'] = $hist['resultado'];
                } else {
                    $ph['resultado_final'] = null;
                }
            } else {
                $ph['resultado_final'] = null;
            }
        }
        // Marcar cuál es el actual para estilado en vista
        $puntoActualId = null;
        if (!empty($puntosHabilitados)) {
            $puntoActualId = end($puntosHabilitados)['punto_id'] ?? null;
        }
        
        // Buscar "votación actual": último punto habilitado
        $votacionActual = null;
        if (!empty($puntosHabilitados)) {
            $ultimo = end($puntosHabilitados);
            $votacionActual = [
                'punto' => $ultimo,
                'votacion' => ['created_at' => $ultimo['fecha_habilitacion']]
            ];
            // Si es un expediente, incluir conteos de votos en tiempo real
            if (!empty($ultimo['item_tipo']) && $ultimo['item_tipo'] === 'expediente' && !empty($ultimo['item_id'])) {
                $votacionActual['resultados'] = $votacionModel->getResultadosExpedienteCounts($sesionId, $ultimo['item_id']);
            }
        }
        
        $this->sendJSON([
            'sesion' => $sesion,
            'miembros_presentes' => $miembrosPresentes,
            'puntos_habilitados' => $puntosHabilitados,
            'punto_actual_id' => $puntoActualId,
            'votacion_actual' => $votacionActual,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    // ============================================
    // SISTEMA DE MOCIONES
    // ============================================
    
    public function enviarMocion() {
        $this->requireLogin();
        
        try {
            // Solo editores y admins pueden enviar mociones
            if ($_SESSION['user_role'] !== 'editor' && $_SESSION['user_role'] !== 'admin') {
                $this->sendJSON(['success' => false, 'error' => 'Permisos insuficientes']);
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $sesionId = $input['sesion_id'] ?? null;
            $tipo = $input['tipo'] ?? '';
            $texto = trim($input['texto'] ?? '');
            
            if (!$sesionId || !$tipo || !$texto) {
                $this->sendJSON(['success' => false, 'error' => 'Datos incompletos']);
                return;
            }
            
            $votacionModel = $this->loadModel('Votacion');
            
            // Crear la moción
            $mocionId = $votacionModel->crearMocion([
                'sesion_id' => $sesionId,
                'usuario_id' => $_SESSION['user_id'],
                'tipo' => $tipo,
                'texto' => $texto,
                'autor_nombre' => $_SESSION['user_name']
            ]);
            
            if ($mocionId) {
                // Obtener la moción creada
                $mocion = $votacionModel->getMocionById($mocionId);
                
                $this->sendJSON([
                    'success' => true, 
                    'message' => 'Moción enviada exitosamente',
                    'mocion' => $mocion
                ]);
            } else {
                $this->sendJSON(['success' => false, 'error' => 'Error al crear la moción']);
            }
            
        } catch (Exception $e) {
            error_log("Error en enviarMocion: " . $e->getMessage());
            $this->sendJSON(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }
    
    public function verificarMociones($sesionId, $desde = 0) {
        try {
            $votacionModel = $this->loadModel('Votacion');
            
            $mociones = $votacionModel->getMocionesRecientes($sesionId, $desde);
            
            $this->sendJSON([
                'success' => true,
                'mociones' => $mociones,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            error_log("Error en verificarMociones: " . $e->getMessage());
            $this->sendJSON(['success' => false, 'error' => 'Error al verificar mociones']);
        }
    }
    
    public function obtenerMociones($sesionId) {
        try {
            $votacionModel = $this->loadModel('Votacion');
            
            // Obtener todas las mociones de la sesión (no solo las recientes)
            $mociones = $votacionModel->getTodasLasMociones($sesionId);
            
            $this->sendJSON([
                'success' => true,
                'mociones' => $mociones,
                'total' => count($mociones),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            error_log("Error en obtenerMociones: " . $e->getMessage());
            $this->sendJSON(['success' => false, 'error' => 'Error al obtener mociones']);
        }
    }
    
    public function pantallaGrande($sesionId = null) {
        // Vista pública optimizada para pantallas grandes (65+ pulgadas)
        // Accesible sin autenticación
        
        try {
            if (!$sesionId) {
                // Si no se especifica sesión, mostrar la sesión activa más reciente
                $votacionModel = $this->loadModel('Votacion');
                $sesionActiva = $votacionModel->getSesionActivaActual();
                if ($sesionActiva) {
                    $sesionId = $sesionActiva['id'];
                } else {
                    // Usar datos por defecto si no hay sesión activa
                    $sesionId = 0;
                }
            }
            
            $votacionModel = $this->loadModel('Votacion');
            
            // Obtener información de la sesión
            if ($sesionId > 0) {
                $sesion = $votacionModel->getSesionById($sesionId);
                if (!$sesion) {
                    $sesion = [
                        'id' => 0,
                        'nombre' => 'Sesión no encontrada',
                        'numero_acta' => '-',
                        'fecha_sesion' => date('Y-m-d'),
                        'estado' => 'inactiva'
                    ];
                }
            } else {
                $sesion = [
                    'id' => 0,
                    'nombre' => 'No hay sesión activa',
                    'numero_acta' => '-',
                    'fecha_sesion' => date('Y-m-d'),
                    'estado' => 'inactiva'
                ];
            }
            
            // Obtener datos de la sesión
            $miembrosPresentes = [];
            $puntosHabilitados = [];
            
            if ($sesion['id'] > 0) {
                $miembrosPresentes = $votacionModel->getMiembrosPresentes($sesion['id']);
                $puntosHabilitados = $votacionModel->getPuntosHabilitados($sesion['id']);
                
                // Enriquecer puntos con resultados
                foreach ($puntosHabilitados as &$punto) {
                    if (!empty($punto['item_tipo']) && $punto['item_tipo'] === 'expediente' && !empty($punto['item_id'])) {
                        $resultados = $votacionModel->getResultadosItem($sesion['id'], 'expediente', $punto['item_id']);
                        $punto['resultados'] = [
                            'positivo' => 0,
                            'negativo' => 0,
                            'abstencion' => 0
                        ];
                        
                        foreach ($resultados as $resultado) {
                            $punto['resultados'][$resultado['tipo_voto']] = (int)$resultado['cantidad'];
                        }
                    }
                }
            }
            
            $data = [
                'sesion' => $sesion,
                'puntos_habilitados' => $puntosHabilitados,
                'miembros_presentes' => $miembrosPresentes,
                'page_title' => 'Pantalla Grande - ' . ($sesion['nombre'] ?? 'Sesión de Votación')
            ];
            
            $this->loadView('votacion/pantalla_grande', $data);
            
        } catch (Exception $e) {
            error_log("Error en pantallaGrande: " . $e->getMessage());
            
            // En caso de error, mostrar vista con datos por defecto
            $data = [
                'sesion' => [
                    'id' => 0,
                    'nombre' => 'Error de conexión',
                    'numero_acta' => '-',
                    'fecha_sesion' => date('Y-m-d'),
                    'estado' => 'error'
                ],
                'puntos_habilitados' => [],
                'miembros_presentes' => [],
                'page_title' => 'Pantalla Grande - Error'
            ];
            
            $this->loadView('votacion/pantalla_grande', $data);
        }
    }
    
    public function pararMocion() {
        $this->requireLogin();
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $mocionId = $input['mocion_id'] ?? null;
            $sesionId = $input['sesion_id'] ?? null;
            
            if (!$mocionId || !$sesionId) {
                $this->sendJSON(['success' => false, 'error' => 'Datos incompletos']);
                return;
            }
            
            $votacionModel = $this->loadModel('Votacion');
            
            // Verificar que la moción existe y pertenece a la sesión
            $mocion = $votacionModel->getMocionById($mocionId);
            if (!$mocion || $mocion['sesion_id'] != $sesionId) {
                $this->sendJSON(['success' => false, 'error' => 'Moción no encontrada']);
                return;
            }
            
            // Desactivar la moción
            $resultado = $votacionModel->desactivarMocion($mocionId);
            
            if ($resultado) {
                // Log de la acción
                error_log("Moción #{$mocionId} desactivada por usuario {$_SESSION['user_name']} (ID: {$_SESSION['user_id']})");
                
                $this->sendJSON([
                    'success' => true, 
                    'message' => 'Moción desactivada exitosamente',
                    'mocion_id' => $mocionId
                ]);
            } else {
                $this->sendJSON(['success' => false, 'error' => 'Error al desactivar la moción']);
            }
            
        } catch (Exception $e) {
            error_log("Error en pararMocion: " . $e->getMessage());
            $this->sendJSON(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    // ============================================
    // ADMINISTRACIÓN DE MOCIONES (SOLO ADMIN)
    // ============================================

    public function adminMociones() {
        $this->requireAdmin();
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendJSON(['success' => false, 'error' => 'Método no permitido']);
                return;
            }

            // Validar token CSRF (termina ejecución si falla)
            $this->validateCSRF();

            $sesionId = $_POST['sesion_id'] ?? '';
            
            if (empty($sesionId)) {
                $this->sendJSON(['success' => false, 'error' => 'Sesión ID requerido']);
                return;
            }

            // Log para debug
            error_log("=== AdminMociones INICIANDO ===");
            error_log("Sesión ID recibido: '{$sesionId}' (tipo: " . gettype($sesionId) . ")");

            $votacionModel = $this->loadModel('Votacion');
            
            // Obtener mociones activas
            error_log("Buscando mociones ACTIVAS para sesión {$sesionId}...");
            $mocionesActivas = $votacionModel->getMocionesPorSesion($sesionId, true);
            error_log("Mociones ACTIVAS encontradas: " . count($mocionesActivas));
            
            // Obtener historial de mociones (últimas 50 inactivas)
            error_log("Buscando HISTORIAL de mociones para sesión {$sesionId}...");
            $historialMociones = $votacionModel->getHistorialMociones($sesionId, 50);
            error_log("Mociones en HISTORIAL encontradas: " . count($historialMociones));
            
            // Debug adicional: verificar si hay mociones en general
            $todasLasMociones = $votacionModel->getTodasLasMociones($sesionId);
            error_log("TODAS las mociones (activas + inactivas) para sesión {$sesionId}: " . count($todasLasMociones));
            
            $this->sendJSON([
                'success' => true,
                'mociones_activas' => $mocionesActivas,
                'historial_mociones' => $historialMociones,
                'debug_info' => [
                    'sesion_id' => $sesionId,
                    'activas_count' => count($mocionesActivas),
                    'historial_count' => count($historialMociones)
                ]
            ]);

        } catch (Exception $e) {
            error_log("Error en adminMociones: " . $e->getMessage());
            $this->sendJSON(['success' => false, 'error' => 'Error interno del servidor', 'debug' => $e->getMessage()]);
        }
    }

    public function adminPararTodasMociones() {
        $this->requireAdmin();
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendJSON(['success' => false, 'error' => 'Método no permitido']);
                return;
            }

            // Validar token CSRF (termina ejecución si falla)
            $this->validateCSRF();

            $sesionId = $_POST['sesion_id'] ?? '';
            
            if (empty($sesionId)) {
                $this->sendJSON(['success' => false, 'error' => 'Sesión ID requerido']);
                return;
            }

            $votacionModel = $this->loadModel('Votacion');
            
            // Parar todas las mociones activas de la sesión
            $mocionesParadas = $votacionModel->pararTodasMocionesSesion($sesionId);
            
            // Log de la acción administrativa
            error_log("Admin {$_SESSION['user_name']} (ID: {$_SESSION['user_id']}) paró {$mocionesParadas} mociones en sesión {$sesionId}");
            
            $this->sendJSON([
                'success' => true,
                'message' => 'Todas las mociones han sido detenidas',
                'mociones_paradas' => $mocionesParadas
            ]);

        } catch (Exception $e) {
            error_log("Error en adminPararTodasMociones: " . $e->getMessage());
            $this->sendJSON(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    public function reactivarMocion() {
        $this->requireAdmin();
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendJSON(['success' => false, 'error' => 'Método no permitido']);
                return;
            }

            // Validar token CSRF (termina ejecución si falla)
            $this->validateCSRF();

            $mocionId = $_POST['mocion_id'] ?? '';
            
            if (empty($mocionId)) {
                $this->sendJSON(['success' => false, 'error' => 'ID de moción requerido']);
                return;
            }

            $votacionModel = $this->loadModel('Votacion');
            
            // Verificar que la moción existe
            $mocion = $votacionModel->obtenerMocionPorId($mocionId);
            if (!$mocion) {
                $this->sendJSON(['success' => false, 'error' => 'Moción no encontrada']);
                return;
            }
            
            // Reactivar la moción
            $resultado = $votacionModel->reactivarMocion($mocionId);
            
            if ($resultado) {
                // Log de la acción administrativa
                error_log("Admin {$_SESSION['user_name']} (ID: {$_SESSION['user_id']}) reactivó moción #{$mocionId}");
                
                $this->sendJSON([
                    'success' => true, 
                    'message' => 'Moción reactivada exitosamente',
                    'mocion_id' => $mocionId
                ]);
            } else {
                $this->sendJSON(['success' => false, 'error' => 'Error al reactivar la moción']);
            }

        } catch (Exception $e) {
            error_log("Error en reactivarMocion: " . $e->getMessage());
            $this->sendJSON(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    public function limpiarHistorialMociones() {
        $this->requireAdmin();
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendJSON(['success' => false, 'error' => 'Método no permitido']);
                return;
            }

            // Validar token CSRF (termina ejecución si falla)
            $this->validateCSRF();

            $sesionId = $_POST['sesion_id'] ?? '';
            
            if (empty($sesionId)) {
                $this->sendJSON(['success' => false, 'error' => 'Sesión ID requerido']);
                return;
            }

            $votacionModel = $this->loadModel('Votacion');
            
            // Limpiar historial de mociones (solo las inactivas)
            $mocionesEliminadas = $votacionModel->limpiarHistorialMociones($sesionId);
            
            // Log de la acción administrativa
            error_log("Admin {$_SESSION['user_name']} (ID: {$_SESSION['user_id']}) limpió {$mocionesEliminadas} mociones del historial en sesión {$sesionId}");
            
            $this->sendJSON([
                'success' => true,
                'message' => 'Historial de mociones limpiado',
                'mociones_eliminadas' => $mocionesEliminadas
            ]);

        } catch (Exception $e) {
            error_log("Error en limpiarHistorialMociones: " . $e->getMessage());
            $this->sendJSON(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    // MÉTODO TEMPORAL PARA DEBUG - ELIMINAR EN PRODUCCIÓN
    public function debugMociones() {
        $this->requireAdmin();
        
        try {
            $votacionModel = $this->loadModel('Votacion');
            
            // Debug completo de la tabla mociones
            $debugResult = $votacionModel->debugTablaMociones();
            
            $this->sendJSON([
                'success' => true,
                'debug_completo' => $debugResult
            ]);

        } catch (Exception $e) {
            $this->sendJSON([
                'success' => false, 
                'error' => 'Error: ' . $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
        }
    }

    // MÉTODO TEMPORAL PARA REACTIVAR MOCIONES - USAR UNA VEZ
    public function reactivarTodasMociones() {
        $this->requireAdmin();
        
        try {
            $votacionModel = $this->loadModel('Votacion');
            
            // Usar el método del modelo en lugar de acceso directo a db
            $mocionesReactivadas = $votacionModel->reactivarTodasMocionesSesion(14);
            
            $this->sendJSON([
                'success' => true,
                'mensaje' => "Se reactivaron {$mocionesReactivadas} mociones",
                'mociones_reactivadas' => $mocionesReactivadas
            ]);

        } catch (Exception $e) {
            error_log("Error en reactivarTodasMociones: " . $e->getMessage());
            $this->sendJSON([
                'success' => false, 
                'error' => 'Error: ' . $e->getMessage()
            ]);
        }
    }


}
?>