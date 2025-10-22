<?php
// Script para crear los ítems estándar de una orden del día
require_once 'config/database.php';

function crearItemsEstandar($ordenDiaId) {
    global $host, $database, $username, $password;
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $items = [
            [1, 'IZAMIENTO DEL PABELLÓN NACIONAL', 'protocolo'],
            [2, 'LECTURA ORDEN DEL DIA', 'protocolo'],
            [3, 'LECTURA Y CONSIDERACIÓN DE ACTAS', 'lectura_actas'],
            [4, 'EXPEDIENTES INGRESADO FUERA DE TÉRMINO', 'expedientes_fuera_termino'],
            [5, 'TRATAMIENTOS: SOBRE TABLAS, DE PREFERENCIA Y/O DE RECONSIDERACIÓN', 'tratamientos'],
            [6, 'PROYECTOS PRESENTADOS POR LAS Y LOS CONCEJALES', 'proyectos_concejales'],
            [7, 'PROYECTOS PRESENTADOS POR EL PODER EJECUTIVO MUNICIPAL', 'proyectos_ejecutivo'],
            [8, 'NOTAS DEL PODER EJECUTIVO', 'notas_ejecutivo'],
            [9, 'NOTAS DE ASUNTOS OFICIALES', 'notas_oficiales'],
            [10, 'NOTAS DE ASUNTOS PARTICULARES', 'notas_particulares'],
            [11, 'ESPACIO CIUDADANO', 'espacio_ciudadano'],
            [12, 'DICTAMENES DE COMISIONES', 'dictamenes_comisiones'],
            [13, 'HOMENAJES', 'homenajes'],
            [14, 'TEMAS INTERNOS', 'temas_internos']
        ];
        
        $sql = "INSERT INTO orden_dia_items (orden_dia_id, numero_orden, titulo, tipo_item) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($items as $item) {
            $stmt->execute([$ordenDiaId, $item[0], $item[1], $item[2]]);
        }
        
        return true;
        
    } catch(PDOException $e) {
        error_log("Error creando ítems estándar: " . $e->getMessage());
        return false;
    }
}
?>