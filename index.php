<?php
// Index principal con manejo de errores para servidor compartido

// Headers para permitir POST en servidores compartidos
header('Accept: application/x-www-form-urlencoded, multipart/form-data');

try {
    require_once 'config/config.php';
    require_once 'config/database.php';
    require_once 'core/Router.php';
    require_once 'core/Controller.php';
    require_once 'core/Model.php';

    session_start();

    // Generar token CSRF si no existe
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $router = new Router();
    $router->run();

} catch (Exception $e) {
    // En caso de error, mostrar mensaje amigable
    echo "<h1>Error del Sistema</h1>";
    echo "<p>Ha ocurrido un error interno. Por favor, contacte al administrador.</p>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='index_diagnostico.php'>Ir al diagnóstico</a></p>";
}
?>