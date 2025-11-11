<?php
/**
 * Herramienta de Diagnóstico para Votación
 * Crear este archivo en el root del proyecto y acceder vía navegador
 * URL: http://tu-dominio.com/debug_votacion.php
 */

// Configuración básica
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Verificar que esté logueado como admin
session_start();

echo "<h1>🔍 Diagnóstico de Votación</h1>";
echo "<style>body{font-family:Arial;} .error{color:red;} .success{color:green;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;}</style>";

if (!isset($_SESSION['user_id'])) {
    echo "<p class='error'>❌ Debes estar logueado para usar esta herramienta</p>";
    echo "<a href='index.php'>Ir al login</a>";
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✅ Conexión a base de datos OK</p>";
    
    // Verificar versión de MySQL
    $stmt = $db->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "<p class='info'>📊 Versión MySQL: {$version['version']}</p>";
    
    // Verificar base de datos actual
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $dbName = $stmt->fetch();
    echo "<p class='info'>🗄️ Base de datos actual: {$dbName['db_name']}</p>";
    
    // 1. Verificar estructura de tablas críticas
    echo "<h2>📋 Verificación de Tablas</h2>";
    
    $tablas = ['votos', 'puntos_habilitados', 'presentes_sesion', 'sesiones_votacion'];
    foreach ($tablas as $tabla) {
        // Usar consulta directa sin parámetros preparados para SHOW TABLES
        $stmt = $db->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt && $stmt->fetch()) {
            echo "<p class='success'>✅ Tabla '$tabla' existe</p>";
            
            // Mostrar estructura
            try {
                $stmt = $db->query("DESCRIBE `$tabla`");
                if ($stmt) {
                    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo "<details><summary>Ver estructura de $tabla</summary><pre>";
                    print_r($cols);
                    echo "</pre></details>";
                } else {
                    echo "<p class='error'>⚠️ No se pudo obtener estructura de '$tabla'</p>";
                }
            } catch (Exception $e) {
                echo "<p class='error'>⚠️ Error al obtener estructura de '$tabla': " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='error'>❌ Tabla '$tabla' NO existe</p>";
        }
    }
    
    // 2. Verificar sesiones activas
    echo "<h2>🎯 Sesiones Activas</h2>";
    try {
        $stmt = $db->prepare("SELECT id, nombre, estado FROM sesiones_votacion WHERE estado = 'activa'");
        $stmt->execute();
        $sesiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($sesiones)) {
            echo "<p class='error'>❌ No hay sesiones activas</p>";
            
            // Mostrar todas las sesiones para debug
            $stmt = $db->prepare("SELECT id, nombre, estado FROM sesiones_votacion ORDER BY id DESC LIMIT 5");
            $stmt->execute();
            $todasSesiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<details><summary>Últimas 5 sesiones (cualquier estado)</summary><pre>";
            print_r($todasSesiones);
            echo "</pre></details>";
        } else {
            foreach ($sesiones as $sesion) {
                echo "<p class='info'>📌 Sesión {$sesion['id']}: {$sesion['nombre']} (Estado: {$sesion['estado']})</p>";
                
                try {
                    // Verificar puntos habilitados en esta sesión
                    $stmt = $db->prepare("SELECT * FROM puntos_habilitados WHERE sesion_id = ? AND habilitado = 1");
                    $stmt->execute([$sesion['id']]);
                    $puntos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo "<details><summary>Puntos habilitados en sesión {$sesion['id']}</summary><pre>";
                    print_r($puntos);
                    echo "</pre></details>";
                } catch (Exception $e) {
                    echo "<p class='error'>⚠️ Error al obtener puntos habilitados: " . $e->getMessage() . "</p>";
                }
                
                try {
                    // Verificar presentes en esta sesión
                    $stmt = $db->prepare("SELECT ps.*, u.first_name, u.last_name FROM presentes_sesion ps JOIN users u ON ps.user_id = u.id WHERE ps.sesion_id = ? AND ps.presente = 1");
                    $stmt->execute([$sesion['id']]);
                    $presentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo "<details><summary>Usuarios presentes en sesión {$sesion['id']}</summary><pre>";
                    print_r($presentes);
                    echo "</pre></details>";
                } catch (Exception $e) {
                    echo "<p class='error'>⚠️ Error al obtener usuarios presentes: " . $e->getMessage() . "</p>";
                }
            }
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error al verificar sesiones: " . $e->getMessage() . "</p>";
    }
    
    // 3. Test de votación simulado
    echo "<h2>🧪 Test de Votación Simulado</h2>";
    
    // Usar cualquier sesión disponible, no solo las activas
    try {
        $stmt = $db->prepare("SELECT id, nombre, estado FROM sesiones_votacion ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $sesionTest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$sesionTest) {
            echo "<p class='error'>❌ No hay ninguna sesión para hacer el test</p>";
        } else {
            $sesionId = $sesionTest['id'];
            $userId = $_SESSION['user_id'];
            
            echo "<p class='info'>Probando con sesión ID: $sesionId ({$sesionTest['nombre']}, estado: {$sesionTest['estado']}), Usuario ID: $userId</p>";
            
            // Cargar modelo de votación
            try {
                require_once __DIR__ . '/app/models/Votacion.php';
                $votacionModel = new Votacion();
                echo "<p class='success'>✅ Modelo Votacion cargado correctamente</p>";
            } catch (Exception $e) {
                echo "<p class='error'>❌ Error al cargar modelo Votacion: " . $e->getMessage() . "</p>";
                throw $e;
            }
        
        // Test 1: ¿Puede votar?
        $puedeVotar = $votacionModel->puedeVotar($userId);
        echo "<p>" . ($puedeVotar ? "✅" : "❌") . " Usuario puede votar: " . ($puedeVotar ? "SÍ" : "NO") . "</p>";
        
        // Test 2: ¿Está presente?
        $presentes = $votacionModel->getPresentesSesion($sesionId);
        $estaPresente = false;
        foreach ($presentes as $presente) {
            if ($presente['user_id'] == $userId) {
                $estaPresente = true;
                break;
            }
        }
        echo "<p>" . ($estaPresente ? "✅" : "❌") . " Usuario está presente: " . ($estaPresente ? "SÍ" : "NO") . "</p>";
        
        // Test 3: ¿Punto de actas habilitado?
        $actasHabilitado = $votacionModel->isPuntoHabilitado($sesionId, 'actas', 0);
        echo "<p>" . ($actasHabilitado ? "✅" : "❌") . " Punto 'actas' habilitado: " . ($actasHabilitado ? "SÍ" : "NO") . "</p>";
        
        // Test 4: ¿Ya votó actas?
        $yaVoto = $votacionModel->yaVoto($sesionId, $userId, 'actas', 0);
        echo "<p>" . ($yaVoto ? "❌" : "✅") . " Ya votó actas: " . ($yaVoto ? "SÍ" : "NO") . "</p>";
        
        // Test 5: Simular registro de voto (SIN EJECUTAR)
        echo "<h3>🔍 Datos que se enviarían al registrar voto:</h3>";
        $datosVoto = [
            'sesion_id' => $sesionId,
            'user_id' => $userId,
            'item_votacion_tipo' => 'actas',
            'item_votacion_id' => 0,
            'numero_expediente' => '',
            'extracto_expediente' => '',
            'tipo_voto' => 'positivo',
            'observaciones' => 'Test de diagnóstico',
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];
        echo "<pre>";
        print_r($datosVoto);
        echo "</pre>";
        
        // Test 6: Verificar consulta yaVoto manual
        echo "<h3>🔍 Test manual de yaVoto:</h3>";
        try {
            $query = "SELECT COUNT(*) as total FROM votos WHERE sesion_id = ? AND user_id = ? AND item_votacion_tipo = ? AND item_votacion_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$sesionId, $userId, 'actas', 0]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p class='info'>Resultado consulta directa: {$result['total']} votos encontrados</p>";
            
            // Mostrar votos existentes del usuario en esta sesión
            $query = "SELECT * FROM votos WHERE sesion_id = ? AND user_id = ? ORDER BY fecha_voto DESC";
            $stmt = $db->prepare($query);
            $stmt->execute([$sesionId, $userId]);
            $votosExistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<details><summary>Votos existentes del usuario en esta sesión</summary><pre>";
            print_r($votosExistentes);
            echo "</pre></details>";
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error en test manual: " . $e->getMessage() . "</p>";
        }
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error en test de votación: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error general: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><p><small>Diagnóstico completado a las " . date('Y-m-d H:i:s') . "</small></p>";
?>