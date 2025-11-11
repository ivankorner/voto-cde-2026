-- Fix para agregar columna total_presentes a historial_votacion si no existe
-- Fecha: 29-Oct-2025

-- Agregar la columna total_presentes si no existe
ALTER TABLE historial_votacion 
ADD COLUMN IF NOT EXISTS total_presentes INT(11) NOT NULL DEFAULT 0 AFTER item_votacion_id;

-- Verificar la estructura
DESCRIBE historial_votacion;
