<?php
// Configuración para servidor compartido/cloud
// Cambiar BASE_URL por la URL real de tu sitio web

// URL base del sitio (cambiar por tu dominio real)
define('BASE_URL', 'https://votocde.online/'); // Cambiar por tu URL real

define('APP_NAME', 'CDE Decide');

// Configuración de la base de datos - Servidor compartido
define('DB_HOST', 'localhost');
define('DB_NAME', 'a0020819_votocde');
define('DB_USER', 'a0020819_votocde');
define('DB_PASS', 'revu06weRI');

// Rutas del sistema
define('CONTROLLERS_PATH', 'app/controllers/');
define('MODELS_PATH', 'app/models/');
define('VIEWS_PATH', 'app/views/');

// Configuración de sesión para servidor compartido (comentado para evitar errores de headers)
// ini_set('session.cookie_httponly', 1);
// ini_set('session.use_only_cookies', 1);
// ini_set('session.cookie_secure', 1); // HTTPS habilitado en servidor compartido
// ini_set('session.gc_maxlifetime', 3600); // 1 hora de vida de sesión

// Configuración de zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configuración de errores para producción
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Configuración para servidor compartido
ini_set('max_execution_time', 30);
ini_set('memory_limit', '128M');

// Incluir helpers
require_once 'helpers/UrlHelper.php';
?>