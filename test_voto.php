<?php
/**
 * Endpoint de Debug para Votación
 * Acceder vía: POST https://votocde.online/test_voto.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Log de toda la petición
$debug = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'UNKNOWN',
    'query_string' => $_SERVER['QUERY_STRING'] ?? '',
    'http_host' => $_SERVER['HTTP_HOST'] ?? 'UNKNOWN',
    'post_data' => $_POST,
    'get_data' => $_GET,
    'headers' => getallheaders(),
    'session_exists' => isset($_SESSION),
];

// Guardar en archivo
file_put_contents('test_voto_debug.log', json_encode($debug, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

// Responder
echo json_encode([
    'success' => true,
    'message' => 'Test endpoint funcionando',
    'debug' => $debug
], JSON_PRETTY_PRINT);
?>