-- =====================================================
-- LIMPIEZA RÁPIDA DE PUESTOS - DonWeb MySQL Compatible
-- =====================================================
-- Copiar y pegar estas queries UNA POR UNA en phpMyAdmin

-- 1. VER PROBLEMA ACTUAL
SELECT id, username, CONCAT('[', puesto, ']') as puesto_con_espacios, first_name, last_name 
FROM users 
WHERE puesto IS NOT NULL AND puesto != '';

-- 2. LIMPIAR SALTOS DE LÍNEA Y ESPACIOS
UPDATE users 
SET puesto = TRIM(REPLACE(REPLACE(REPLACE(puesto, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) 
WHERE puesto IS NOT NULL;

-- 3. NORMALIZAR PRESIDENTE
UPDATE users SET puesto = 'Presidente' 
WHERE puesto LIKE '%Presidente%' AND puesto NOT LIKE '%Vice%';

-- 4. NORMALIZAR VICE PRESIDENTE
UPDATE users SET puesto = 'Vice Presidente' 
WHERE puesto LIKE '%Vice%';

-- 5. NORMALIZAR CONCEJAL
UPDATE users SET puesto = 'Concejal' 
WHERE puesto LIKE '%Concejal%';

-- 6. LIMPIAR VACÍOS
UPDATE users SET puesto = NULL WHERE puesto = '' OR puesto = ' ';

-- 7. VERIFICAR RESULTADO FINAL
SELECT id, username, puesto, first_name, last_name, role_id
FROM users 
WHERE puesto IS NOT NULL
ORDER BY 
    CASE puesto
        WHEN 'Presidente' THEN 1
        WHEN 'Vice Presidente' THEN 2
        WHEN 'Concejal' THEN 3
        WHEN 'Secretario' THEN 4
        WHEN 'Pro Secretario' THEN 5
        ELSE 6
    END;