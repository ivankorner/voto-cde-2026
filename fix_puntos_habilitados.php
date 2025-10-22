<?php
// Script para agregar columnas faltantes a puntos_habilitados
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h1>Reparación Final - Tabla puntos_habilitados</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<p><strong>Verificando estructura de puntos_habilitados...</strong></p>";
    
    // Verificar estructura actual
    $stmt = $db->query("DESCRIBE puntos_habilitados");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Columnas actuales:</strong></p>";
    echo "<ul>";
    $hasNumeroExpediente = false;
    $hasExtracto = false;
    
    foreach ($columns as $col) {
        echo "<li>{$col['Field']} - {$col['Type']}</li>";
        if ($col['Field'] === 'numero_expediente') $hasNumeroExpediente = true;
        if ($col['Field'] === 'extracto') $hasExtracto = true;
    }
    echo "</ul>";
    
    // Agregar columnas si faltan
    $changes = [];
    
    if (!$hasNumeroExpediente) {
        try {
            $db->exec("ALTER TABLE puntos_habilitados ADD COLUMN numero_expediente VARCHAR(100) NULL AFTER item_id");
            $changes[] = "✅ Columna 'numero_expediente' agregada a puntos_habilitados";
        } catch (Exception $e) {
            $changes[] = "❌ Error agregando 'numero_expediente': " . $e->getMessage();
        }
    } else {
        $changes[] = "✅ Columna 'numero_expediente' ya existe en puntos_habilitados";
    }
    
    if (!$hasExtracto) {
        try {
            $db->exec("ALTER TABLE puntos_habilitados ADD COLUMN extracto TEXT NULL AFTER numero_expediente");
            $changes[] = "✅ Columna 'extracto' agregada a puntos_habilitados";
        } catch (Exception $e) {
            $changes[] = "❌ Error agregando 'extracto': " . $e->getMessage();
        }
    } else {
        $changes[] = "✅ Columna 'extracto' ya existe en puntos_habilitados";
    }
    
    echo "<p><strong>Cambios aplicados:</strong></p>";
    echo "<ul>";
    foreach ($changes as $change) {
        echo "<li>{$change}</li>";
    }
    echo "</ul>";
    
    // Verificar estructura final
    echo "<p><strong>Verificando estructura final...</strong></p>";
    $stmt = $db->query("DESCRIBE puntos_habilitados");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        $rowClass = ($col['Field'] === 'numero_expediente' || $col['Field'] === 'extracto') ? 'style="background-color: #d4edda;"' : '';
        echo "<tr {$rowClass}>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Probar la consulta problemática
    echo "<h2>Prueba de consulta problemática</h2>";
    try {
        // Buscar una sesión que exista
        $stmt = $db->query("SELECT id FROM sesiones_votacion LIMIT 1");
        $sesion = $stmt->fetch();
        
        if ($sesion) {
            $sesionId = $sesion['id'];
            echo "<p>Probando con sesión ID: {$sesionId}</p>";
            
            $query = "SELECT id, item_tipo, item_id, numero_expediente, extracto, orden_punto, habilitado, fecha_habilitacion
                      FROM puntos_habilitados
                      WHERE sesion_id = ? AND habilitado = 1
                      ORDER BY orden_punto ASC";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$sesionId]);
            $results = $stmt->fetchAll();
            
            echo "<p>✅ <strong>¡Consulta exitosa!</strong> La consulta que causaba el error ahora funciona correctamente.</p>";
            echo "<p>Resultados encontrados: " . count($results) . "</p>";
            
        } else {
            echo "<p>⚠️ No hay sesiones disponibles para probar, pero la estructura es correcta.</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ <strong>Error en la consulta:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<hr>";
    echo "<h2>🎉 ¡Reparación Completada!</h2>";
    echo "<p><strong>El problema del error SQL debería estar completamente solucionado.</strong></p>";
    echo "<p><a href='/votacion/vista-publica' class='btn' style='background: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🧪 Probar Vista Pública</a></p>";
    echo "<p><a href='/'>Ir al Sistema Principal</a> | <a href='navegacion_test.php'>Ir a Pruebas</a></p>";
    
} catch (Exception $e) {
    echo "<p><strong>Error general:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>