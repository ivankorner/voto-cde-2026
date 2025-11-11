<?php
/**
 * Endpoint Directo para Votación
 * Este archivo maneja las peticiones de voto sin depender del router
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';

// Iniciar sesión
session_start();

// Registrar toda la petición para debug
error_log("VOTAR_DIRECTO: METHOD=" . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN') . 
          " | URI=" . ($_SERVER['REQUEST_URI'] ?? 'UNKNOWN') . 
          " | POST_DATA=" . json_encode($_POST) . 
          " | SESSION_ID=" . ($_SESSION['user_id'] ?? 'NO_SESSION'));

// Aceptar POST (método correcto)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    
    // Si es GET, dar información de debug
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode([
            'success' => false, 
            'message' => 'Este endpoint requiere POST, pero recibió GET',
            'debug' => [
                'method' => $_SERVER['REQUEST_METHOD'],
                'uri' => $_SERVER['REQUEST_URI'] ?? '',
                'server' => $_SERVER['HTTP_HOST'] ?? '',
                'help' => 'Asegúrate de que el JavaScript esté enviando POST correctamente'
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Método no permitido: ' . $_SERVER['REQUEST_METHOD']]);
    }
    exit;
}

// Cargar controlador de votación
require_once __DIR__ . '/app/controllers/VotacionController.php';

try {
    $controller = new VotacionController();
    $controller->votar();
} catch (Exception $e) {
    header('Content-Type: application/json');
    error_log("Error en votar_directo.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false, 
        'message' => 'Error al procesar el voto: ' . $e->getMessage()
    ]);
}
?>