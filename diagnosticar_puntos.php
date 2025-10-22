<?php
// Script para diagnosticar y llenar datos en puntos_habilitados
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h1>Diagnóstico de Puntos Habilitados</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Buscar sesión activa
    $stmt = $db->query("SELECT id, nombre FROM sesiones_votacion WHERE estado = 'activa' ORDER BY id DESC LIMIT 1");
    $sesionActiva = $stmt->fetch();
    
    if (!$sesionActiva) {
        $stmt = $db->query("SELECT id, nombre FROM sesiones_votacion ORDER BY id DESC LIMIT 1");
        $sesionActiva = $stmt->fetch();
    }
    
    if ($sesionActiva) {
        $sesionId = $sesionActiva['id'];
        echo "<h2>Analizando Sesión ID: {$sesionId} - {$sesionActiva['nombre']}</h2>";
        
        // 1. Verificar datos actuales en puntos_habilitados
        echo "<h3>1. Datos actuales en puntos_habilitados:</h3>";
        $stmt = $db->prepare("SELECT * FROM puntos_habilitados WHERE sesion_id = ? ORDER BY orden_punto");
        $stmt->execute([$sesionId]);
        $puntos = $stmt->fetchAll();
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Orden</th><th>Tipo</th><th>Item ID</th><th>Número Exp.</th><th>Extracto</th><th>Habilitado</th></tr>";
        
        foreach ($puntos as $punto) {
            echo "<tr>";
            echo "<td>{$punto['id']}</td>";
            echo "<td>{$punto['orden_punto']}</td>";
            echo "<td>{$punto['item_tipo']}</td>";
            echo "<td>{$punto['item_id']}</td>";
            echo "<td>" . ($punto['numero_expediente'] ?? 'NULL') . "</td>";
            echo "<td>" . (substr($punto['extracto'] ?? 'NULL', 0, 50)) . "</td>";
            echo "<td>" . ($punto['habilitado'] ? 'Sí' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // 2. Verificar si hay datos en orden_dia_expedientes para esta sesión
        echo "<h3>2. Expedientes disponibles para esta sesión:</h3>";
        
        $query = "SELECT sv.orden_dia_id, ode.id, ode.numero_expediente, ode.extracto
                  FROM sesiones_votacion sv
                  LEFT JOIN orden_dia_items odi ON odi.orden_dia_id = sv.orden_dia_id
                  LEFT JOIN orden_dia_expedientes ode ON ode.orden_dia_item_id = odi.id
                  WHERE sv.id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$sesionId]);
        $expedientes = $stmt->fetchAll();
        
        if (empty($expedientes)) {
            echo "<p>❌ No se encontraron expedientes asociados a esta sesión.</p>";
        } else {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Orden Día ID</th><th>Expediente ID</th><th>Número</th><th>Extracto</th></tr>";
            foreach ($expedientes as $exp) {
                echo "<tr>";
                echo "<td>{$exp['orden_dia_id']}</td>";
                echo "<td>{$exp['id']}</td>";
                echo "<td>" . ($exp['numero_expediente'] ?? 'NULL') . "</td>";
                echo "<td>" . (substr($exp['extracto'] ?? 'NULL', 0, 100)) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // 3. Intentar repoblar los datos
        echo "<h3>3. Repoblando datos en puntos_habilitados:</h3>";
        
        if (!empty($expedientes) && $expedientes[0]['id']) {
            echo "<p><strong>Intentando sincronizar datos...</strong></p>";
            
            // Actualizar puntos existentes con datos de expedientes
            $updateQuery = "UPDATE puntos_habilitados ph
                           LEFT JOIN orden_dia_expedientes ode ON ph.item_id = ode.id
                           SET ph.numero_expediente = ode.numero_expediente,
                               ph.extracto = ode.extracto
                           WHERE ph.sesion_id = ? AND ph.item_tipo = 'expediente' AND ode.id IS NOT NULL";
            
            $stmt = $db->prepare($updateQuery);
            $result = $stmt->execute([$sesionId]);
            
            if ($result) {
                $affected = $stmt->rowCount();
                echo "<p>✅ Actualizados {$affected} puntos con datos de expedientes.</p>";
            } else {
                echo "<p>❌ Error actualizando puntos.</p>";
            }
        }
        
        // 4. Verificar datos después de la actualización
        echo "<h3>4. Datos después de la sincronización:</h3>";
        $stmt = $db->prepare("SELECT * FROM puntos_habilitados WHERE sesion_id = ? ORDER BY orden_punto");
        $stmt->execute([$sesionId]);
        $puntosActualizados = $stmt->fetchAll();
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Orden</th><th>Tipo</th><th>Item ID</th><th>Número Exp.</th><th>Extracto</th><th>Habilitado</th></tr>";
        
        foreach ($puntosActualizados as $punto) {
            $hasData = !empty($punto['numero_expediente']) || !empty($punto['extracto']);
            $rowStyle = $hasData ? 'style="background-color: #d4edda;"' : 'style="background-color: #f8d7da;"';
            
            echo "<tr {$rowStyle}>";
            echo "<td>{$punto['id']}</td>";
            echo "<td>{$punto['orden_punto']}</td>";
            echo "<td>{$punto['item_tipo']}</td>";
            echo "<td>{$punto['item_id']}</td>";
            echo "<td>" . ($punto['numero_expediente'] ?? 'VACÍO') . "</td>";
            echo "<td>" . (substr($punto['extracto'] ?? 'VACÍO', 0, 50)) . "</td>";
            echo "<td>" . ($punto['habilitado'] ? 'Sí' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p>❌ No se encontraron sesiones disponibles.</p>";
    }
    
    echo "<hr>";
    echo "<h2>🧪 Prueba la vista pública ahora</h2>";
    echo "<p><a href='/votacion/vista-publica' style='background: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Ver Vista Pública</a></p>";
    
} catch (Exception $e) {
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>