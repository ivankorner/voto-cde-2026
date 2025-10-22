<?php
// Script para agregar columnas faltantes en orden_dia_expedientes
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h1>Reparación de Columnas Faltantes</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<p><strong>Verificando estructura de orden_dia_expedientes...</strong></p>";
    
    // Verificar estructura actual
    $stmt = $db->query("DESCRIBE orden_dia_expedientes");
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
            $db->exec("ALTER TABLE orden_dia_expedientes ADD COLUMN numero_expediente VARCHAR(100) NULL AFTER orden_dia_item_id");
            $changes[] = "✅ Columna 'numero_expediente' agregada";
        } catch (Exception $e) {
            $changes[] = "❌ Error agregando 'numero_expediente': " . $e->getMessage();
        }
    } else {
        $changes[] = "✅ Columna 'numero_expediente' ya existe";
    }
    
    if (!$hasExtracto) {
        try {
            $db->exec("ALTER TABLE orden_dia_expedientes ADD COLUMN extracto TEXT NULL AFTER numero_expediente");
            $changes[] = "✅ Columna 'extracto' agregada";
        } catch (Exception $e) {
            $changes[] = "❌ Error agregando 'extracto': " . $e->getMessage();
        }
    } else {
        $changes[] = "✅ Columna 'extracto' ya existe";
    }
    
    echo "<p><strong>Cambios aplicados:</strong></p>";
    echo "<ul>";
    foreach ($changes as $change) {
        echo "<li>{$change}</li>";
    }
    echo "</ul>";
    
    // Verificar estructura final
    echo "<p><strong>Verificando estructura final...</strong></p>";
    $stmt = $db->query("DESCRIBE orden_dia_expedientes");
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
        $query = "SELECT ode.id, ode.numero_expediente, ode.extracto
                  FROM orden_dia_expedientes ode
                  LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        echo "<p>✅ <strong>¡Consulta exitosa!</strong> La consulta que causaba el error ahora funciona correctamente.</p>";
        
        if ($result) {
            echo "<p>Datos de prueba: ID={$result['id']}, Número={$result['numero_expediente']}, Extracto=" . substr($result['extracto'] ?? 'NULL', 0, 50) . "...</p>";
        } else {
            echo "<p>No hay datos en la tabla, pero la estructura es correcta.</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ <strong>Error en la consulta:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<p><strong>✅ Reparación completada.</strong></p>";
    echo "<p><a href='/votacion/vista-publica'>Probar Vista Pública</a> | <a href='/'>Ir al Sistema Principal</a></p>";
    
} catch (Exception $e) {
    echo "<p><strong>Error general:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>