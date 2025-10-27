<?php
/**
 * Script de limpieza de puestos para servidor DonWeb
 * Ejecutar una sola vez visitando: https://votocde.online/limpiar_puestos_donweb.php
 */

// Configuración de base de datos DonWeb
$host = 'localhost';
$dbname = 'a0020819_votocde';
$username = 'a0020819_votocde';
$password = 'revu06weRI';

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Limpieza de Puestos - VotoCDE</h1>";
    echo "<p><strong>Servidor:</strong> DonWeb MySQL</p>";
    echo "<hr>";
    
    // 1. Mostrar estado ANTES de limpiar
    echo "<h2>📊 Estado ANTES de la limpieza:</h2>";
    $stmt = $pdo->query("SELECT id, username, CONCAT('[', IFNULL(puesto, 'NULL'), ']') as puesto, first_name, last_name FROM users WHERE puesto IS NOT NULL AND puesto != ''");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Puesto (con espacios)</th><th>Nombre</th><th>Apellido</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['username']}</td>";
        echo "<td style='background: #fff3cd;'>{$row['puesto']}</td>";
        echo "<td>{$row['first_name']}</td>";
        echo "<td>{$row['last_name']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h2>🔧 Ejecutando limpieza...</h2>";
    
    // 2. Limpiar saltos de línea y espacios
    $cleaned = $pdo->exec("UPDATE users SET puesto = TRIM(REPLACE(REPLACE(REPLACE(puesto, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) WHERE puesto IS NOT NULL");
    echo "✅ Paso 1: Limpiados saltos de línea y espacios extras en $cleaned registros<br>";
    
    // 3. Normalizar Presidente
    $presidente = $pdo->exec("UPDATE users SET puesto = 'Presidente' WHERE puesto LIKE '%Presidente%' AND puesto NOT LIKE '%Vice%'");
    echo "✅ Paso 2: Normalizados $presidente registros a 'Presidente'<br>";
    
    // 4. Normalizar Vice Presidente
    $vice = $pdo->exec("UPDATE users SET puesto = 'Vice Presidente' WHERE puesto LIKE '%Vice%'");
    echo "✅ Paso 3: Normalizados $vice registros a 'Vice Presidente'<br>";
    
    // 5. Normalizar Concejal
    $concejal = $pdo->exec("UPDATE users SET puesto = 'Concejal' WHERE puesto LIKE '%Concejal%'");
    echo "✅ Paso 4: Normalizados $concejal registros a 'Concejal'<br>";
    
    // 6. Limpiar valores vacíos
    $empty = $pdo->exec("UPDATE users SET puesto = NULL WHERE puesto = '' OR puesto = ' '");
    echo "✅ Paso 5: Limpiados $empty registros vacíos (convertidos a NULL)<br>";
    
    echo "<hr>";
    
    // 7. Mostrar estado DESPUÉS de limpiar
    echo "<h2>✅ Estado DESPUÉS de la limpieza:</h2>";
    $stmt = $pdo->query("SELECT id, username, IFNULL(puesto, '[Sin puesto]') as puesto, first_name, last_name FROM users ORDER BY CASE puesto WHEN 'Presidente' THEN 1 WHEN 'Vice Presidente' THEN 2 WHEN 'Concejal' THEN 3 ELSE 6 END");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Puesto (limpio)</th><th>Nombre</th><th>Apellido</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $bgColor = '#d4edda'; // Verde por defecto
        if ($row['puesto'] === 'Presidente') $bgColor = '#cce5ff';
        if ($row['puesto'] === 'Vice Presidente') $bgColor = '#e7f3ff';
        if ($row['puesto'] === '[Sin puesto]') $bgColor = '#f8d7da';
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['username']}</td>";
        echo "<td style='background: $bgColor;'><strong>{$row['puesto']}</strong></td>";
        echo "<td>{$row['first_name']}</td>";
        echo "<td>{$row['last_name']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h2>🎉 Limpieza completada exitosamente!</h2>";
    echo "<p><strong>Próximos pasos:</strong></p>";
    echo "<ol>";
    echo "<li>Verificar que los puestos se muestren correctamente arriba</li>";
    echo "<li>Ir a la Vista Pública de una sesión activa</li>";
    echo "<li>Verificar que el hemiciclo muestre correctamente al Presidente en posición #1</li>";
    echo "<li>Eliminar este archivo por seguridad: <code>rm limpiar_puestos_donweb.php</code></li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error de conexión</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Verifica las credenciales de la base de datos en el script.</p>";
}
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #f5f5f5;
}
table {
    width: 100%;
    background: white;
    border-collapse: collapse;
    margin: 20px 0;
}
th {
    background: #007bff;
    color: white;
    padding: 10px;
}
td {
    padding: 8px;
}
h1 {
    color: #007bff;
}
h2 {
    color: #28a745;
    margin-top: 30px;
}
hr {
    margin: 30px 0;
    border: none;
    border-top: 2px solid #dee2e6;
}
</style>