<?php
/**
 * Script de diagnóstico para verificar datos de pantalla grande
 */

require_once 'config/config.php';
require_once 'config/database.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h1>🔍 Diagnóstico Pantalla Grande</h1>";
    echo "<style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        h2 { color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #667eea; color: white; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
    </style>";
    
    // 1. Verificar sesión activa
    echo "<h2>1️⃣ Sesión Activa</h2>";
    $stmt = $db->query("
        SELECT sv.*, od.numero_acta, od.fecha_sesion, od.tipo_sesion 
        FROM sesiones_votacion sv
        LEFT JOIN orden_dia od ON sv.orden_dia_id = od.id
        WHERE sv.estado IN ('activa', 'pausada')
        ORDER BY sv.id DESC 
        LIMIT 1
    ");
    $sesion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($sesion) {
        echo "<p class='success'>✅ Sesión encontrada: ID {$sesion['id']}</p>";
        echo "<table>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        foreach ($sesion as $key => $value) {
            echo "<tr><td>{$key}</td><td>{$value}</td></tr>";
        }
        echo "</table>";
        
        $sesionId = $sesion['id'];
        
        // 2. Verificar puntos habilitados
        echo "<h2>2️⃣ Puntos Habilitados</h2>";
        $stmt = $db->prepare("
            SELECT * FROM puntos_habilitados 
            WHERE sesion_id = ? 
            ORDER BY orden_punto ASC
        ");
        $stmt->execute([$sesionId]);
        $puntos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($puntos)) {
            echo "<p class='success'>✅ " . count($puntos) . " puntos encontrados</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Orden</th><th>Tipo</th><th>Item ID</th><th>Número</th><th>Habilitado</th><th>Extracto</th></tr>";
            foreach ($puntos as $p) {
                $habilitado = $p['habilitado'] ? '✅ SÍ' : '❌ NO';
                $clase = $p['habilitado'] ? 'success' : 'error';
                echo "<tr>";
                echo "<td>{$p['id']}</td>";
                echo "<td>{$p['orden_punto']}</td>";
                echo "<td>{$p['item_tipo']}</td>";
                echo "<td>{$p['item_id']}</td>";
                echo "<td>{$p['numero_expediente']}</td>";
                echo "<td class='{$clase}'>{$habilitado}</td>";
                echo "<td>" . substr($p['extracto'], 0, 100) . "...</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ No hay puntos en la tabla puntos_habilitados</p>";
        }
        
        // 3. Verificar presentes
        echo "<h2>3️⃣ Presentes en Sesión</h2>";
        $stmt = $db->prepare("
            SELECT ps.*, u.first_name, u.last_name, u.username, r.name AS role_name
            FROM presentes_sesion ps
            JOIN users u ON ps.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            WHERE ps.sesion_id = ?
            ORDER BY u.last_name, u.first_name
        ");
        $stmt->execute([$sesionId]);
        $presentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($presentes)) {
            echo "<p class='success'>✅ " . count($presentes) . " personas registradas</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Rol</th><th>Presente</th></tr>";
            foreach ($presentes as $p) {
                $presente = $p['presente'] ? '✅ SÍ' : '❌ NO';
                $clase = $p['presente'] ? 'success' : 'error';
                echo "<tr>";
                echo "<td>{$p['user_id']}</td>";
                echo "<td>{$p['first_name']} {$p['last_name']}</td>";
                echo "<td>{$p['role_name']}</td>";
                echo "<td class='{$clase}'>{$presente}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Solo editores presentes
            $editoresPresentes = array_filter($presentes, function($p) {
                return $p['role_name'] === 'editor' && $p['presente'] == 1;
            });
            echo "<p class='warning'>📊 Editores presentes: " . count($editoresPresentes) . "</p>";
        } else {
            echo "<p class='error'>❌ No hay presentes registrados</p>";
        }
        
        // 4. Verificar votos
        echo "<h2>4️⃣ Votos Registrados</h2>";
        $stmt = $db->prepare("
            SELECT v.*, u.first_name, u.last_name
            FROM votos v
            JOIN users u ON v.user_id = u.id
            WHERE v.sesion_id = ?
            ORDER BY v.fecha_voto DESC
        ");
        $stmt->execute([$sesionId]);
        $votos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($votos)) {
            echo "<p class='success'>✅ " . count($votos) . " votos registrados</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Usuario</th><th>Tipo Item</th><th>Item ID</th><th>Tipo Voto</th><th>Número Exp</th><th>Fecha</th></tr>";
            foreach ($votos as $v) {
                echo "<tr>";
                echo "<td>{$v['id']}</td>";
                echo "<td>{$v['first_name']} {$v['last_name']}</td>";
                echo "<td>{$v['item_votacion_tipo']}</td>";
                echo "<td>{$v['item_votacion_id']}</td>";
                echo "<td><strong>{$v['tipo_voto']}</strong></td>";
                echo "<td>{$v['numero_expediente']}</td>";
                echo "<td>{$v['fecha_voto']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='warning'>⚠️ No hay votos registrados aún</p>";
        }
        
    } else {
        echo "<p class='error'>❌ No hay sesión activa o pausada</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='vista-pantalla-grande.php' target='_blank'>🖥️ Abrir Vista Pantalla Grande</a></p>";
    echo "<p><a href='javascript:location.reload()'>🔄 Recargar diagnóstico</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
