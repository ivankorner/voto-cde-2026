<?php
// Script para sincronizar correctamente puntos_habilitados con expedientes
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h1>Sincronización de Puntos Habilitados</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Buscar la sesión más reciente
    $stmt = $db->query("SELECT id, nombre, orden_dia_id FROM sesiones_votacion ORDER BY id DESC LIMIT 1");
    $sesion = $stmt->fetch();
    
    if ($sesion) {
        $sesionId = $sesion['id'];
        $ordenDiaId = $sesion['orden_dia_id'];
        
        echo "<h2>Sesión: {$sesion['nombre']} (ID: {$sesionId})</h2>";
        echo "<p>Orden del día ID: {$ordenDiaId}</p>";
        
        // 1. Obtener todos los expedientes del orden del día
        echo "<h3>1. Obteniendo expedientes del orden del día:</h3>";
        
        $queryExpedientes = "SELECT ode.id, ode.numero_expediente, ode.extracto, odi.numero_orden
                            FROM orden_dia_expedientes ode
                            JOIN orden_dia_items odi ON ode.orden_dia_item_id = odi.id
                            WHERE odi.orden_dia_id = ? AND ode.numero_expediente IS NOT NULL
                            ORDER BY odi.numero_orden, ode.id";
        
        $stmt = $db->prepare($queryExpedientes);
        $stmt->execute([$ordenDiaId]);
        $expedientes = $stmt->fetchAll();
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Expediente ID</th><th>Número</th><th>Extracto</th><th>Orden</th></tr>";
        
        foreach ($expedientes as $exp) {
            echo "<tr>";
            echo "<td>{$exp['id']}</td>";
            echo "<td>{$exp['numero_expediente']}</td>";
            echo "<td>" . substr($exp['extracto'], 0, 80) . "...</td>";
            echo "<td>{$exp['numero_orden']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // 2. Limpiar puntos existentes para esta sesión y recrearlos
        echo "<h3>2. Recreando puntos habilitados:</h3>";
        
        // Eliminar puntos existentes
        $stmt = $db->prepare("DELETE FROM puntos_habilitados WHERE sesion_id = ?");
        $stmt->execute([$sesionId]);
        echo "<p>✅ Puntos anteriores eliminados</p>";
        
        // Recrear puntos
        $insertQuery = "INSERT INTO puntos_habilitados 
                       (sesion_id, item_tipo, item_id, numero_expediente, extracto, orden_punto, habilitado) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($insertQuery);
        
        // Punto 1: Apertura de sesión
        $stmt->execute([$sesionId, 'global', 0, 'Apertura de Sesión', 'Llamado a lista y verificación de quórum', 1, 1]);
        echo "<p>✅ Punto 1: Apertura de Sesión - Habilitado</p>";
        
        // Puntos de expedientes
        $orden = 2;
        $habilitados = 0;
        foreach ($expedientes as $exp) {
            $habilitado = $habilitados < 4 ? 1 : 0; // Habilitar solo los primeros 4
            $stmt->execute([
                $sesionId, 
                'expediente', 
                $exp['id'], 
                $exp['numero_expediente'], 
                $exp['extracto'], 
                $orden, 
                $habilitado
            ]);
            
            $status = $habilitado ? 'Habilitado' : 'No habilitado';
            echo "<p>✅ Punto {$orden}: {$exp['numero_expediente']} - {$status}</p>";
            
            $orden++;
            if ($habilitado) $habilitados++;
        }
        
        // Punto de cierre
        $stmt->execute([$sesionId, 'global', 0, 'Cierre de Sesión', 'Cierre de la sesión y próximos pasos', $orden, 0]);
        echo "<p>✅ Punto {$orden}: Cierre de Sesión - No habilitado</p>";
        
        // 3. Verificar resultado final
        echo "<h3>3. Verificación final:</h3>";
        
        $stmt = $db->prepare("SELECT * FROM puntos_habilitados WHERE sesion_id = ? ORDER BY orden_punto");
        $stmt->execute([$sesionId]);
        $puntosFinales = $stmt->fetchAll();
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Orden</th><th>Tipo</th><th>Número Exp.</th><th>Extracto</th><th>Habilitado</th></tr>";
        
        foreach ($puntosFinales as $punto) {
            $habilitadoClass = $punto['habilitado'] ? 'style="background-color: #d4edda;"' : 'style="background-color: #f8d7da;"';
            
            echo "<tr {$habilitadoClass}>";
            echo "<td>{$punto['orden_punto']}</td>";
            echo "<td>{$punto['item_tipo']}</td>";
            echo "<td>" . ($punto['numero_expediente'] ?? 'N/A') . "</td>";
            echo "<td>" . substr($punto['extracto'] ?? 'N/A', 0, 60) . "...</td>";
            echo "<td>" . ($punto['habilitado'] ? 'SÍ' : 'NO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<hr>";
        echo "<h2>🎉 ¡Sincronización Completada!</h2>";
        echo "<p><strong>Los puntos habilitados ahora deberían mostrar correctamente en la vista pública.</strong></p>";
        
    } else {
        echo "<p>❌ No se encontró ninguna sesión.</p>";
    }
    
    echo "<hr>";
    echo "<h2>🧪 Prueba la vista pública</h2>";
    echo "<p><a href='/votacion/vista-publica' style='background: green; color: white; padding: 15px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Ver Vista Pública Actualizada</a></p>";
    
} catch (Exception $e) {
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>