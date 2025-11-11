<?php
/**
 * Herramienta de Verificación y Limpieza de Votos
 * Acceder vía: http://tu-dominio.com/debug_votos.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

session_start();

echo "<h1>🗳️ Diagnóstico de Votos</h1>";
echo "<style>body{font-family:Arial;} .error{color:red;} .success{color:green;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border:1px solid #ddd;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f5f5f5;}</style>";

if (!isset($_SESSION['user_id'])) {
    echo "<p class='error'>❌ Debes estar logueado</p>";
    echo "<a href='index.php'>Ir al login</a>";
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Obtener sesión activa
    $stmt = $db->prepare("SELECT id, nombre FROM sesiones_votacion WHERE estado = 'activa' ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $sesion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$sesion) {
        echo "<p class='error'>❌ No hay sesión activa</p>";
        exit;
    }
    
    $sesionId = $sesion['id'];
    echo "<p class='info'>📌 Sesión activa: {$sesion['nombre']} (ID: $sesionId)</p>";
    
    // 1. Mostrar todos los votos de la sesión
    echo "<h2>📊 Votos en la Sesión Actual</h2>";
    
    $stmt = $db->prepare("
        SELECT v.*, u.first_name, u.last_name, u.username 
        FROM votos v 
        JOIN users u ON v.user_id = u.id 
        WHERE v.sesion_id = ? 
        ORDER BY v.fecha_voto DESC
    ");
    $stmt->execute([$sesionId]);
    $votos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($votos)) {
        echo "<p class='info'>ℹ️ No hay votos registrados en esta sesión</p>";
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Usuario</th><th>Tipo Item</th><th>Item ID</th><th>Tipo Voto</th><th>Fecha</th><th>IP</th></tr>";
        foreach ($votos as $voto) {
            $itemIdDisplay = $voto['item_votacion_id'] === null ? 'NULL' : $voto['item_votacion_id'];
            echo "<tr>";
            echo "<td>{$voto['id']}</td>";
            echo "<td>{$voto['first_name']} {$voto['last_name']} ({$voto['username']})</td>";
            echo "<td>{$voto['item_votacion_tipo']}</td>";
            echo "<td>{$itemIdDisplay}</td>";
            echo "<td>{$voto['tipo_voto']}</td>";
            echo "<td>{$voto['fecha_voto']}</td>";
            echo "<td>{$voto['ip_address']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 2. Verificar duplicados
    echo "<h2>🔍 Detección de Votos Duplicados</h2>";
    
    $stmt = $db->prepare("
        SELECT sesion_id, user_id, item_votacion_tipo, item_votacion_id, COUNT(*) as cantidad
        FROM votos 
        WHERE sesion_id = ?
        GROUP BY sesion_id, user_id, item_votacion_tipo, item_votacion_id
        HAVING COUNT(*) > 1
    ");
    $stmt->execute([$sesionId]);
    $duplicados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($duplicados)) {
        echo "<p class='success'>✅ No hay votos duplicados</p>";
    } else {
        echo "<p class='error'>❌ Se encontraron votos duplicados:</p>";
        echo "<table>";
        echo "<tr><th>Usuario ID</th><th>Tipo Item</th><th>Item ID</th><th>Cantidad</th><th>Acción</th></tr>";
        foreach ($duplicados as $dup) {
            $itemIdDisplay = $dup['item_votacion_id'] === null ? 'NULL' : $dup['item_votacion_id'];
            echo "<tr>";
            echo "<td>{$dup['user_id']}</td>";
            echo "<td>{$dup['item_votacion_tipo']}</td>";
            echo "<td>{$itemIdDisplay}</td>";
            echo "<td>{$dup['cantidad']}</td>";
            echo "<td><a href='?limpiar_duplicado=1&sesion={$dup['sesion_id']}&user={$dup['user_id']}&tipo={$dup['item_votacion_tipo']}&item={$dup['item_votacion_id']}'>🧹 Limpiar</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 3. Procesar limpieza de duplicados
    if (isset($_GET['limpiar_duplicado'])) {
        $sesionLimpiar = $_GET['sesion'];
        $userLimpiar = $_GET['user'];
        $tipoLimpiar = $_GET['tipo'];
        $itemLimpiar = $_GET['item'] === '' ? null : $_GET['item'];
        
        echo "<h3>🧹 Limpiando Duplicados</h3>";
        
        // Obtener todos los votos duplicados para este caso
        if ($itemLimpiar === null) {
            $stmt = $db->prepare("
                SELECT id, fecha_voto 
                FROM votos 
                WHERE sesion_id = ? AND user_id = ? AND item_votacion_tipo = ? AND item_votacion_id IS NULL
                ORDER BY fecha_voto ASC
            ");
            $stmt->execute([$sesionLimpiar, $userLimpiar, $tipoLimpiar]);
        } else {
            $stmt = $db->prepare("
                SELECT id, fecha_voto 
                FROM votos 
                WHERE sesion_id = ? AND user_id = ? AND item_votacion_tipo = ? AND item_votacion_id = ?
                ORDER BY fecha_voto ASC
            ");
            $stmt->execute([$sesionLimpiar, $userLimpiar, $tipoLimpiar, $itemLimpiar]);
        }
        
        $votosDuplicados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($votosDuplicados) > 1) {
            // Mantener solo el primer voto (más antiguo) y eliminar el resto
            $primerVoto = array_shift($votosDuplicados);
            echo "<p class='info'>✅ Manteniendo voto ID: {$primerVoto['id']} (fecha: {$primerVoto['fecha_voto']})</p>";
            
            foreach ($votosDuplicados as $votoEliminar) {
                $stmt = $db->prepare("DELETE FROM votos WHERE id = ?");
                $stmt->execute([$votoEliminar['id']]);
                echo "<p class='success'>🗑️ Eliminado voto duplicado ID: {$votoEliminar['id']} (fecha: {$votoEliminar['fecha_voto']})</p>";
            }
            
            echo "<p class='success'>✅ Limpieza completada. <a href='?'>🔄 Recargar</a></p>";
        } else {
            echo "<p class='info'>ℹ️ No hay duplicados para limpiar</p>";
        }
    }
    
    // 4. Test rápido de voto para usuario actual
    echo "<h2>🧪 Test de Voto (Usuario Actual)</h2>";
    
    $userId = $_SESSION['user_id'];
    
    // Verificar si puede votar actas
    require_once __DIR__ . '/app/models/Votacion.php';
    $votacionModel = new Votacion();
    
    $puedeVotar = $votacionModel->puedeVotar($userId);
    $actasHabilitado = $votacionModel->isPuntoHabilitado($sesionId, 'actas', 0);
    $yaVotoActas = $votacionModel->yaVoto($sesionId, $userId, 'actas', 0);
    
    echo "<p>Usuario ID: $userId</p>";
    echo "<p>Puede votar: " . ($puedeVotar ? "✅ SÍ" : "❌ NO") . "</p>";
    echo "<p>Actas habilitado: " . ($actasHabilitado ? "✅ SÍ" : "❌ NO") . "</p>";
    echo "<p>Ya votó actas: " . ($yaVotoActas ? "❌ SÍ" : "✅ NO") . "</p>";
    
    if ($puedeVotar && $actasHabilitado && !$yaVotoActas) {
        echo "<p class='success'>✅ El usuario debería poder votar actas sin problemas</p>";
    } else if ($yaVotoActas) {
        echo "<p class='info'>ℹ️ El usuario ya votó actas (normal, no es error)</p>";
    } else {
        echo "<p class='error'>❌ El usuario no puede votar actas por algún motivo</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><p><small>Diagnóstico completado a las " . date('Y-m-d H:i:s') . "</small></p>";
?>