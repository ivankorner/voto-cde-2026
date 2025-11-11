<?php
/**
 * Script de migración para agregar columna total_presentes a historial_votacion
 * Ejecutar UNA SOLA VEZ en el servidor de producción
 */

require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== MIGRACIÓN: Agregar total_presentes a historial_votacion ===\n\n";
    
    // Verificar si la columna ya existe
    $checkQuery = "SHOW COLUMNS FROM historial_votacion LIKE 'total_presentes'";
    $stmt = $db->query($checkQuery);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($exists) {
        echo "✓ La columna 'total_presentes' ya existe en historial_votacion\n";
        echo "No se requiere migración.\n";
        exit(0);
    }
    
    echo "➤ La columna 'total_presentes' NO existe. Agregando...\n";
    
    // Agregar la columna
    $alterQuery = "ALTER TABLE historial_votacion 
                   ADD COLUMN total_presentes INT(11) NOT NULL DEFAULT 0 
                   AFTER item_votacion_id";
    
    $db->exec($alterQuery);
    
    echo "✓ Columna 'total_presentes' agregada exitosamente\n\n";
    
    // Verificar la estructura final
    echo "=== Estructura actual de historial_votacion ===\n";
    $describeQuery = "DESCRIBE historial_votacion";
    $stmt = $db->query($describeQuery);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo sprintf("%-25s %-20s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'] === 'NO' ? 'NOT NULL' : 'NULL'
        );
    }
    
    echo "\n✓ Migración completada con éxito!\n";
    
} catch (PDOException $e) {
    echo "✗ ERROR en migración: " . $e->getMessage() . "\n";
    exit(1);
}
