<?php
// Script de depuración para encontrar el error de numero_expediente
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h1>Depuración Error numero_expediente</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>1. Verificando estructura de tablas:</h2>";
    echo "<ul>";
    
    // Listar todas las tablas
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "<li><strong>Tabla: {$table}</strong><br>";
        
        // Verificar si tiene columna numero_expediente
        try {
            $stmt = $db->query("DESCRIBE {$table}");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $hasNumeroExpediente = false;
            echo "&nbsp;&nbsp;Columnas: ";
            $columnNames = [];
            foreach ($columns as $col) {
                $columnNames[] = $col['Field'];
                if ($col['Field'] === 'numero_expediente') {
                    $hasNumeroExpediente = true;
                }
            }
            echo implode(', ', $columnNames) . "<br>";
            
            if ($hasNumeroExpediente) {
                echo "&nbsp;&nbsp;✅ <strong>Tiene numero_expediente</strong><br>";
            } else {
                echo "&nbsp;&nbsp;❌ No tiene numero_expediente<br>";
            }
            
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;Error describiendo tabla: " . $e->getMessage() . "<br>";
        }
        echo "</li>";
    }
    
    echo "</ul>";
    
    echo "<h2>2. Probando consultas específicas del modelo Votacion:</h2>";
    echo "<ul>";
    
    // Probar consulta específica que falla
    echo "<li>Probando getPuntosHabilitados...";
    try {
        // Simular una sesión ID que existe
        $stmt = $db->query("SELECT id FROM sesiones_votacion LIMIT 1");
        $sesion = $stmt->fetch();
        
        if ($sesion) {
            $sesionId = $sesion['id'];
            echo " (usando sesión ID: {$sesionId})<br>";
            
            $query = "SELECT id, item_tipo, item_id, numero_expediente, extracto, orden_punto, habilitado, fecha_habilitacion
                      FROM puntos_habilitados
                      WHERE sesion_id = ? AND habilitado = 1
                      ORDER BY orden_punto ASC";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$sesionId]);
            $results = $stmt->fetchAll();
            
            echo "&nbsp;&nbsp;✅ Consulta exitosa - " . count($results) . " resultados";
        } else {
            echo " - No hay sesiones disponibles para probar";
        }
    } catch (Exception $e) {
        echo "<br>&nbsp;&nbsp;❌ <strong>ERROR:</strong> " . $e->getMessage();
    }
    echo "</li>";
    
    // Probar otras consultas problemáticas
    echo "<li>Probando consulta de expedientes...";
    try {
        $query = "SELECT ode.id, ode.numero_expediente, ode.extracto
                  FROM orden_dia_expedientes ode
                  LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        echo " ✅ Consulta exitosa - " . count($results) . " resultados";
    } catch (Exception $e) {
        echo " ❌ <strong>ERROR:</strong> " . $e->getMessage();
    }
    echo "</li>";
    
    echo "</ul>";
    
    echo "<h2>3. Verificando tabla orden_dia_expedientes:</h2>";
    try {
        $stmt = $db->query("DESCRIBE orden_dia_expedientes");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage();
    }
    
} catch (Exception $e) {
    echo "<p><strong>Error general:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>