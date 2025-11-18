<?php
// Vista pública optimizada para pantallas grandes (65+ pulgadas)
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Obtener la sesión más reciente activa
    $stmt = $db->query("
        SELECT sv.*, od.numero_acta, od.fecha_sesion, od.tipo_sesion 
        FROM sesiones_votacion sv
        LEFT JOIN orden_dia od ON sv.orden_dia_id = od.id
        WHERE sv.estado IN ('activa', 'pausada')
        ORDER BY sv.id DESC 
        LIMIT 1
    ");
    
    $sesion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$sesion) {
        $sesion = [
            'id' => 0,
            'nombre' => 'No hay sesión activa',
            'numero_acta' => '-',
            'fecha_sesion' => date('Y-m-d'),
            'estado' => 'inactiva'
        ];
    }
    
    // Obtener TODOS los puntos habilitados para votación (los que están abiertos ahora)
    $puntosHabilitados = [];
    if ($sesion['id'] > 0) {
        // Obtener todos los puntos habilitados actualmente
        $stmt = $db->prepare("
            SELECT ph.*, 
                   COALESCE(ph.numero_expediente, 'Sin número') as numero_expediente,
                   COALESCE(ph.extracto, 'Sin descripción') as extracto
            FROM puntos_habilitados ph
            WHERE ph.sesion_id = ? AND ph.habilitado = 1
            ORDER BY ph.orden_punto ASC
        ");
        $stmt->execute([$sesion['id']]);
        $puntosHabilitados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener miembros presentes
    $presentes = [];
    if ($sesion['id'] > 0) {
        $stmt = $db->prepare("
            SELECT ps.*, u.first_name, u.last_name, u.username, r.name AS role_name
            FROM presentes_sesion ps
            JOIN users u ON ps.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            WHERE ps.sesion_id = ? AND ps.presente = 1 AND r.name = 'editor'
            ORDER BY u.last_name, u.first_name
        ");
        $stmt->execute([$sesion['id']]);
        $presentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener resultados de votaciones y calcular total de votos por punto
    $resultadosVotacion = [];
    if ($sesion['id'] > 0 && !empty($puntosHabilitados)) {
        foreach ($puntosHabilitados as &$punto) {
            // Determinar el item_id según el tipo
            $itemIdParaQuery = null;
            if ($punto['item_tipo'] === 'expediente') {
                $itemIdParaQuery = $punto['item_id'];
            } elseif ($punto['item_tipo'] === 'actas') {
                $itemIdParaQuery = 0;
            }
            
            if ($itemIdParaQuery !== null) {
                $stmt = $db->prepare("
                    SELECT 
                        tipo_voto,
                        COUNT(*) as cantidad,
                        GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name) SEPARATOR ', ') as votantes
                    FROM votos v
                    JOIN users u ON v.user_id = u.id
                    WHERE v.sesion_id = ? AND v.item_votacion_id = ? AND v.item_votacion_tipo = ?
                    GROUP BY tipo_voto
                ");
                $stmt->execute([$sesion['id'], $itemIdParaQuery, $punto['item_tipo']]);
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $resultadosVotacion[$punto['id']] = [
                    'positivo' => 0,
                    'negativo' => 0,
                    'abstencion' => 0,
                    'votantes' => [],
                    'total_votos' => 0
                ];
                
                foreach ($resultados as $resultado) {
                    $resultadosVotacion[$punto['id']][$resultado['tipo_voto']] = $resultado['cantidad'];
                    $resultadosVotacion[$punto['id']]['votantes'][$resultado['tipo_voto']] = $resultado['votantes'];
                    $resultadosVotacion[$punto['id']]['total_votos'] += $resultado['cantidad'];
                }
                
                // Guardar el total de votos en el punto para ordenamiento
                $punto['total_votos'] = $resultadosVotacion[$punto['id']]['total_votos'];
            } else {
                $punto['total_votos'] = 0;
            }
        }
        unset($punto); // Romper la referencia
        
        // ORDENAR: Primero el punto con MENOS votos (el que se está votando ahora)
        // Luego el resto por orden ascendente de puntos
        usort($puntosHabilitados, function($a, $b) {
            // Si tienen diferente cantidad de votos, el de menos votos primero
            if ($a['total_votos'] !== $b['total_votos']) {
                return $a['total_votos'] <=> $b['total_votos'];
            }
            // Si tienen la misma cantidad, ordenar por orden_punto
            return $a['orden_punto'] <=> $b['orden_punto'];
        });
    }
    
} catch (Exception $e) {
    error_log("Error en vista pantalla grande: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    $sesion = [
        'id' => 0,
        'nombre' => 'Error de conexión',
        'numero_acta' => '-',
        'fecha_sesion' => date('Y-m-d'),
        'estado' => 'error'
    ];
    $puntosHabilitados = [];
    $presentes = [];
    $resultadosVotacion = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión de Votación - Pantalla Grande</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* === Viewport adaptativo (encaja 100% ventana) === */
        :root {
            --target-width: 100vw;
            --target-height: 100vh;
            --scale: 1; /* sin escalado, todo fluido */
            /* escalas dinámicas para textos/espaciados */
            --header-h: clamp(120px, 12vh, 220px);
            --pad: clamp(12px, 1vw, 24px);
            --gap: clamp(14px, 1.2vw, 28px);
            --fs-base: clamp(14px, 0.95vw, 20px);
            --fs-title: clamp(22px, 1.8vw, 38px);
            --fs-h1: clamp(28px, 2.5vw, 64px);
            --avatar: clamp(48px, 3vw, 100px);
        }
        html, body { width: 100%; height: 100%; }
        #viewport-4k {
            width: var(--target-width);
            height: var(--target-height);
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        /* === OPTIMIZACIÓN PARA PANTALLAS GRANDES === */
        
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #5a67d8 100%);
            --success-gradient: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            --warning-gradient: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            --danger-gradient: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --text-primary: #1a202c;
            --text-secondary: #4a5568;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            overflow: hidden; /* sin scroll en pantalla grande */
            position: relative;
            font-size: var(--fs-base);
        }
        
        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 25% 75%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 75% 25%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(102, 126, 234, 0.2) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
            z-index: -1;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-30px) rotate(2deg); }
            66% { transform: translateY(-15px) rotate(-1deg); }
        }
        
        /* === HEADER PRINCIPAL === */
        .header-pantalla-grande {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 4px solid rgba(65, 84, 241, 0.8);
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: calc(var(--pad) * 1.5) 0;
            min-height: var(--header-h);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 calc(var(--pad) * 2);
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .logo-circle {
            width: calc(var(--avatar) * 1.2);
            height: calc(var(--avatar) * 1.2);
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: clamp(20px, 1.6vw, 36px);
            font-weight: 800;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .titulo-principal {
            font-size: var(--fs-h1);
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .subtitulo-principal {
            font-size: clamp(14px, 1.1vw, 24px);
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .info-sesion {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .fecha-hora {
            font-size: clamp(16px, 1.4vw, 28px);
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .estado-sesion {
            padding: calc(var(--pad) * 0.8) calc(var(--pad) * 1.4);
            border-radius: 50px;
            font-weight: 600;
            font-size: clamp(12px, 1vw, 20px);
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .estado-activa {
            background: var(--success-gradient);
            color: white;
            animation: pulse-glow 2s infinite;
        }
        
        .estado-pausada {
            background: var(--warning-gradient);
            color: white;
        }
        
        .estado-inactiva {
            background: var(--danger-gradient);
            color: white;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3); }
            50% { box-shadow: 0 8px 35px rgba(40, 167, 69, 0.6); }
        }
        
        /* === GRID PRINCIPAL === */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr; /* Votación ocupa 2/3, presentes 1/3 */
            gap: var(--gap);
            padding: calc(var(--pad) * 2);
            max-width: 100%;
            margin: 0 auto;
            height: calc(100vh - var(--header-h) - 80px); /* Espacio para footer */
        }
        
        /* === SECCIÓN DE VOTACIÓN === */
        .votacion-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: calc(var(--pad) * 2);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--glass-border);
            height: 100%;
            overflow: hidden;
        }
        
        .section-title {
            font-size: var(--fs-title);
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--pad);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .section-title i {
            font-size: 2rem;
            color: #667eea;
        }
        
        .punto-votacion {
            background: white;
            border-radius: 20px;
            padding: calc(var(--pad) * 2);
            margin-bottom: calc(var(--pad) * 1.5);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
            border-left: 12px solid #667eea;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .punto-votacion::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        /* Indicador de votación activa */
        .votacion-activa {
            animation: pulse-border 2s infinite;
        }
        
        @keyframes pulse-border {
            0%, 100% { 
                border-left-color: #667eea;
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
            }
            50% { 
                border-left-color: #28a745;
                box-shadow: 0 20px 45px rgba(40, 167, 69, 0.3);
            }
        }
        
        .punto-votacion:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 25px 50px rgba(102, 126, 234, 0.25);
        }
        
        .punto-numero {
            font-size: clamp(18px, 1.5vw, 30px);
            font-weight: 800;
            color: #667eea;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .punto-numero::before {
            content: '●';
            animation: pulse 2s infinite;
        }
        
        .punto-expediente {
            font-size: clamp(20px, 1.6vw, 32px);
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.2rem;
            line-height: 1.3;
        }
        
        .punto-descripcion {
            font-size: clamp(16px, 1.2vw, 24px);
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: calc(var(--pad) * 1.5);
        }

        /* zona scrollable dentro de votación/hemiciclo */
        .section-content.scroll-area {
            height: calc(100% - (var(--fs-title) + var(--pad) * 2));
            overflow: auto;
            padding-right: 4px;
        }
        
        /* === RESULTADOS DE VOTACIÓN === */
        .resultados-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: calc(var(--gap) * 1.2);
            margin-top: calc(var(--pad) * 1.5);
        }
        
        .resultado-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 20px;
            padding: calc(var(--pad) * 1.8);
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .resultado-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            transition: height 0.3s ease;
        }
        
        .resultado-item:hover {
            transform: translateY(-5px) scale(1.05);
        }
        
        .resultado-item:hover::before {
            height: 12px;
        }
        
        .resultado-positivo::before {
            background: linear-gradient(90deg, #28a745, #20c997);
        }
        
        .resultado-negativo::before {
            background: linear-gradient(90deg, #dc3545, #e83e8c);
        }
        
        .resultado-abstencion::before {
            background: linear-gradient(90deg, #ffc107, #fd7e14);
        }
        
        .resultado-numero {
            font-size: clamp(32px, 3vw, 72px);
            font-weight: 900;
            margin-bottom: 0.5rem;
            line-height: 1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .resultado-positivo .resultado-numero {
            color: #28a745;
        }
        
        .resultado-negativo .resultado-numero {
            color: #dc3545;
        }
        
        .resultado-abstencion .resultado-numero {
            color: #ffc107;
        }
        
        .resultado-label {
            font-size: clamp(14px, 1.1vw, 22px);
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        .resultado-votantes {
            font-size: clamp(11px, 0.8vw, 16px);
            color: var(--text-secondary);
            margin-top: 0.8rem;
            padding-top: 0.8rem;
            border-top: 1px solid #e9ecef;
            line-height: 2;
            max-height: 150px;
            overflow-y: auto;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .badge-votante {
            display: inline-block;
            padding: 0.6rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: clamp(10px, 0.9vw, 15px);
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideInBadge 0.4s ease-out;
            transition: all 0.3s ease;
        }
        
        .badge-votante:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }
        
        .badge-positivo {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: 2px solid #1e7e34;
        }
        
        .badge-negativo {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
            color: white;
            border: 2px solid #a71d2a;
        }
        
        .badge-abstencion {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: #333;
            border: 2px solid #e0a800;
        }
        
        @keyframes slideInBadge {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* === HEMICICLO (COMPACTO) === */
        .hemiciclo-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: calc(var(--pad) * 1.5);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--glass-border);
            height: 100%;
            overflow: hidden;
        }
        
        .hemiciclo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: calc(var(--gap) * 0.8);
            margin-top: var(--pad);
        }
        
        .miembro-hemiciclo {
            background: white;
            border-radius: 12px;
            padding: calc(var(--pad) * 0.8);
            text-align: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }
        
        .miembro-presente {
            border-color: #28a745;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        }
        
        .miembro-avatar {
            width: clamp(40px, 2.5vw, 60px);
            height: clamp(40px, 2.5vw, 60px);
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.6rem;
            color: white;
            font-size: clamp(14px, 1.2vw, 22px);
            font-weight: 700;
        }
        
        .miembro-nombre {
            font-size: clamp(12px, 0.85vw, 16px);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.4rem;
            line-height: 1.2;
        }
        
        .miembro-estado {
            padding: calc(var(--pad) * 0.3) calc(var(--pad) * 0.6);
            border-radius: 20px;
            font-size: clamp(10px, 0.75vw, 14px);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .presente {
            background: #28a745;
            color: white;
        }
        
        .ausente {
            background: #6c757d;
            color: white;
        }
        
        /* === ÁREA DE MOCIONES === */
        .area-mociones-pantalla-grande {
            position: fixed;
            top: 150px;
            left: 5%;
            right: 5%;
            z-index: 10000;
            pointer-events: none;
        }
        
        .mocion-alert-grande {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 3px solid #f39c12;
            border-left: 10px solid #e67e22;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 25px 50px rgba(243, 156, 18, 0.4);
            animation: slideInDown 0.5s ease-out;
            pointer-events: auto;
        }
        
        .mocion-icon {
            font-size: 4rem;
            color: #e67e22;
            margin-right: 2rem;
            animation: pulse 2s infinite;
        }
        
        .mocion-content h2 {
            font-size: 2.5rem;
            color: #d68910;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        
        .mocion-texto {
            font-size: 1.8rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            line-height: 1.4;
        }
        
        .mocion-autor {
            font-size: 1.4rem;
            color: var(--text-secondary);
        }
        
        /* === ANIMACIONES === */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-100px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* === FOOTER === */
        .footer-pantalla-grande {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            padding: 1rem 3rem;
            border-top: 2px solid var(--glass-border);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 999;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 100%;
            margin: 0 auto;
        }
        
        .ultima-actualizacion {
            font-size: clamp(12px, 0.9vw, 18px);
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .auto-refresh-info {
            font-size: clamp(12px, 0.9vw, 18px);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .auto-refresh-info i {
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* === Ajustes mínimos extra grandes === */
        @media (min-width: 2560px) {
            :root { --gap: clamp(16px, 0.8vw, 36px); }
        }
        
        /* === ESTADOS ESPECIALES === */
        .sin-votacion {
            text-align: center;
            padding: 4rem;
            background: rgba(108, 117, 125, 0.1);
            border-radius: 20px;
            margin: 2rem 0;
        }
        
        .sin-votacion i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .sin-votacion h3 {
            font-size: 2rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }
        
        .sin-votacion p {
            font-size: 1.4rem;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <!-- Wrapper 4K fijo (4096x2160) -->
    <div id="viewport-4k">
    <!-- Header Principal -->
    <header class="header-pantalla-grande">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h1 class="titulo-principal">CONCEJO DELIBERANTE</h1>
                    <p class="subtitulo-principal">Sesión de Votación en Vivo</p>
                </div>
            </div>
            
            <div class="info-sesion">
                <div class="fecha-hora" id="fecha-hora-actual"></div>
                <div class="estado-sesion estado-<?= $sesion['estado'] ?>">
                    <i class="bi bi-<?= $sesion['estado'] === 'activa' ? 'play-circle' : ($sesion['estado'] === 'pausada' ? 'pause-circle' : 'stop-circle') ?>"></i>
                    <?= ucfirst($sesion['estado']) ?>
                </div>
            </div>
        </div>
    </header>

    <!-- DEBUG INFO -->
    <!-- 
    Sesión ID: <?= $sesion['id'] ?>
    Estado: <?= $sesion['estado'] ?>
    Puntos habilitados: <?= count($puntosHabilitados) ?>
    <?php if (!empty($puntosHabilitados)): ?>
        <?php foreach ($puntosHabilitados as $idx => $p): ?>
        Punto <?= $idx + 1 ?>: Orden=<?= $p['orden_punto'] ?>, Tipo=<?= $p['item_tipo'] ?>, ID=<?= $p['item_id'] ?>, Exp=<?= $p['numero_expediente'] ?>
        <?php endforeach; ?>
    <?php endif; ?>
    Presentes: <?= count($presentes) ?>
    Resultados cargados: <?= count($resultadosVotacion) ?>
    -->

    <!-- Contenido Principal -->
    <main class="main-grid">
        <!-- Sección de Votación -->
        <section class="votacion-section">
            <h2 class="section-title">
                <i class="bi bi-check-circle"></i>
                Votación en Curso
                <span style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 0.4rem 1.2rem; border-radius: 25px; margin-left: 1rem; font-size: clamp(12px, 1vw, 20px); animation: pulse-glow 2s infinite;">
                    <i class="bi bi-broadcast"></i> EN VIVO
                </span>
            </h2>
            <div class="section-content scroll-area">
                <?php if (empty($puntosHabilitados)): ?>
                    <div class="sin-votacion">
                        <i class="bi bi-hourglass-split"></i>
                        <h3>Esperando votación</h3>
                        <p>El punto que se está votando aparecerá aquí automáticamente</p>
                        <p style="margin-top: 1rem; font-size: 1.2rem; color: #6c757d;">
                            <i class="bi bi-info-circle"></i> 
                            Los puntos se ordenan automáticamente: primero el que se está votando, luego los ya votados
                        </p>
                    </div>
                <?php else: ?>
                    <?php 
                    $totalPresentes = count($presentes);
                    foreach ($puntosHabilitados as $index => $punto): 
                        $esActual = ($index === 0); // El primero es el actual (menos votos)
                        $totalVotosPunto = $punto['total_votos'] ?? 0;
                        $porcentaje = $totalPresentes > 0 ? round(($totalVotosPunto / $totalPresentes) * 100) : 0;
                    ?>
                        <div class="punto-votacion <?= $esActual ? 'votacion-activa' : '' ?>" data-punto-id="<?= $punto['id'] ?>" style="<?= $esActual ? '' : 'opacity: 0.85; border-left-color: #95a5a6;' ?>">
                            <div class="punto-numero">
                                <?= $esActual ? '� VOTANDO AHORA' : '✅ Votado' ?> - Punto <?= $punto['orden_punto'] ?>
                                <?php if ($totalPresentes > 0): ?>
                                    <span style="float: right; font-size: clamp(12px, 1vw, 18px); color: #6c757d;">
                                        <?= $totalVotosPunto ?>/<?= $totalPresentes ?> votos (<?= $porcentaje ?>%)
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="punto-expediente">
                                <?= htmlspecialchars($punto['numero_expediente']) ?>
                            </div>
                            <div class="punto-descripcion">
                                <?= htmlspecialchars($punto['extracto']) ?>
                            </div>
                            
                            <!-- Resultados de votación (para todos los tipos) -->
                            <?php if (isset($resultadosVotacion[$punto['id']])): ?>
                                <div class="resultados-grid">
                                    <div class="resultado-item resultado-positivo">
                                        <div class="resultado-numero"><?= $resultadosVotacion[$punto['id']]['positivo'] ?></div>
                                        <div class="resultado-label">✓ Afirmativos</div>
                                        <?php if (!empty($resultadosVotacion[$punto['id']]['votantes']['positivo'])): ?>
                                            <div class="resultado-votantes">
                                                <?php 
                                                $votantesPositivo = array_filter(array_map('trim', explode(',', $resultadosVotacion[$punto['id']]['votantes']['positivo'])));
                                                foreach ($votantesPositivo as $votante): 
                                                ?>
                                                    <span class="badge-votante badge-positivo"><?= htmlspecialchars($votante) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="resultado-item resultado-negativo">
                                        <div class="resultado-numero"><?= $resultadosVotacion[$punto['id']]['negativo'] ?></div>
                                        <div class="resultado-label">✕ Negativos</div>
                                        <?php if (!empty($resultadosVotacion[$punto['id']]['votantes']['negativo'])): ?>
                                            <div class="resultado-votantes">
                                                <?php 
                                                $votantesNegativo = array_filter(array_map('trim', explode(',', $resultadosVotacion[$punto['id']]['votantes']['negativo'])));
                                                foreach ($votantesNegativo as $votante): 
                                                ?>
                                                    <span class="badge-votante badge-negativo"><?= htmlspecialchars($votante) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="resultado-item resultado-abstencion">
                                        <div class="resultado-numero"><?= $resultadosVotacion[$punto['id']]['abstencion'] ?></div>
                                        <div class="resultado-label">⊗ Abstenciones</div>
                                        <?php if (!empty($resultadosVotacion[$punto['id']]['votantes']['abstencion'])): ?>
                                            <div class="resultado-votantes">
                                                <?php 
                                                $votantesAbstencion = array_filter(array_map('trim', explode(',', $resultadosVotacion[$punto['id']]['votantes']['abstencion'])));
                                                foreach ($votantesAbstencion as $votante): 
                                                ?>
                                                    <span class="badge-votante badge-abstencion"><?= htmlspecialchars($votante) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Sección Hemiciclo -->
        <section class="hemiciclo-section">
            <h2 class="section-title">
                <i class="bi bi-people"></i>
                Presentes 
                <span style="background: #28a745; color: white; padding: 0.3rem 1rem; border-radius: 20px; margin-left: 1rem; font-size: clamp(14px, 1.2vw, 24px);">
                    <?= count($presentes) ?>
                </span>
            </h2>
            <div class="section-content scroll-area">
                <div class="hemiciclo-grid">
                    <?php if (empty($presentes)): ?>
                        <div class="text-center text-muted" style="grid-column: 1 / -1;">
                            <i class="bi bi-people"></i> No hay editores presentes en la sesión
                        </div>
                    <?php else: ?>
                        <?php foreach ($presentes as $presente): ?>
                            <div class="miembro-hemiciclo miembro-presente">
                                <div class="miembro-avatar">
                                    <?= strtoupper(substr($presente['first_name'], 0, 1) . substr($presente['last_name'], 0, 1)) ?>
                                </div>
                                <div class="miembro-nombre">
                                    <?= htmlspecialchars($presente['first_name'] . ' ' . $presente['last_name']) ?>
                                </div>
                                <div class="miembro-estado presente">
                                    Presente
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Área para mociones (similar a la vista normal) -->
    <div id="area-mociones-pantalla-grande" class="area-mociones-pantalla-grande" style="display: none;">
        <!-- Las mociones aparecerán aquí -->
    </div>

    <!-- Footer -->
    <footer class="footer-pantalla-grande">
        <div class="footer-content">
            <div class="ultima-actualizacion">
                <i class="bi bi-arrow-clockwise"></i>
                Última actualización: <span id="ultima-actualizacion-tiempo"></span>
            </div>
            <div class="auto-refresh-info">
                <i class="bi bi-arrow-repeat"></i>
                Actualización automática cada 3 segundos
            </div>
        </div>
    </footer>

    </div> <!-- /#viewport-4k -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        const sesionId = <?= $sesion['id'] ?>;
        let ultimaMocionId = 0;
        
        // Actualizar fecha y hora
        function actualizarFechaHora() {
            const ahora = new Date();
            const opciones = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            
            document.getElementById('fecha-hora-actual').textContent = 
                ahora.toLocaleDateString('es-ES', opciones);
        }
        
        // Actualizar timestamp de última actualización
        function actualizarTimestamp() {
            const ahora = new Date();
            document.getElementById('ultima-actualizacion-tiempo').textContent = 
                ahora.toLocaleTimeString('es-ES');
        }
        
        // Verificar mociones nuevas
        function verificarMociones() {
            if (sesionId <= 0) return;
            
            fetch(`${BASE_URL}votacion/verificar-mociones/${sesionId}?desde=${ultimaMocionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.mociones && data.mociones.length > 0) {
                        const mocion = data.mociones[data.mociones.length - 1];
                        mostrarMocionPantallaGrande(mocion);
                        ultimaMocionId = Math.max(ultimaMocionId, mocion.id);
                    }
                })
                .catch(error => {
                    console.error('Error al verificar mociones:', error);
                });
        }
        
        // Mostrar moción optimizada para pantalla grande
        function mostrarMocionPantallaGrande(mocion) {
            const fechaFormateada = new Date(mocion.fecha_creacion).toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            // Crear elemento de moción
            const areaMociones = document.getElementById('area-mociones-pantalla-grande');
            areaMociones.innerHTML = `
                <div class="mocion-alert-grande">
                    <div class="d-flex align-items-center">
                        <div class="mocion-icon">
                            <i class="bi bi-megaphone"></i>
                        </div>
                        <div class="mocion-content flex-grow-1">
                            <h2>📢 MOCIÓN SOLICITADA</h2>
                            <div class="mocion-texto">
                                <strong>${mocion.tipo_texto}:</strong> ${mocion.texto}
                            </div>
                            <div class="mocion-autor">
                                <i class="bi bi-person"></i> ${mocion.autor_nombre} - 
                                <i class="bi bi-clock"></i> ${fechaFormateada}
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning btn-lg" onclick="cerrarMocionPantallaGrande()" style="margin-left: 2rem;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            `;
            
            // Mostrar moción
            areaMociones.style.display = 'block';
            
            // Auto-ocultar después de 8 segundos (más tiempo para pantalla grande)
            setTimeout(() => {
                cerrarMocionPantallaGrande();
            }, 8000);
        }
        
        // Cerrar moción
        function cerrarMocionPantallaGrande() {
            const areaMociones = document.getElementById('area-mociones-pantalla-grande');
            areaMociones.style.display = 'none';
        }
        
        // Actualizar contenido completo
        function actualizarContenido() {
            location.reload();
        }
        
        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            // Actualizar fecha y hora inmediatamente
            actualizarFechaHora();
            actualizarTimestamp();
            
            // Configurar intervalos - Actualización más frecuente
            setInterval(actualizarFechaHora, 1000);     // Cada 1 segundo (reloj)
            setInterval(verificarMociones, 2000);       // Cada 2 segundos (mociones)
            setInterval(actualizarContenido, 3000);     // Cada 3 segundos (votaciones y presentes)
            setInterval(actualizarTimestamp, 1000);     // Cada 1 segundo (timestamp)
            
            console.log('Vista pantalla grande iniciada - Actualización cada 3 segundos');
        });
    </script>
</body>
</html>