<?php
// Script para reparar tablas faltantes en VotoCDE
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h1>Reparación de Tablas VotoCDE</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<p><strong>Ejecutando migración...</strong></p>";
    
    // Leer el archivo SQL
    $sql = file_get_contents(__DIR__ . '/database/fix_missing_tables.sql');
    
    // Dividir por declaraciones (separadas por ;)
    $statements = array_filter(explode(';', $sql), function($stmt) {
        return trim($stmt) !== '' && !preg_match('/^\s*--/', trim($stmt));
    });
    
    echo "<ul>";
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        try {
            $db->exec($statement);
            
            // Extraer nombre de tabla del CREATE TABLE
            if (preg_match('/CREATE TABLE IF NOT EXISTS\s+(\w+)/i', $statement, $matches)) {
                echo "<li>✅ Tabla <strong>{$matches[1]}</strong> verificada/creada</li>";
            }
        } catch (PDOException $e) {
            echo "<li>❌ Error: " . htmlspecialchars($e->getMessage()) . "</li>";
        }
    }
    
    echo "</ul>";
    
    // Verificar las tablas
    echo "<h2>Verificación de Tablas</h2>";
    echo "<ul>";
    
    $tables = ['puntos_habilitados', 'presentes_sesion', 'historial_votacion'];
    foreach ($tables as $table) {
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM {$table}");
            $count = $stmt->fetchColumn();
            echo "<li>✅ Tabla <strong>{$table}</strong> - {$count} registros</li>";
        } catch (PDOException $e) {
            echo "<li>❌ Tabla <strong>{$table}</strong> - Error: " . htmlspecialchars($e->getMessage()) . "</li>";
        }
    }
    
    echo "</ul>";
    
    echo "<p><strong>Migración completada.</strong></p>";
    echo "<p><a href='/'>Ir al Sistema Principal</a> | <a href='navegacion_test.php'>Ir a Pruebas</a></p>";
    
} catch (Exception $e) {
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>